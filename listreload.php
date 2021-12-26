<?php

  include "includes/includes.php";
  include "includes/constants.php";
  header("Content-Type: text/html; charset=utf-8");
  header('Cache-Control: no-cache, no-store, max-age=0, must-revalidate');
  header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
  header('Pragma: no-cache');
  $spielID = (int)$_GET['game'];
  $id = (int)$_GET['id'];

  $trennzeichen = "$"; //Das Zeichen, auf das im Skript responded wird

  //Reloaded eine Liste, erfordert komplexeren Code als reload.php
  //Die Liste, die reloaded wird ist unterschiedlich, je nach Spielphase und eigenem Charakter
  //Wenn reload gesetzt ist, wird außerdem geschaut, ob ich reloaden muss!
  $ichLebe = false;
  $ichLebeQ = $mysqli->Query("SELECT * FROM $spielID"."_spieler WHERE id = $id");
  $ichLebeQ = $ichLebeQ->fetch_assoc();
  if (isset($_GET['reload']))
  {
    if ($ichLebeQ['reload'] == 1 && $_GET['reload'] == 1)
    {
      echo "1";
      die;
    }
    else
    {
      echo "0";
    }
  }
  if ($ichLebeQ['lebt'] == 1)
    $ichLebe = true;


  $spielRes = $mysqli->Query("SELECT * FROM $spielID"."_game");
  $spielAss = $spielRes->fetch_assoc();
  $phase = $spielAss['spielphase'];

  $text = "";
  if ($ichLebe || $phase <= PHASESPIELSETUP || $phase >= PHASESIEGEREHRUNG)
  {
    //Schaue nach, ob Antwort bereits gespeichert ist!
    if (array_key_exists("list_lebe",$spielAss)) //Für Backward compatibility!
      {
        if ($phase != PHASENACHT3 && $spielAss['list_lebe_aktualisiert'] > (microtime(true)*1000 - _LISTMAXRELOADTIME)) //Bei Werwölfen nicht die Liste schicken!
        {
            //Sende vorgefertigte Antwort!
            echo $spielAss['list_lebe'];
            die;
        }
      }
    if ($phase == PHASESETUP)
    {
      //spielersuchen-Phase
      //Gib jeden Spieler als String zurück, farbe alle schwarz = 0
      //Bei Spieler 0 schreibe (Spielleiter) dazu
      $spielerRes = $mysqli->Query("SELECT * FROM $spielID"."_spieler");
      while ($temp = $spielerRes->fetch_assoc())
      {
        if ($temp['spielleiter']==1)
        {
          $text.= $trennzeichen.$temp['name']." (Spielleiter)$trennzeichen"."0";
        }
        else
        {
          $text.= $trennzeichen.$temp['name'].$trennzeichen."0";
        }
      }
    }
    elseif ($phase == PHASESPIELSETUP)
    {
      //Alle werden in einer Liste angezeigt, die bereit sind grün
      $spielerRes = $mysqli->Query("SELECT * FROM $spielID"."_spieler");
      while ($temp = $spielerRes->fetch_assoc())
      {
        if ($temp['spielleiter']==1)
        {
          $text.= $trennzeichen.$temp['name']." (Spielleiter)$trennzeichen"."1";
        }
        else
        {
          if ($temp['bereit']==1)
            $text.= $trennzeichen.$temp['name'].$trennzeichen."1";
          else
            $text.= $trennzeichen.$temp['name'].$trennzeichen."0";
        }
      }
    }
    elseif ($phase == PHASENACHT3) //Nur für Werwölfe, daher nicht in Liste schreiben!
    {
      //Schaue nach, ob ich Werwolf bin
      $myRes = $mysqli->Query("SELECT * FROM $spielID"."_spieler WHERE id = $id");
      $myAssoc = $myRes->fetch_assoc();
      if ($myAssoc['nachtIdentitaet'] == CHARWERWOLF || $myAssoc['nachtIdentitaet'] == CHARURWOLF)
      {
        //Ich bin Werwolf --> Liste der (lebenden) Werwölfe
        $spielerRes = $mysqli->Query("SELECT * FROM $spielID"."_spieler");
        while ($temp = $spielerRes->fetch_assoc())
        {

          if (($temp['nachtIdentitaet']==CHARWERWOLF || $temp['nachtIdentitaet']==CHARURWOLF) && $temp['lebt']==1)
          {
            if ($temp['wahlAuf']==-1)
              echo $trennzeichen.$temp['name']." (wach)".$trennzeichen."0";
            else
            {
              //Finde Namen heraus von wahlAuf
              $nameRes = $mysqli->Query("SELECT * FROM $spielID"."_spieler WHERE id = ".$temp['wahlAuf']);
              $nameAssoc = $nameRes->fetch_assoc();
              echo $trennzeichen.$temp['name']." (wach): ".$nameAssoc['name'].$trennzeichen."1";
            }
          }

        }
      }
      die;
    }
    elseif ($phase == PHASEBUERGERMEISTERWAHL)
    {
      //Bürgermeisterwahl
      //ähnlich wie bei den Werwölfen
      $spielerRes = $mysqli->Query("SELECT * FROM $spielID"."_spieler");
      while ($temp = $spielerRes->fetch_assoc())
      {
        if ($temp['lebt']==1)
        {
          if ($temp['wahlAuf']==-1)
            $text.= $trennzeichen.$temp['name']." (noch nicht Abgestimmt)".$trennzeichen."0";
          else
          {
            //Finde Namen heraus von wahlAuf
            $nameRes = $mysqli->Query("SELECT * FROM $spielID"."_spieler WHERE id = ".$temp['wahlAuf']);
            $nameAssoc = $nameRes->fetch_assoc();
            $text.= $trennzeichen.$temp['name'].": ".$nameAssoc['name'].$trennzeichen."1";
          }
        }
      }
    }
    elseif ($phase == PHASEANKLAGEN)
    {
      //Anklagen
      $spielerRes = $mysqli->Query("SELECT * FROM $spielID"."_spieler WHERE angeklagtVon > -1 AND lebt = 1");
      while ($temp = $spielerRes->fetch_assoc())
      {
          //Finde Namen heraus von angeklagtVon
        $nameRes = $mysqli->Query("SELECT * FROM $spielID"."_spieler WHERE id = ".$temp['angeklagtVon']);
        $nameAssoc = $nameRes->fetch_assoc();
        $text.= $trennzeichen.$temp['name']." (angeklagt von ".$nameAssoc['name'].")".$trennzeichen."1";
      }
    }
    elseif ($phase == PHASEABSTIMMUNG)
    {
      //Abstimmung
      //Zeige zuerst die Angeklagten an
      $spielerRes = $mysqli->Query("SELECT * FROM $spielID"."_spieler WHERE lebt = 1 AND angeklagtVon > -1");
      while ($temp = $spielerRes->fetch_assoc())
      {
        //Finde heraus, wieviele Stimmen
        $stimmenRes = $mysqli->Query("SELECT * FROM $spielID"."_spieler WHERE lebt = 1 AND wahlAuf = ".$temp['id']);
        //Finde heraus, ob Bürgermeister auf ihn gestimmt hat
        $buergermeisterRes = $mysqli->Query("SELECT * FROM $spielID"."_spieler WHERE lebt = 1 AND wahlAuf = ".$temp['id']." AND buergermeister = 1");
        $buergermeisterText = ""; //Zeige nichts an, wenn der Bürgermeister ihn nicht ausgewählt hat
        if ($buergermeisterRes->num_rows > 0)
          $buergermeisterText = " + Stimme des Bürgermeisters";
        $text.= $trennzeichen.$temp['name'].", normale Stimmen: ".$stimmenRes->num_rows.$buergermeisterText.$trennzeichen."2";
      }

      //Dann zeige an, wer für wen gestimmt hat
      $spielerRes2 = $mysqli->Query("SELECT * FROM $spielID"."_spieler WHERE lebt = 1");
      while ($temp = $spielerRes2->fetch_assoc())
      {
        if ($temp['wahlAuf']==-1)
          $text.= $trennzeichen.$temp['name']." (noch nicht Abgestimmt)".$trennzeichen."0";
        else
        {
          //Finde Namen heraus von wahlAuf
          $nameRes = $mysqli->Query("SELECT * FROM $spielID"."_spieler WHERE id = ".$temp['wahlAuf']);
          $nameAssoc = $nameRes->fetch_assoc();
          $text.= $trennzeichen.$temp['name'].": ".$nameAssoc['name'].$trennzeichen."1";
        }
      }
    }
    elseif ($phase == PHASESTICHWAHL)
    {
      //Stichwahl = im Prinzip dasselbe wie in Phase 11
      //Zeige zuerst die Angeklagten an
      $spielerRes = $mysqli->Query("SELECT * FROM $spielID"."_spieler WHERE lebt = 1 AND angeklagtVon > -1");
      while ($temp = $spielerRes->fetch_assoc())
      {
        //Finde heraus, wieviele Stimmen
        $stimmenRes = $mysqli->Query("SELECT * FROM $spielID"."_spieler WHERE lebt = 1 AND wahlAuf = ".$temp['id']);
        //Finde heraus, ob Bürgermeister auf ihn gestimmt hat
        $buergermeisterRes = $mysqli->Query("SELECT * FROM $spielID"."_spieler WHERE lebt = 1 AND wahlAuf = ".$temp['id']." AND buergermeister = 1");
        $buergermeisterText = ""; //Zeige nichts an, wenn der Bürgermeister ihn nicht ausgewählt hat
        if ($buergermeisterRes->num_rows > 0)
          $buergermeisterText = " + Stimme des Bürgermeisters";
        $text.= $trennzeichen.$temp['name'].", normale Stimmen: ".$stimmenRes->num_rows.$buergermeisterText.$trennzeichen."2";
      }

      //Dann zeige an, wer für wen gestimmt hat
      $spielerRes2 = $mysqli->Query("SELECT * FROM $spielID"."_spieler WHERE lebt = 1");
      while ($temp = $spielerRes2->fetch_assoc())
      {
        if ($temp['wahlAuf']==-1)
          $text.= $trennzeichen.$temp['name']." (noch nicht Abgestimmt)".$trennzeichen."0";
        else
        {
          //Finde Namen heraus von wahlAuf
          $nameRes = $mysqli->Query("SELECT * FROM $spielID"."_spieler WHERE id = ".$temp['wahlAuf']);
          $nameAssoc = $nameRes->fetch_assoc();
          $text.= $trennzeichen.$temp['name'].": ".$nameAssoc['name'].$trennzeichen."1";
        }
      }
    }
    $mysqli->Query("UPDATE $spielID"."_game SET `list_lebe` = '$text', `list_lebe_aktualisiert` = ". (int)(microtime(true)*1000));
    echo $text;
  }
  else
  {
    if (array_key_exists("list_tot",$spielAss)) //Für Backward compatibility!
    {
      if ($spielAss['list_tot_aktualisiert'] > (microtime(true)*1000 - _LISTMAXRELOADTIME)) //Bei Werwölfen nicht die Liste schicken!
      {
          //Sende vorgefertigte Antwort!
          echo $spielAss['list_tot'];
          die;
      }
    }
    $rueckgabe = "";
    //Zuerst "verkünden", welche Phase wir haben
    $text = "";
    switch ($phase)
    {
      case PHASENACHT3:
        $text = "Nacht (Teil 1)";
        break;
      case PHASENACHT4:
        $text = "Nacht (Teil 2)";
        break;
      case PHASENACHT5:
        $text = "Nacht (Teil 3)";
        break;
      case PHASETOTEBEKANNTGEBEN:
        $text = "Morgen";
        break;
      case PHASEBUERGERMEISTERWAHL:
        $text = "Bürgermeisterwahl";
        break;
      case PHASEDISKUSSION:
        $text = "Diskussion";
        break;
      case PHASEANKLAGEN:
        $text = "Anklagen";
        break;
      case PHASEABSTIMMUNG:
        $text = "Abstimmung";
        break;
      case PHASESTICHWAHL:
        $text = "Stichwahl";
        break;
    }
    $rueckgabe.= $trennzeichen.$text.$trennzeichen."3";

    $spielerRes = $mysqli->Query("SELECT * FROM $spielID"."_spieler WHERE lebt = 1");
      while ($temp = $spielerRes->fetch_assoc())
      {
    $identitaet = "";
      switch ($temp['nachtIdentitaet'])
      {
        case CHARDORFBEWOHNER:
          $identitaet = "Dorfbewohner";
          break;
        case CHARWERWOLF:
          $identitaet = "Werwolf";
          break;
        case CHARSEHER:
          $identitaet = "Seher/in";
          break;
        case CHARHEXE:
          $identitaet = "Hexe/r";
          break;
        case CHARJAEGER:
          $identitaet = "Jäger/in";
          break;
        case CHARAMOR:
          $identitaet = "Amor";
          break;
        case CHARBESCHUETZER:
          $identitaet = "Beschützer/in";
          break;
        case CHARPARERM:
          $identitaet = "Paranormale(r) Ermittler/in";
          break;
        case CHARLYKANTROPH:
          $identitaet = "Lykantroph/in";
          break;
        case CHARSPION:
          $identitaet = "Spion/in";
          break;
        case CHARMORDLUSTIGER:
          $identitaet = "Mordlustige(r)";
          break;
        case CHARPAZIFIST:
          $identitaet = "Pazifist/in";
          break;
        case CHARALTERMANN:
          $identitaet = "Alte(r)";
          break;
        case CHARURWOLF:
          $identitaet = "Urwolf/Urwölfin";
          break;
      }
  //Eine Liste aller aktiver Spieler anzeigen
  //zuerst alle Lebenden anzeigen
      if (($phase == PHASENACHT3 && ($temp['nachtIdentitaet'] == CHARWERWOLF || $temp['nachtIdentitaet'] == CHARURWOLF)) || $phase == PHASEBUERGERMEISTERWAHL || $phase == PHASEDISKUSSION || $phase == PHASEANKLAGEN || $phase == PHASEABSTIMMUNG) //In diesen Phasen können die Werwölfe abstimmen
      {
        if ($temp['wahlAuf']==-1)
          $rueckgabe.= $trennzeichen.$temp['name']." ($identitaet, wach)".$trennzeichen."0";
        else
        {
          //Finde Namen heraus von wahlAuf
          $nameRes = $mysqli->Query("SELECT * FROM $spielID"."_spieler WHERE id = ".$temp['wahlAuf']);
          $nameAssoc = $nameRes->fetch_assoc();
          $rueckgabe.= $trennzeichen.$temp['name']." ($identitaet, wach): ".$nameAssoc['name'].$trennzeichen."1";
        }
      }
      elseif ($phase >= PHASENACHTENDE || ($phase == PHASENACHT1 && $temp['nachtIdentitaet']==CHARAMOR)||($phase == PHASENACHT3 && ($temp['nachtIdentitaet']==CHARSEHER || $temp['nachtIdentitaet']==CHARBESCHUETZER || $temp['nachtIdentitaet']==CHARPARERM || $temp['nachtIdentitaet']==CHARSPION)) || ($phase == PHASENACHT4 && $temp['nachtIdentitaet']==CHARHEXE))
      {
        //ist wach
        if ($temp['nachtIdentitaet']==CHARHEXE)
        {
          //Zeige Tränke
          $heiltrank = $temp['hexeHeiltraenke'];
          $todestrank = $temp['hexeTodestraenke'];
          $rueckgabe.= $trennzeichen.$temp['name']." ($identitaet, wach, Heiltränke: $heiltrank, Todestränke: $todestrank)".$trennzeichen."0";
        }
        else
        {
          $rueckgabe.= $trennzeichen.$temp['name']." ($identitaet, wach)".$trennzeichen."0";
        }
      }
      else
      {
        //nicht wach
        if ($temp['nachtIdentitaet']==CHARHEXE)
        {
          //Zeige Tränke
          $heiltrank = $temp['hexeHeiltraenke'];
          $todestrank = $temp['hexeTodestraenke'];
          $rueckgabe.= $trennzeichen.$temp['name']." ($identitaet, Heiltränke: $heiltrank, Todestränke: $todestrank)".$trennzeichen."0";
        }
        else
        {
          $rueckgabe.= $trennzeichen.$temp['name']." ($identitaet)".$trennzeichen."0";
        }

      }
    }
    //Dann alle Toten anzeigen
    $spielerRes = $mysqli->Query("SELECT * FROM $spielID"."_spieler WHERE lebt = 0");
      while ($temp = $spielerRes->fetch_assoc())
      {
    $identitaet = "";
      switch ($temp['nachtIdentitaet'])
      {
        case CHARDORFBEWOHNER:
          $identitaet = "Dorfbewohner";
          break;
        case CHARWERWOLF:
          $identitaet = "Werwolf";
          break;
        case CHARSEHER:
          $identitaet = "Seher/in";
          break;
        case CHARHEXE:
          $identitaet = "Hexe/r";
          break;
        case CHARJAEGER:
          $identitaet = "Jäger/in";
          break;
        case CHARAMOR:
          $identitaet = "Amor";
          break;
        case CHARBESCHUETZER:
          $identitaet = "Beschützer/in";
          break;
        case CHARPARERM:
          $identitaet = "Paranormale(r) Ermittler/in";
          break;
        case CHARLYKANTROPH:
          $identitaet = "Lykantroph/in";
          break;
        case CHARSPION:
          $identitaet = "Spion/in";
          break;
        case CHARMORDLUSTIGER:
          $identitaet = "Mordlustige(r)";
          break;
        case CHARPAZIFIST:
          $identitaet = "Pazifist/in";
          break;
        case CHARALTERMANN:
          $identitaet = "Alte(r)";
          break;
        case CHARURWOLF:
          $identitaet = "Urwolf/Urwölfin";
          break;
      }
  //Eine Liste aller aktiver Spieler anzeigen
  //zuerst alle Lebenden anzeigen

      $rueckgabe.= $trennzeichen.$temp['name']." ($identitaet, tot)".$trennzeichen."4";

    }
    $mysqli->Query("UPDATE $spielID"."_game SET `list_tot` = '$rueckgabe', `list_tot_aktualisiert` = ". (int)(microtime(true)*1000));
    echo $rueckgabe;
  }

  //0: schwarz
  //1: grün
  //2: rot
  //3: schwarz groß
  //4: grau
?>
