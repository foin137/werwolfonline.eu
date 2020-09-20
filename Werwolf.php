<!DOCTYPE html>
<HTML>
<head>
<title>Werwölfe</title>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" /> 
<meta name="viewport" content="width=device-width, initial-scale=1" />
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Cache-Control" content="no-cache">
<meta http-equiv="Expires" content="Sat, 01 Dec 2001 00:00:00 GMT">
<link rel="stylesheet" type="text/css" href="style.css">
<?php
//Für die Farben ...
if (isset($_POST['settings_color']))
{
  save_local_settings();
  die("Reload please");
}
if (isset($_COOKIE['developer']))
{
  if ($_COOKIE['developer']==1)
  {
    print_r($_COOKIE);
    print_r($_POST);
  }
}
if (isset($_COOKIE['back_color_n_r']) && isset ($_COOKIE['back_color_n_g']) && isset ($_COOKIE['back_color_n_b']) && isset ($_COOKIE['back_color_d_r']) && isset ($_COOKIE['back_color_d_g']) && isset ($_COOKIE['back_color_d_b']) && isset ($_COOKIE['color_p_r'])  && isset ($_COOKIE['color_p_g']) && isset ($_COOKIE['color_p_b']) && isset ($_COOKIE['color_n_r'])  && isset ($_COOKIE['color_n_g']) && isset ($_COOKIE['color_n_b']))
{
    try{
    
      $c_back_n_r = $_COOKIE['back_color_n_r'];
      $c_back_n_g = $_COOKIE['back_color_n_g'];
      $c_back_n_b = $_COOKIE['back_color_n_b'];
      $c_back_d_r = $_COOKIE['back_color_d_r'];
      $c_back_d_g = $_COOKIE['back_color_d_g'];
      $c_back_d_b = $_COOKIE['back_color_d_b'];
      $c_p_r = $_COOKIE['color_p_r'];
      $c_p_g = $_COOKIE['color_p_g'];
      $c_p_b = $_COOKIE['color_p_b'];
      $c_n_r = $_COOKIE['color_n_r'];
      $c_n_g = $_COOKIE['color_n_g'];
      $c_n_b = $_COOKIE['color_n_b'];
    }
    catch(Exception $e)
    {
      $c_p_r = 181;//= "#00AA00";
      $c_p_g = 33;
      $c_p_b = 76;
      $c_back_n_r = 60;// = "#404050";
      $c_back_n_g = 60;
      $c_back_n_b = 60;
      $c_back_d_r = 199;//= "#BBAA80";
      $c_back_d_g = 194;
      $c_back_d_b = 149;
      $c_n_r = 86; //= "#3162dd";
      $c_n_g = 132;
      $c_n_b = 247;
    }
}
else
{
    $c_p_r = 181;//= "#00AA00";
      $c_p_g = 33;
      $c_p_b = 76;
      $c_back_n_r = 60;// = "#404050";
      $c_back_n_g = 60;
      $c_back_n_b = 60;
      $c_back_d_r =199;//= "#BBAA80";
      $c_back_d_g = 194;
      $c_back_d_b = 149;
      $c_n_r = 86; //= "#3162dd";
      $c_n_g = 132;
      $c_n_b = 247;
}

?>
<style type="text/css">
 #gameboard {background-color:<?php echo "rgb($c_back_n_r,$c_back_n_g,$c_back_n_b)"; ?>}

 h3 {
    color:<?php echo "rgb($c_p_r,$c_p_g,$c_p_b)"; ?>;
    font-size:150%;
 }
p {
    color:<?php echo "rgb($c_n_r,$c_n_g,$c_n_b)"; ?>;
    font-size:100%;
}
p.error {
    color:red;
    font-size:100%;
}
p#liste {
    color:black;
    font-size:100%;
    line-height:1.2;
    margin:0;
}

</style>
<link rel="SHORTCUT ICON" href="images/icon.ico" type="image/x-icon">
</head>
<body onload="jsstart();">
<section id="header">
<h1>Werwolfonline.eu</h1>
</section>
<section id="gameboard">
<?php
  include "includes.php"; //Datenbank
  include "constants.php"; //Hier werden Konstanten für Phasen und Charaktere definiert
  $pageReload = false;
  $listReload = false;
  $aktBeiTime = false; //Bei True aktualisiert der Browser bei Ablauf des Timers
  $displayTag = false; //bei true ändert sich der Hintergrund
  $displayFade = false; //bei true ist die Änderung des Hintergrunds ein Übergang
  $timerZahl = 0; // Gibt die Sekundenanzahl an, von denen Javascript herunterzählen soll.
  $timerAb = 0; //Ab wann Timer und Text angezeigt werdne (Sekunden bis zu dem Zeitpunkt)
  $timerText = ""; //Welcher Text vor dem Timer angezeigt werden soll
  $logButtonAnzeigen = true;
  
  
      //Schauen, ob wir uns bereits in einem Spiel befinden!
      if (isset($_COOKIE['SpielID']) && isset($_COOKIE['eigeneID']))
      {
          $_COOKIE['SpielID'] = (int)$_COOKIE['SpielID'];
          $_COOKIE['eigeneID'] = (int)$_COOKIE['eigeneID'];
          $spielID = $_COOKIE['SpielID'];
          $eigeneID = $_COOKIE['eigeneID'];
          if (isset($_POST['spielLoeschen']))
          {
            //Will der Spieler das Spiel löschen?
            if ($_POST['spielLoeschen'] == 1)
            {
              //Sicherheitshalber auch den Spieler entfernen
              aus_spiel_entfernen((int)$eigeneID,$mysqli);
              toGameLog($mysqli, getName($mysqli,$eigeneID). " hat das Spiel verlassen.");
              //Cookies löschen
              setcookie ("SpielID", 0, time()-172800); 
              setcookie ("eigeneID",0, time()-172800);
              start();
            }
          }
          else
          {
            //Schauen, ob es auch einen Eintrag zu diesem Spiel in der Datenbank gibt...
            $alleres = $mysqli->Query("SELECT * FROM $spielID"."_spieler");
            if(isset($alleres->num_rows))
            {
              //Schauen, ob es auch mich als Spieler gibt und meine verifizierungsnr stimmt
              $spielerResult = $mysqli->Query("SELECT * FROM $spielID"."_spieler WHERE id = $eigeneID AND verifizierungsnr = ".(int)$_COOKIE['verifizierungsnr']);
              if ($spielerResult->num_rows >= 1)
              {
                
                //Zuallererst einmal den letzten Zugriff loggen:
                $mysqli->Query("UPDATE $spielID"."_game SET letzterAufruf = ".time());
                //Wir befinden uns bereits in einem Spiel
                
                //Dass wir aber nicht ohne Grund reloaden, setzen wir für uns selbst reload auf false:
                setReloadZero($eigeneID,$mysqli);
                //echo "<p algin='center' class='normal'>Du befindest dich bereits in einem Spiel, Name: ".getName($mysqli,$eigeneID)."</p>";
                $myname = getName($mysqli,$eigeneID);
                echo "<div id='playername'><p class='normal' >Name: ". $myname ."</p></div>";
                
                //Nachschauen, ob ich Bürgermeister bin ... Dann nämlich anschreiben ...
                $buergermRes = $mysqli->Query("SELECT * FROM $spielID"."_spieler WHERE buergermeister = 1");
                if ($buergermRes->num_rows > 0)
                {
                  $buergermAss = $buergermRes->fetch_assoc();
                  if ($buergermAss['id']==$eigeneID)
                  {
                    //Ich bin Bürgermeister
                    echo "<p  class='normal'>Sie sind Bürgermeister</p>";
                  }
                }
                
                //Vielleicht will der Spielleiter jemanden entfernen?
                if (isset($_POST['spieler_entfernen']) && isset($_POST['entfernenID']))
                {
                    $res = $mysqli->Query("SELECT * FROM $spielID"."_spieler WHERE id = $eigeneID AND spielleiter = 1");
                    if ($res->num_rows > 0)
                    {
                        aus_spiel_entfernen((int)$_POST['entfernenID'],$mysqli);
                        $text = $myname."(Spielleiter) hat ".getName($mysqli,(int)($_POST['entfernenID'])). " aus dem Spiel entfernt.";
                        toGameLog($mysqli, $text);
                        toAllPlayerLog($mysqli, $text);
                    }
                }
                
                //Bevor wir noch auf die Phase schauen, schauen wir, ob irgendetwas unabhängig von der Phase ist
                //wie zum Beispiel der Tod des Jägers
                $jaegerRes = $mysqli->Query("SELECT * FROM $spielID"."_spieler WHERE jaegerDarfSchiessen = 1");
                if ($jaegerRes->num_rows > 0)
                {
                  $jaegerA = $jaegerRes->fetch_assoc();
                  if ($jaegerA['id']==$eigeneID)
                  {
                    //Falls wir selbst der Jäger sind, dürfen wir schießen
                    if (isset($_POST['jaegerHatAusgewaehlt']))
                    {
                      toeteSpieler($mysqli, $_POST['jaegerID']);
                      //Dann setze jaegerDarfSchiessen wieder auf 0
                      $mysqli->Query("UPDATE $spielID"."_spieler SET jaegerDarfSchiessen = 0, reload = 1 WHERE id = $eigeneID");
                      $mysqli->Query("UPDATE $spielID"."_spieler SET reload = 1");
                      $pageReload = true;
                    }
                    jaegerInitialisiere($mysqli);
                  }
                  else
                  {
                    echo "<p >Der Jäger wurde getötet</p>";
                    echo "<p class='normal' >Warten auf Jäger</p>";
                    $pageReload = true;
                  }
                }
                else
                {
                  //Weiters schauen wir noch, ob der Bürgermeister sein Amt weitergeben muss...
                  $buergermeisterRes = $mysqli->Query("SELECT * FROM $spielID"."_spieler WHERE buergermeisterDarfWeitergeben = 1");
                  $gameAssoc = gameAssoc($mysqli); //Nur Bürgermeister weitergeben, wenn das Spiel noch nicht aus ist
                  if ($buergermeisterRes->num_rows > 0 && $gameAssoc['spielphase'] != PHASESIEGEREHRUNG && $gameAssoc['spielphase'] != PHASESPIELSETUP && $gameAssoc['spielphase'] != PHASESETUP)
                  {
                    //Der Bürgermeister wurde getötet und darf sein Amt weitergeben ...
                    $buergermA = $buergermeisterRes->fetch_assoc();
                    if ($buergermA['id']==$eigeneID)
                    {
                      //Falls wir selbst der Jäger sind, dürfen wir schießen
                      if (isset($_POST['buergermeisterNachfolger']))
                      {
                        //Dann setze buergermeisterDarfWeitergeben wieder auf 0
                        $mysqli->Query("UPDATE $spielID"."_spieler SET buergermeisterDarfWeitergeben = 0, reload = 1 WHERE id = $eigeneID");
                        $mysqli->Query("UPDATE $spielID"."_spieler SET reload = 1");
                        $neuerBuergermeister = (int)$_POST['buergermeisterID'];
                        $mysqli->Query("UPDATE $spielID"."_spieler SET buergermeister = 1 WHERE id = $neuerBuergermeister");
                        toGameLog($mysqli,getName($mysqli,$neuerBuergermeister)." wurde als Nachfolger des Bürgermeisters eingesetzt.");
                        toAllPlayerLog($mysqli,getName($mysqli,$neuerBuergermeister)." wurde als Nachfolger des Bürgermeisters eingesetzt.");
                        $pageReload = true;
                      }
                      buergermeisterInitialisiere($mysqli);
                    }
                    else
                    {
                      echo "<p >Der Bürgermeister wurde getötet</p>";
                      echo "<p class='normal' >Er darf sein Amt weitergeben</p>";
                      $pageReload = true;
                    }
                  }
                  else
                  {
                    //Die weitere Vorgehensweise kommt auf die Phase des Spiels an:
                    $gameResult = $mysqli->Query("SELECT * FROM $spielID"."_game");
                    //echo $mysqli->error;
                    $gameResAssoc = $gameResult->fetch_assoc();
                    $phase = $gameResAssoc['spielphase'];
                    
                    if ($phase == PHASENACHTBEGINN)
                    {
                      $displayTag = false; //bei true ändert sich der Hintergrund
                      $displayFade = true;
                    }
                    elseif ($phase > PHASENACHTBEGINN && $phase < PHASENACHTENDE)
                    {
                      $displayTag = false;
                      $displayFade = false;
                    }
                    elseif ($phase == PHASENACHTENDE)
                    {
                      $displayTag = true;
                      $displayFade = true;
                    }
                    else
                    {
                      $displayTag = true;
                      $displayFade = false;
                    }
                    
                    //Nachschauen, ob ich noch lebe ;)
                    $ass = eigeneAssoc($mysqli);
                    if ($phase >= PHASENACHTBEGINN && $phase <= PHASENACHABSTIMMUNG && $ass['lebt'] == 0)
                    {
                      //Falls ich tot bin, zeige
                      binTot($mysqli);
                      $listReload = true;
                    }
                    else
                    {
                      if ($phase == PHASESETUP)
                      {
                        //In Phase 0 hat der Spielleiter die Möglichkeit, die Regeln zu bearbeiten.
                        if (isset($_POST['spielEditieren']))
                        {
                          spielRegeln($mysqli);
                        }
                        else
                        {
                          //Grundsätzlich sollte in dieser Phase jeder responden:
                          $pageReload = true;
                          
                          //Zuerst die Regeln aktualisieren, falls sie bearbeitet wurden
                          if ($eigeneID == 0 && isset($_POST['editierenAuswahl']))
                            spielRegelnAnwenden($mysqli);
                          
                          //Phase 0 = Setup und Spielersuchen -> Zeige daher eine Liste der Spieler an
                          echo "<BR><h2>$spielID</h2><BR><p class='normal' >Mit dieser Zahl können andere Ihrem Spiel beitreten!</p>";
                          
                          //Der Spielleiter bekommt zusätzlich einen Button angezeigt, mit dem er die Einstellungen bearbeiten kann.
                          $eigRes = $mysqli->Query("SELECT * FROM $spielID"."_spieler WHERE id = $eigeneID");
                          $eigAss = $eigRes->fetch_assoc();
                          if ($eigAss['spielleiter'] == 1)
                          {
                            echo '<form action="Werwolf.php" method="post">
                            <input type="hidden" name="spielEditieren" value=1 />
                            <p class="normal" align="center">Sie als Spielleiter erhalten die Möglichkeit, die Regeln des Spiels zu bearbeiten:
                            <input type="submit" value = "Regeln bearbeiten"/></p>
                            </form>
                            <form action="Werwolf.php" method="post">
                            <input type="hidden" name="spielStarten" value=1 />
                            <p class="normal" align="center">
                            <input type="submit" value = "Spiel starten ;)"/></p>
                            </form>';
                          }
                          
                          //Zeige alle Spieler in einer Liste an--> Alt, wird jz via javascript gelöst
                          echo "<BR><h3>Spieler in diesem Spiel:</h3>";
                          $spieleranzahlQuery = $mysqli->Query("SELECT * FROM $spielID"."_spieler");
                          $spielerzahl = $spieleranzahlQuery->num_rows; //und zähle mit
                          //Die Liste wird mit Javascript erstellt
                          echo "<form name='list'><div id='listdiv'></div></form>";
                          $listReload = true; //Dass unsere Liste refresht wird ;)
                          echo "<h3 >Spieleranzahl: $spielerzahl</h3>";
                          
                          //Falls der Spielleiter das Spiel beginnenlassen will:
                          if (isset($_POST['spielStarten']))
                          {
                            spielInitialisieren($mysqli,$spielerzahl);  
                          }
                        }
                      }
                      elseif ($phase == PHASESPIELSETUP)
                      {
                        //Jedem einen Button anzeigen, dass er bereit ist.
                        //Wenn er noch nicht zugestimmt hat.
                        $pageReload = true;
                        $spielerAssoc = $spielerResult->fetch_assoc();
                        if ($spielerAssoc['bereit']== 0 && !isset($_POST['bereit']))
                        {
                          //Zeige Formular zum Klicken auf bereit
                          echo '<form action="Werwolf.php" method="post">
                            <input type="hidden" name="bereit" value=1 />
                            <p class="normal" align = "center">Drücke "bereit", um zu starten!</p>
                            <p class="normal" align="center">
                            <input type="submit" value = "bereit"/></p>
                            </form>';
                        }
                        else
                        {
                          if (isset($_POST['bereit']))
                          {
                            //Der Spieler hat auf den Button gedrückt!
                            //markiere das in der Datenbank
                            $mysqli->Query("UPDATE $spielID"."_spieler SET bereit = 1 WHERE id = $eigeneID");
                            //überprüfe, ob alle bereit sind ...
                            $bereitRes = $mysqli->Query("SELECT * FROM $spielID"."_spieler WHERE bereit = 0");
                            if ($bereitRes->num_rows < 1)
                            {
                              //Alle sind bereit, starte Spiel
                              spielStarten($mysqli);
                            }
                            else
                            {
                              //Spiel wird noch nicht gestartet, aber die anderen updaten:
                              //alleReloadAusser($eigeneID,$mysqli); Ausgeschaltet, weil die Liste über javascript aktualisiert wird
                            }
                          }
                          elseif (isset($_POST['spielBeginnenOhneRuecksicht']))
                          {
                            spielStarten($mysqli);
                          }
                          
                          //Diese Liste wird mit javascript erstellt
                          echo "<form name='list'><div id='listdiv'></div></form>";
                          $listReload = true; //Dass unsere Liste refresht wird
                          echo ("<p >Warte auf andere Spieler ...</p>");
                          
                          //Als Spielleiter sollte man das Spiel "ohne Rücksicht auf Verluste" beginnen können
                          if ($eigeneID == 0)
                          {
                            ?><form action="Werwolf.php" method="post">
                              <input type="hidden" name="spielBeginnenOhneRuecksicht" value=1 />
                              <p class="normal" align="center">
                              <input type="submit" value = "Spiel beginnen ohne zu warten" onClick="if (confirm('Wollen Sie das Spiel wirklich starten, ohne auf alle zu warten. Wer noch nicht bereit ist, wird aus dem Spiel gelöscht')==false){return false;}"/></p>
                            </form> 
                              <?php
                          }
                        }
                      }
                      elseif ($phase == PHASENACHTBEGINN)
                      {
                        echo "<p >Die Nacht bricht herein ...</p>";
                        $eigeneAssoc = eigeneAssoc($mysqli);
                        if ($eigeneAssoc['countdownBis']>time())
                        {
                          //Zeige noch Countdown an ...
                          $timerZahl = $eigeneAssoc['countdownBis']-time()+1;
                          $timerAb = 0;
                          $timerText = "";
                          $aktBeiTime = true;
                          echo "<div  id='timerdiv'></div>";
                        }
                        else
                        {
                          $mysqli->Query("UPDATE $spielID"."_game SET spielphase = ".PHASENACHT3);
                          $mysqli->Query("UPDATE $spielID"."_spieler SET reload = 1, bereit = 0");    
                          phaseInitialisieren(PHASENACHT3,$mysqli);
                          $pageReload = true;
                        }
                      }
                      elseif ($phase == PHASENACHT1)
                      {
                        //Amor wählt verliebte aus ...
                        characterButton($mysqli);
                        $eigeneAssoc = eigeneAssoc($mysqli);
                        if ($eigeneAssoc['bereit']==1)
                        {
                          warteAufAndere();
                          $pageReload = true;
                        }
                        else
                        {
                          if ($eigeneAssoc['nachtIdentitaet'] == CHARAMOR)
                          {
                            //Amor
                            if (isset($_POST['amorHatAusgewaehlt']))
                            {
                              if (amorGueltig($mysqli,$_POST['amorID1'],$_POST['amorID2'])==false)
                              {
                                amorInitialisiere($mysqli); //Irgendetwas ist schiefgegangen --> wähle nochmals aus
                              }
                              else
                              {
                                warteAufAndere();
                                $pageReload = true;
                                setBereit($mysqli,$eigeneID,1);
                                phaseBeendenWennAlleBereit(PHASENACHT1,$mysqli);
                              }
                            }
                            else
                            {
                              amorInitialisiere($mysqli);
                            }
                          }
                          else
                          {
                            //Ganz normaler Dorfbewohner
                            if (isset($_POST['weiterschlafen']))
                            {
                              //der Button wurde bereits geklickt
                              setBereit($mysqli,$eigeneID,1);
                              warteAufAndere();
                              $pageReload = true;
                              phaseBeendenWennAlleBereit(PHASENACHT1,$mysqli);
                            }
                            else
                            {
                              //zeige den Button an
                              dorfbewohnerWeiterschlafen();
                            }
                          }
                        }
                      }
                      elseif ($phase == PHASENACHT2)
                      {
                        //Die Verliebten erwachen ...
                        characterButton($mysqli);
                        $eigeneAssoc = eigeneAssoc($mysqli);
                        if ($eigeneAssoc['bereit']==1)
                        {
                          warteAufAndere();
                          $pageReload = true;
                        }
                        else
                        {
                          if ($eigeneAssoc['verliebtMit'] > -1)
                          {
                            if (isset($_POST['verliebteWeiter']))
                            {
                              setBereit($mysqli,$eigeneID,1);
                              warteAufAndere();
                              $pageReload = true;
                              phaseBeendenWennAlleBereit(PHASENACHT2,$mysqli);
                            }
                            else
                            {
                              //Zeige an, mit wem ich verliebt bin
                              echo "<form action='Werwolf.php' method='post'>";
                              echo "<p class='normal' >Der Pfeil des Amor, der nie sein Ziel verfehlt, trifft Sie und sie verlieben sich unsterblich ...</p>";
                              echo "<p >Sie sind verliebt mit ".getName($mysqli,$eigeneAssoc['verliebtMit'])."</p>";
                              echo "<p class='normal' >Sie spielen nun gemeinsam mit Ihrem Geliebten, gehören Sie unterschiedlichen Gruppierungen an,
                              gewinnen Sie nur, wenn Sie alle anderen Spieler töten. Ansonsten gewinnen Sie wie gewohnt mit Ihrer Gruppierung (Dorfbewohner, Werwölfe)</p>";
                              //Auch den Charakter anzeigen
                              $verliebtRes = $mysqli->Query("SELECT * FROM $spielID"."_spieler WHERE id = ".(int)$eigeneAssoc['verliebtMit']);
                              $verliebtAss = $verliebtRes->fetch_assoc();
                              echo "<p >Identität: ".nachtidentitaetAlsString($verliebtAss['nachtIdentitaet'])."</p>";
                              toPlayerLog($mysqli,getName($mysqli,$eigeneAssoc['verliebtMit'])." ist ".nachtidentitaetAlsString($verliebtAss['nachtIdentitaet']) .".",$eigeneID);
                              echo '<input type="hidden" name="verliebteWeiter" value=1 />';
                              echo '<p id = "normal" align = "center"><input type="submit" value = "Weiter"/></p></form>';
                            }
                          }
                          else
                          {
                            //Ganz normaler Dorfbewohner
                            if (isset($_POST['weiterschlafen']))
                            {
                              //der Button wurde bereits geklickt
                              setBereit($mysqli,$eigeneID,1);
                              warteAufAndere();
                              $pageReload = true;
                              phaseBeendenWennAlleBereit(PHASENACHT2,$mysqli);
                            }
                            else
                            {
                              //zeige den Button an
                              dorfbewohnerWeiterschlafen();
                            }
                          }
                        }
                      }
                      elseif ($phase == PHASENACHT3)
                      {
                        //Hier erwachen die Werwölfe, die Seherin und können ihre Nachtaktivität ausführen
                        //Die anderen Spieler müssen auf einen Button drücken, um als ready zu gelten.
                        
                        characterButton($mysqli); //Zuerst jedem Spieler seinen Charakter zeigen
                        
                        //Schaue nach, welcher Charakter ich bin...
                        $spielerResult = $mysqli->Query("SELECT * FROM $spielID"."_spieler WHERE id = $eigeneID");
                        $spielerAssoc = $spielerResult->fetch_assoc();
                        //Falls der Spieler bereit ist, brauche ich nur noch warten auf andere anzeigen
                        if ($spielerAssoc['bereit']==1)
                        {
                          warteAufAndere();
                          $pageReload = true;
                        }
                        else
                        {
                          $identitaet = $spielerAssoc['nachtIdentitaet'];
                          if ($identitaet == CHARSEHER)
                          {
                            //SEHER
                            if (isset($_POST['seherHatAusgewaehlt']))
                            {
                              if (seherSehe($mysqli,$_POST['seherID'])==false)
                              {
                                //Der Seher hat einen ungültigen Zug gemacht
                                //nochmals von vorne
                                seherInitialisiere($mysqli);
                              }
                              else
                              {
                                //Der Seher hat erfolgreich ausgewählt --> Schaue, ob wir die Phase schon beenden können
                                warteAufAndere();
                                $pageReload = true;
                                phaseBeendenWennAlleBereit(PHASENACHT3,$mysqli);
                              }
                            }
                            else
                            {
                              //Der Seher hat noch nicht ausgewählt -> Initialisiere
                              seherInitialisiere($mysqli);
                            }
                          }
                          elseif ($identitaet == CHARSPION)
                          {
                            //Spion
                            if (isset($_POST['spionHatAusgewaehlt']) && isset($_POST['spionID']) && isset ($_POST['spionIdentitaet']))
                            {
                              if (spionSehe($mysqli,$_POST['spionID'],$_POST['spionIdentitaet'])==false)
                              {
                                //Ungültiger Zug, nochmals von vorne
                                spionInitialisiere($mysqli);
                              }
                              else
                              {
                                //Gültiger Zug!
                                warteAufAndere();
                                $pageReload = true;
                                phaseBeendenWennAlleBereit(PHASENACHT3,$mysqli);
                              }
                            }
                            else
                            {
                              spionInitialisiere($mysqli);
                            }
                          }
                          elseif ($identitaet == CHARBESCHUETZER)
                          {
                            //Beschützer, früher Leibwächter
                            if (isset($_POST['beschuetzerHatAusgewaehlt']))
                            {
                              if (beschuetzerAuswahl($mysqli,$_POST['beschuetzerID'])==false)
                              {
                                //Ungültig -> nochmal
                                beschuetzerInitialisiere($mysqli);
                              }
                              else
                              {
                                //Der Beschützer hat erfolgreich ausgewählt
                                warteAufAndere();
                                $pageReload = true;
                                phaseBeendenWennAlleBereit(PHASENACHT3,$mysqli);
                              }
                            }
                            else
                            {
                              beschuetzerInitialisiere($mysqli);
                            }
                          }
                          elseif ($identitaet == CHARPARERM)
                          {
                            //Paranormaler Ermittler
                            $eigeneAssoc = eigeneAssoc($mysqli);
                            if ($eigeneAssoc['parErmEingesetzt']==0)
                            {
                              if (isset($_POST['parErmHatAusgewaehlt']))
                              {
                                if (parErmAusgewaehlt($mysqli,$_POST['parErmID'])==false)
                                {
                                  //Ungültig
                                  parErmInitialisiere($mysqli);
                                }
                                else
                                {
                                  //Erfolgreich
                                  setBereit($mysqli,$eigeneID,1);
                                  warteAufAndere();
                                  $pageReload = true;
                                  phaseBeendenWennAlleBereit(PHASENACHT3,$mysqli);
                                }
                              }
                              elseif (isset ($_POST['parErmNichtAuswaehlen']))
                              {
                                //Diese Runde nicht auswählen
                                setBereit($mysqli,$eigeneID,1);
                                warteAufAndere();
                                $pageReload = true;
                                phaseBeendenWennAlleBereit(PHASENACHT3,$mysqli);
                              }
                              else
                              {
                                //Fragen, ob er einsetzen will
                                parErmInitialisiere($mysqli);
                              }
                            }
                            else
                            {
                              //Ganz normaler Dorfbewohner
                              if (isset($_POST['weiterschlafen']))
                              {
                                //der Button wurde bereits geklickt
                                setBereit($mysqli,$eigeneID,1);
                                warteAufAndere();
                                $pageReload = true;
                                phaseBeendenWennAlleBereit(PHASENACHT3,$mysqli);
                              }
                              else
                              {
                                //zeige den Button an
                                dorfbewohnerWeiterschlafen();
                              }
                            }
                          }
                          elseif ($identitaet == CHARWERWOLF || $identitaet == CHARURWOLF)
                          {
                            //WERWÖLFE
                            $zeitAbgelaufen = false;
                            //Zuerst kontrollieren, ob die Zeit schon ausgelaufen ist
                            $eigeneAssoc = eigeneAssoc($mysqli);
                            if ($eigeneAssoc['countdownBis']<= time())
                            {
                              //Timer abgelaufen, nachschauen, ob es sich bloß um den Einstimmigkeits-Timer handelt ...
                              $gameAssoc = gameAssoc($mysqli);
                              if ($gameAssoc['werwolfeinstimmig']==1)
                              {
                                //Es handelt sich um die Einstimmigkeits-Abstimmung, mal sehen ob wir mehr als 2
                                //Werwölfe haben, dann starte einen neuen Timer [Anm.: müssen wir haben, da sonst werwolfeinstimmig gleich auf 0 gesetzt wird]
                                //Starte einen neuen Timer, bei dem die Abstimmung nicht mehr einstimmig sein muss ...
                                $werwolfQ = $mysqli->Query("SELECT * FROM $spielID"."_spieler WHERE (nachtIdentitaet = ". CHARWERWOLF ." OR nachtIdentitaet = ".CHARURWOLF.") AND lebt = 1");
                                $werwolfzahl = $werwolfQ->num_rows;
                                $countdownBis = time()+$gameAssoc['werwolftimer2']+$gameAssoc['werwolfzusatz2']*$werwolfzahl;
                                if ($countdownBis >= time()+15)
                                  $countdownAb = time()+5;
                                else
                                  $countdownAb = time();
                                $mysqli->Query("UPDATE $spielID"."_spieler SET countdownBis = $countdownBis, countdownAb = $countdownAb WHERE (nachtIdentitaet = ". CHARWERWOLF ." OR nachtIdentitaet = ".CHARURWOLF.") AND lebt = 1");
                                $mysqli->Query("UPDATE $spielID"."_game SET werwolfeinstimmig = 0");
                                
                                //Timer initialiseren
                                $timerZahl = $countdownBis - time()+1; 
                                $timerAb = $countdownAb - time()+1;
                                $aktBeiTime = true;
                                $timerText = "Zeit, bis die Abstimmung der Werwölfe zu keinem Ergebnis führen kann: ";
                                
                                //Überprüfe, ob es nicht jetzt schon eine Einstimmigkeit gibt...
                                //Die Wahl muss nicht einstimmig sein ...
                                $werwolfQ = $mysqli->Query("SELECT * FROM $spielID"."_spieler WHERE (nachtIdentitaet = ". CHARWERWOLF ." OR nachtIdentitaet = ".CHARURWOLF.") AND lebt = 1");
                                $werwolfzahl = $werwolfQ->num_rows;
                                $alleSpielerQ = $mysqli->Query("SELECT * FROM $spielID"."_spieler WHERE lebt = 1");
                                while ($temp = $alleSpielerQ->fetch_assoc())
                                {
                                   $query = $mysqli->Query("SELECT * FROM $spielID"."_spieler WHERE lebt = 1 AND (nachtIdentitaet = ". CHARWERWOLF ." OR nachtIdentitaet = ". CHARURWOLF .") AND wahlAuf = ".$temp['id']);
                                   if ($query->num_rows > $werwolfzahl/2)
                                   {
                                    //Mit Mehrheit beschlossen...
                                    $opfer = $temp['id'];
                                    $mysqli->Query("UPDATE $spielID"."_spieler SET bereit = 1, reload = 1 WHERE (nachtIdentitaet = ".CHARWERWOLF." OR nachtIdentitaet = ".CHARWERWOLF.")");
                                    $mysqli->Query("UPDATE $spielID"."_game SET werwolfopfer = $opfer");
                                    phaseBeendenWennAlleBereit(PHASENACHT3,$mysqli); //Schauen, ob wir die Phase schon beenden können
                                    toGameLog($mysqli,"Die Wahl der Werwölfe fiel mehrheitlich auf: ".$temp['name']);
                                    break;
                                   }
                                }
                              }
                              else
                              {
                                //Fehlschlag
                                $zeitAbgelaufen = true;
                                $mysqli->Query("UPDATE $spielID"."_spieler SET bereit = 1, reload = 1 WHERE (nachtIdentitaet = ".CHARWERWOLF." OR nachtIdentitaet = ".CHARURWOLF.")");
                                $mysqli->Query("UPDATE $spielID"."_game SET werwolfopfer = -1");
                                phaseBeendenWennAlleBereit(PHASENACHT3,$mysqli); //Schauen, ob wir die Phase schon beenden können
                                toGameLog($mysqli,"Die Werwölfe konnten sich nicht auf ein Opfer einigen ...");
                              }
                            }
                            else
                            {
                              //Timer initialiseren
                              $timerZahl = $eigeneAssoc['countdownBis'] - time()+1; 
                              $timerAb = $eigeneAssoc['countdownAb'] - time()+1;
                              $aktBeiTime = true;
                              $gameAssoc = gameAssoc($mysqli);
                              if ($gameAssoc['werwolfeinstimmig']==1)
                                $timerText = "Zeit, bis die Abstimmung der Werwölfe nicht mehr einstimmig sein muss: ";
                              else
                                $timerText = "Zeit, bis die Abstimmung der Werwölfe zu keinem Ergebnis führen kann: ";
                            }
                            if (isset($_POST['werwolfAuswahl']) && !$zeitAbgelaufen)
                            {
                              //einmal die Wahl eintragen
                              $wahlID = (int)$_POST['werwolfID'];
                              $mysqli->Query("UPDATE $spielID"."_spieler SET wahlAuf = $wahlID WHERE id = $eigeneID");
                              
                              //Schauen, ob wir einstimmig sein müssen
                              $gameAssoc = gameAssoc($mysqli);
                              if ($gameAssoc['werwolfeinstimmig']==1)
                              {
                                //Dann schauen, ob schon alle abgestimmt haben
                                $einstimmig = 1;
                                $opfer = $wahlID;
                                $alleSpielerRes = $mysqli->Query("SELECT * FROM $spielID"."_spieler WHERE lebt = 1");
                                while ($temp = $alleSpielerRes->fetch_assoc())
                                {
                                  if ($temp['nachtIdentitaet']==CHARWERWOLF || $temp['nachtIdentitaet'] == CHARURWOLF)
                                  {
                                    if ($temp['wahlAuf'] != $opfer)
                                      $einstimmig = 0;
                                  }
                                }
                                //Falls einstimmig--> Alle Werwölfe auf bereit setzen und weiter gehts ;)
                                if ($einstimmig == 1)
                                {
                                  $mysqli->Query("UPDATE $spielID"."_spieler SET bereit = 1, reload = 1 WHERE (nachtIdentitaet = ".CHARWERWOLF. " OR nachtIdentitaet = ". CHARURWOLF.")");
                                  $mysqli->Query("UPDATE $spielID"."_game SET werwolfopfer = $opfer");
                                  phaseBeendenWennAlleBereit(PHASENACHT3,$mysqli); //Schauen, ob wir die Phase schon beenden können
                                  toGameLog($mysqli,"Die Wahl der Werwölfe fiel einstimmig auf: ". getName($mysqli,$opfer)); 
                                }
                              }
                              else
                              {
                                //Die Wahl muss nicht einstimmig sein ...
                                $werwolfQ = $mysqli->Query("SELECT * FROM $spielID"."_spieler WHERE (nachtIdentitaet = ". CHARWERWOLF ." OR nachtIdentitaet = ". CHARURWOLF.") AND lebt = 1");
                                $werwolfzahl = $werwolfQ->num_rows;
                                $alleSpielerQ = $mysqli->Query("SELECT * FROM $spielID"."_spieler WHERE lebt = 1");
                                while ($temp = $alleSpielerQ->fetch_assoc())
                                {
                                   $query = $mysqli->Query("SELECT * FROM $spielID"."_spieler WHERE lebt = 1 AND (nachtIdentitaet = ". CHARWERWOLF ." OR nachtIdentitaet = ".CHARURWOLF.") AND wahlAuf = ".$temp['id']);
                                   if ($query->num_rows > $werwolfzahl/2)
                                   {
                                    //Mit Mehrheit beschlossen...
                                    $opfer = $temp['id'];
                                    $mysqli->Query("UPDATE $spielID"."_spieler SET bereit = 1, reload = 1 WHERE nachtIdentitaet = ".CHARWERWOLF." OR nachtIdentitaet = ".CHARURWOLF);
                                    $mysqli->Query("UPDATE $spielID"."_game SET werwolfopfer = $opfer");
                                    phaseBeendenWennAlleBereit(PHASENACHT3,$mysqli); //Schauen, ob wir die Phase schon beenden können
                                    toGameLog($mysqli,"Die Wahl der Werwölfe fiel mehrheitlich auf: ".$temp['name']);
                                    break;
                                   }
                                }
                              }
                            }
                            echo "<div id = 'timerdiv' ></div><br>";
                            echo "<form name='list'><div id='listdiv'></div></form>"; //Die Liste, was die anderen gewählt haben
                            $listReload=true;
                            echo "<form action='Werwolf.php' method='post'>";
                            echo '<input type="hidden" name="werwolfAuswahl" value=1 />';
                            echo "<p class='normal' >Für den Tod welches Spielers wollen Sie stimmen?</p>";
                            echo "<p ><select name = 'werwolfID' size = 1>";
                            $alleSpielerRes = $mysqli->Query("SELECT * FROM $spielID"."_spieler WHERE lebt = 1");
                            while ($temp = $alleSpielerRes->fetch_assoc())
                            {
                              echo "<option value = '".$temp['id']."'>".$temp['name']."</option>";
                            }
                            echo '</select></p><p id = "normal" align = "center"><input type="submit" value = "Für diesen Spieler stimmen"/></p></form>';
                            $gameAssoc = gameAssoc($mysqli);
                            if ($gameAssoc['werwolfeinstimmig']==1)
                              echo "<p class='normal'>Zur Info: Die Wahl muss einstimmig sein, wählen Sie solange, bis die Wahl einstimmig ist</p>";
                            else
                              echo "<p class='normal'>Zur Info: Die Mehrheit der Werwölfe bestimmt das Opfer (Einstimmigkeit bei mehr als 2 Spielern ist nicht erforderlich)</p>";
                            $pageReload = true; //Falls alle abgestimmt haben
                          }
                          else
                          {
                            //Ganz normaler Dorfbewohner
                            if (isset($_POST['weiterschlafen']))
                            {
                              //der Button wurde bereits geklickt
                              setBereit($mysqli,$eigeneID,1);
                              warteAufAndere();
                              $pageReload = true;
                              phaseBeendenWennAlleBereit(PHASENACHT3,$mysqli);
                            }
                            else
                            {
                              //zeige den Button an
                              dorfbewohnerWeiterschlafen();
                            }
                          }
                        }
                      }
                      elseif ($phase == PHASENACHT4)
                      {
                        characterButton($mysqli); //Zuerst jedem Spieler seinen Charakter zeigen
                      
                        //Schaue nach, welcher Charakter ich bin...
                        $spielerResult = $mysqli->Query("SELECT * FROM $spielID"."_spieler WHERE id = $eigeneID");
                        $spielerAssoc = $spielerResult->fetch_assoc();
                        //Falls der Spieler bereit ist, brauche ich nur noch warten auf andere anzeigen
                        if ($spielerAssoc['bereit']==1)
                        {
                          warteAufAndere();
                          $pageReload = true;
                        }
                        else
                        {
                          $identitaet = $spielerAssoc['nachtIdentitaet'];
                          if ($identitaet == CHARHEXE)
                          {
                            //Hexe
                            if (isset($_POST['hexeAuswahl']))
                            {
                              if (isset($_POST['hexeHeilen']))
                              {
                                if ($_POST['hexeHeilen']==1)
                                {
                                  //Die Hexe will das Opfer heilen...
                                  $eigeneAssoc=eigeneAssoc($mysqli);
                                  if ($eigeneAssoc['hexeHeiltraenke']>0)
                                  {
                                    $heilTraenkeNeu = (int)$eigeneAssoc['hexeHeiltraenke']-1;
                                    $mysqli->Query("UPDATE $spielID"."_spieler SET hexeHeiltraenke = $heilTraenkeNeu, hexeHeilt = 1 WHERE id = $eigeneID");
                                    toPlayerLog($mysqli,"1 Heiltrank verwendet.",$eigeneID);
                                    toGameLog($mysqli,"Die Hexe heilt das Opfer der Werwölfe.");
                                  }
                                  else
                                  {
                                    $mysqli->Query("UPDATE $spielID"."_spieler SET hexeHeilt = 0 WHERE id = $eigeneID");
                                  }
                                }
                                else
                                {
                                  $mysqli->Query("UPDATE $spielID"."_spieler SET hexeHeilt = 0 WHERE id = $eigeneID");
                                }
                              }
                              //Und jetzt noch schauen wegen Töten ...
                              if (isset($_POST['toeten']))
                              {
                                if ($_POST['toeten'] > -1)
                                {
                                  //Ein Opfer wurde ausgewählt ... schauen, ob wir überhaupt noch Todestrank haben...
                                  $eigeneAssoc=eigeneAssoc($mysqli);
                                  if ($eigeneAssoc['hexeTodestraenke']>0)
                                  {
                                    $todestraenkeNeu = (int)$eigeneAssoc['hexeTodestraenke']-1;
                                    $hexenOpfer = (int)$_POST['toeten'];
                                    $mysqli->Query("UPDATE $spielID"."_spieler SET hexeTodestraenke = $todestraenkeNeu, hexenOpfer = $hexenOpfer WHERE id = $eigeneID");
                                    toPlayerLog($mysqli,"1 Todestrank verwendet für Spieler ".getName($mysqli,$hexenOpfer),$eigeneID).".";
                                    toGameLog($mysqli,"Die Hexe verwendet einen Todestrank, um ".getName($mysqli,$hexenOpfer)." zu töten.");
                                  }
                                  else
                                  {
                                    $mysqli->Query("UPDATE $spielID"."_spieler SET hexenOpfer = -1 WHERE id = $eigeneID");
                                  }
                                }
                                else
                                {
                                  $mysqli->Query("UPDATE $spielID"."_spieler SET hexenOpfer = -1 WHERE id = $eigeneID");
                                } 
                              }
                              //Jetzt müssen wir die Hexe noch auf bereit setzen
                              setBereit($mysqli,$eigeneID,1);
                              $pageReload = true;
                              phaseBeendenWennAlleBereit(PHASENACHT4,$mysqli);//Schauen, ob wir schon zur nächsten Phase übergehen können.
                            }
                            else
                            {
                              hexeInitialisieren($mysqli);
                            }
                          }
                          elseif ($identitaet == CHARURWOLF)
                          {
                            if (isset($_POST['urwolfHatAusgewaehlt']) && isset($_POST['urwolfID']))
                            {
                              if (urwolfHandle($mysqli,$_POST['urwolfID'])==false)
                              {
                                //Ungültiger Zug, nochmals von vorne
                                urwolfInitialisiere($mysqli);
                              }
                              else
                              {
                                //Gültiger Zug!
                                warteAufAndere();
                                $pageReload = true;
                                phaseBeendenWennAlleBereit(PHASENACHT4,$mysqli);
                              }
                            }
                            else
                            {
                              $res = $mysqli->Query("SELECT * FROM $spielID"."_spieler WHERE id = $eigeneID");
                              $a = $res->fetch_assoc();
                              if ($a['urwolf_anzahl_faehigkeiten'] > 0)
                              {
                                urwolfInitialisiere($mysqli);
                              }
                              else
                              {
                                if (isset($_POST['weiterschlafen']))
                                {
                                  //der Button wurde bereits geklickt
                                  setBereit($mysqli,$eigeneID,1);
                                  warteAufAndere();
                                  $pageReload = true;
                                  phaseBeendenWennAlleBereit(PHASENACHT4,$mysqli);
                                }
                                else
                                {
                                  //zeige den Button an
                                  dorfbewohnerWeiterschlafen();
                                }  
                              }
                            }  
                          }
                          else
                          {
                            //keine Besondere Identitaet diese Nacht
                            if (isset($_POST['weiterschlafen']))
                            {
                              //der Button wurde bereits geklickt
                              setBereit($mysqli,$eigeneID,1);
                              warteAufAndere();
                              $pageReload = true;
                              phaseBeendenWennAlleBereit(PHASENACHT4,$mysqli);
                            }
                            else
                            {
                              //zeige den Button an
                              dorfbewohnerWeiterschlafen();
                            }
                          }
                        } 
                      }
                      elseif ($phase == PHASENACHTENDE)
                      {
                        echo "<h3 >Es wird Morgen ...</h3>";
                        $eigeneAssoc = eigeneAssoc($mysqli);
                        if ($eigeneAssoc['countdownBis']>time())
                        {
                          //Zeige noch Countdown an ...
                          $timerZahl = $eigeneAssoc['countdownBis']-time()+1;
                          $timerAb = 0;
                          $timerText = "";
                          $aktBeiTime = true;
                          echo "<div  id='timerdiv'></div>";
                        }
                        else
                        {
                          $mysqli->Query("UPDATE $spielID"."_game SET spielphase = ".PHASETOTEBEKANNTGEBEN);
                          $mysqli->Query("UPDATE $spielID"."_spieler SET reload = 1, bereit = 0");    
                          phaseInitialisieren(PHASETOTEBEKANNTGEBEN,$mysqli);
                          $pageReload = true;
                        }
                      }
                      elseif ($phase == PHASETOTEBEKANNTGEBEN)
                      {
                        characterButton($mysqli);
                        //Zeige den Tagestext an
                        $gameAssoc = gameAssoc($mysqli);
                        $tagestext = $gameAssoc['tagestext'];
                        echo "<h3 >Der Tag beginnt</h3>";
                        $eigeneAssoc = eigeneAssoc($mysqli);
                        echo "<p >$tagestext</p>";
                        if ($eigeneAssoc['popup_text'] != "")
                        {
                            echo "<br><p >".$eigeneAssoc['popup_text']."</p>";
                            $mysqli->Query("UPDATE $spielID"."_spieler SET popup_text = '' WHERE id = $eigeneID");
                        }
                        //Nachsehen, ob es einen Bürgermeister gibt
                        $bres = $mysqli->Query("SELECT * FROM $spielID"."_spieler WHERE buergermeister = 1");
                        if ($bres->num_rows > 0)
                        {
                          //Es gibt einen Bürgermeister --> Dieser kann zur nächsten Phase übergehen
                          //Setze mich selbst auf bereit
                          //Wenn ich selbst der Bürgermeister bin, kann ich zur nächsten Phase übergehen
                          $bass = $bres->fetch_assoc();
                          if ($bass['id']==$eigeneID)
                          {
                            if (isset($_POST['buergermeisterWeiter']))
                            {
                              $pageReload = true;
                              setBereit($mysqli,$eigeneID,1);
                              phaseBeendenWennAlleBereit(PHASETOTEBEKANNTGEBEN,$mysqli);
                              warteAufAndere();
                            }
                            else
                            {
                              //$eigeneAssoc = eigeneAssoc($mysqli); schon gemacht
                              if ($eigeneAssoc['bereit']==0)
                              {
                                //Zeige einen Button an, mit dem der Bürgermeister das Spiel fortführen kann
                                echo "<form action='Werwolf.php' method='post'>";
                                echo '<input type="hidden" name="buergermeisterWeiter" value=1 />';
                                echo '<p id = "normal" align = "center"><input type="submit" value = "Weiter"/></p>';
                                echo "</form>";
                              }
                              else
                              {
                                warteAufAndere();
                              }
                            }  
                          }
                          else
                          {
                            //Ich bin nicht Bürgermeister --> Warte, bis es weitergeht
                            $pageReload = true;
                            setBereit($mysqli,$eigeneID,1);
                            phaseBeendenWennAlleBereit(PHASETOTEBEKANNTGEBEN,$mysqli);
                            warteAufAndere();
                          }
                        }
                        else
                        {
                          //Es gibt keinen Bürgermeister --> Jeder muss auf weiter drücken
                          if (isset($_POST['dorfbewohnerWeiter']))
                            {
                              $pageReload = true;
                              setBereit($mysqli,$eigeneID,1);
                              phaseBeendenWennAlleBereit(PHASETOTEBEKANNTGEBEN,$mysqli);
                              warteAufAndere();
                            }
                            else
                            {
                              //$eigeneAssoc = eigeneAssoc($mysqli); schon gemacht
                              if ($eigeneAssoc['bereit']==0)
                              {
                                //Zeige einen Button an, mit dem der Bürgermeister das Spiel fortführen kann
                                echo "<form action='Werwolf.php' method='post'>";
                                echo '<input type="hidden" name="dorfbewohnerWeiter" value=1 />';
                                echo '<p id = "normal" align = "center"><input type="submit" value = "Weiter"/></p>';
                                echo "</form>";
                              }
                              else
                              {
                                warteAufAndere();
                              }
                            } 
                          
                        }
                      }
                      elseif ($phase == PHASEBUERGERMEISTERWAHL)
                      {
                        characterButton($mysqli);
                        echo "<h3 >Wahl des Bürgermeisters</h3>";
                        echo "<p class='normal' >Da es momentan keinen Bürgermeister im Dorf gibt, beschließt das Dorf, einen zu wählen ...</p>";
                        echo "<p class='normal' >Fragen Sie in die Runde, wer sich als Bürgermeister aufstellen lassen will und diskutieren Sie jede Bewerbung ...</p>";
                        //Bürgermeisterwahl, ähnlich der Werwolfabstimmung
                        if (isset($_POST['buergermeisterWahlAuswahl']))
                        {
                          //einmal die Wahl eintragen
                          $wahlID = (int)$_POST['buergermeisterID'];
                          $mysqli->Query("UPDATE $spielID"."_spieler SET wahlAuf = $wahlID WHERE id = $eigeneID");
                          //Dann schauen, ob wir schon eine Mehrheit haben
                          
                          //Generiere eine Text zum Anzeigen, wer für wen gestimmt hat
                          $alleSpielerRes = $mysqli->Query("SELECT * FROM $spielID"."_spieler WHERE lebt = 1");
                          $text = "";
                          $anzahlSpieler = $alleSpielerRes->num_rows;
                          while ($temp = $alleSpielerRes->fetch_assoc())
                          {
                            $w = $temp['wahlAuf'];
                            if (!isset($wahlAufSpieler[$w]))
                              $wahlAufSpieler[$w] = 0;
                            $wahlAufSpieler[$w]++;
                            if ($w > -1)
                            {
                                $text .= $temp['name']." -> ". getName($mysqli,$w). ", "; 
                            }
                          }
                          //Schauen, ob jemand mehr als 50% der Stimmen hat
                          foreach ($wahlAufSpieler as $id => $stimmen)
                          {
                            if ($stimmen > $anzahlSpieler/2 && $id > -1)
                            {
                              //Dieser Spieler hat die Mehrheit
                              $mysqli->Query("UPDATE $spielID"."_spieler SET bereit = 1");
                              $mysqli->Query("UPDATE $spielID"."_spieler SET buergermeister = 1 WHERE id = $id");
                              toGameLog($mysqli,getName($mysqli,$id)." wurde zum Bürgermeister gewählt, abgestimmt haben: $text");
                              toAllPlayerLog($mysqli,getName($mysqli,$id)." wurde zum Bürgermeister gewählt, abgestimmt haben: $text");
                              phaseBeendenWennAlleBereit(PHASEBUERGERMEISTERWAHL,$mysqli); 
                              break;
                            }
                          }
                        }
                        echo "<form name='list'><div id='listdiv'></div></form>"; //Die Liste, was die anderen gewählt haben
                        $listReload=true;
                        echo "<form action='Werwolf.php' method='post'>";
                        echo '<input type="hidden" name="buergermeisterWahlAuswahl" value=1 />';
                        echo "<p class='normal' >Für welchen Spieler als Bürgermeister möchten Sie stimmen?</p>";
                        echo "<p ><select name = 'buergermeisterID' size = 1>";
                        $alleSpielerRes = $mysqli->Query("SELECT * FROM $spielID"."_spieler WHERE lebt = 1");
                        while ($temp = $alleSpielerRes->fetch_assoc())
                        {
                          echo "<option value = '".$temp['id']."'>".$temp['name']."</option>";
                        }
                        echo '</select></p><p id = "normal" align = "center"><input type="submit" value = "Für diesen Spieler stimmen"/></p></form>';
                        echo "<p class='normal'>Der Bürgermeister beginnt Abstimmungen und erhält bei der Abstimmung des Dorfes jeden Tag eine zusätzliche halbe Stimme.
                        Über 50% der Spieler müssen für den Bürgermeister stimmen, damit er gewählt wird</p>";
                        echo "</form>";
                        $pageReload = true; //Falls alle abgestimmt haben
                      }
                      elseif ($phase == PHASEDISKUSSION)
                      {
                        characterButton($mysqli);
                        //Diskussion
                        //Schauen, ob ich Bürgermeister bin...
                        $bres = $mysqli->Query("SELECT * FROM $spielID"."_spieler WHERE buergermeister = 1");
                        $bras = $bres->fetch_assoc();
                        if ($bras['id']==$eigeneID)
                        {
                          //Ich bin Bürgermeister, ich kann zur nächsten Phase übergehen...
                          if (isset($_POST['buergermeisterWeiter']))
                          {
                            $pageReload = true;
                            $mysqli->Query("UPDATE $spielID"."_spieler SET bereit = 1");
                            phaseBeendenWennAlleBereit(PHASEDISKUSSION,$mysqli);
                          }
                          else
                          {
                            //Zeige einen Button an, mit dem der Bürgermeister das Spiel fortführen kann
                            echo "<form action='Werwolf.php' method='post'>";
                            echo '<input type="hidden" name="buergermeisterWeiter" value=1 />';
                            echo '<p id = "normal" align = "center">Sie als Bürgermeister dürfen entscheiden,
                            wann es Zeit ist, die Diskussion zu beenden und zu den
                            Anklagen überzugehen.<input type="submit" value = "Zu den Anklagen übergehen"/></p>';
                            echo "</form>";
                          }
                        }
                        //Alle sehen diesen Text
                        echo "<h3 >Diskussion</h3>";
                        echo "<p class='normal' >Diskutieren Sie mit, versuchen Sie die Werwölfe zu entlarven, die anderen aber von Ihrer Unschuld zu überzeugen</p>";
                        $pageReload = true;
                      }
                      elseif ($phase == PHASEANKLAGEN)
                      {
                        characterButton($mysqli);
                        //Anklagen
                        echo "<form name='list'><div id='listdiv'></div></form>"; //Die Liste der Angeklagten
                        $listReload=true;
                        $pageReload=true;
                        //Jeder, der noch niemanden angeklagt hat, kann jemanden anklagen
                        $angeklagtRes = $mysqli->Query("SELECT * FROM $spielID"."_spieler WHERE angeklagtVon = $eigeneID");
                        if ($angeklagtRes->num_rows > 0)
                        {
                          //habe bereits jemanden angeklagt
                        }
                        else
                        {
                          if (isset($_POST['angeklagterID']))
                          {
                            //Ich klage gerade jemanden an
                            if ($_POST['angeklagterID']!=-1)
                              $mysqli->Query("UPDATE $spielID"."_spieler SET angeklagtVon = $eigeneID WHERE id = ".(int)$_POST['angeklagterID']);
                          }
                          else
                          {
                            //Wen möchte ich anklagen?
                            echo "<form action='Werwolf.php' method='post'>";
                            echo "<p class='normal' >Wollen Sie einen Spieler anklagen?</p>";
                            echo "<p ><select name = 'angeklagterID' size = 1>";
                            echo "<option value = '-1'>Niemand</option>";
                            $alleSpielerRes = $mysqli->Query("SELECT * FROM $spielID"."_spieler WHERE lebt = 1");
                            while ($temp = $alleSpielerRes->fetch_assoc())
                            {
                              echo "<option value = '".$temp['id']."'>".$temp['name']."</option>";
                            }
                            echo '</select></p><p id = "normal" align = "center"><input type="submit" value = "Diesen Spieler anklagen"/></p></form>';
                            echo "</form>";
                          }
                        }
                        
                        //Als Bürgermeister habe ich zusätzlich noch die Möglichkeit, zur nächsten Phase zu springen
                        $bres = $mysqli->Query("SELECT * FROM $spielID"."_spieler WHERE buergermeister = 1");
                        $bras = $bres->fetch_assoc();
                        if ($bras['id']==$eigeneID)
                        {
                          //Ich bin Bürgermeister, ich kann zur nächsten Phase übergehen...
                          if (isset($_POST['buergermeisterWeiter']))
                          {
                            $mysqli->Query("UPDATE $spielID"."_spieler SET bereit = 1");
                            phaseBeendenWennAlleBereit(PHASEANKLAGEN,$mysqli);
                          }
                          else
                          {
                            //Zeige einen Button an, mit dem der Bürgermeister das Spiel fortführen kann
                            echo "<form action='Werwolf.php' method='post'>";
                            echo '<input type="hidden" name="buergermeisterWeiter" value=1 />';
                            echo '<p id = "normal" align = "center"><input type="submit" value = "Zur Abstimmung übergehen"/></p>';
                            echo "<p id = 'normal' align= 'center'>Achten Sie als Bürgermeister darauf, dass alle Angeklagten Zeit haben, sich zu verteidigen.</p>";
                            echo "</form>";
                          }
                        }
                      }
                      elseif ($phase == PHASEABSTIMMUNG)
                      {
                        characterButton($mysqli);
                        //Abstimmung, bei Mehrheit wird Opfer getötet, sonst Stichwahl
                        //Wieder ähnlich wie Abstimmung für Bürgermeister
                        echo "<div  id='timerdiv'></div><br>";
                        echo "<form name='list'><div id='listdiv'></div></form>"; //Die Liste, was die anderen gewählt haben
                        $listReload=true;
                        $pageReload=true;
                        
                        //Schaue zuerst, ob der timer noch nicht abgelaufen ist
                        $eigeneAssoc = eigeneAssoc($mysqli);
                        if ($eigeneAssoc['countdownBis']<= time())
                        {
                          //Zeit abgelaufen
                          endeDerAbstimmungEinfacheMehrheit(-1,$mysqli);
                          toGameLog($mysqli,"Die Versammlung des Dorfes konnte sich nicht auf einen Spieler einigen, den sie töten will.");
                          toAllPlayerLog($mysqli,"Die Versammlung des Dorfes konnte sich nicht auf einen Spieler einigen, den sie töten will.");
                        }
                        else
                        {
                          $timerText = "Zeit, bis das Dorf zu keinem Ergebnis kommt: ";
                          $timerZahl = $eigeneAssoc['countdownBis'] - time()+1;
                          $timerAb = $eigeneAssoc['countdownAb']- time()+1;
                          $aktBeiTime = true;
                          if (isset($_POST['dorfWahlID']))
                          {
                            //einmal die Wahl eintragen
                            $wahlID = (int)$_POST['dorfWahlID'];
                            $mysqli->Query("UPDATE $spielID"."_spieler SET wahlAuf = $wahlID WHERE id = $eigeneID");
                            //Dann schauen, ob wir schon eine Mehrheit haben
                            $text = "";
                            $alleSpielerRes = $mysqli->Query("SELECT * FROM $spielID"."_spieler WHERE lebt = 1");
                            $anzahlSpieler = $alleSpielerRes->num_rows;
                            while ($temp = $alleSpielerRes->fetch_assoc())
                            {
                              $w = $temp['wahlAuf'];
                              if (!isset($wahlAufSpieler[$w]))
                                $wahlAufSpieler[$w] = 0;
                              $wahlAufSpieler[$w]+=1;
                              //Falls es der Bürgermeister ist, zusätzliche 1/2 Stimme
                              if ($temp['buergermeister']==1)
                                $wahlAufSpieler[$w]+=0.5;
                              if ($w > -1)
                              {
                                  $text .= $temp['name']." -> ". getName($mysqli,$w). ", "; 
                              }
                            }
                            $wahlErfolgreich = 0;
                            //Schauen, ob jemand mehr als 50% der Stimmen hat
                            foreach ($wahlAufSpieler as $id => $stimmen)
                            {
                              if ($stimmen > (($anzahlSpieler+0.5)/2) && $id > -1)
                              {
                                //Dieser Spieler hat die Mehrheit
                                toGameLog($mysqli,getName($mysqli,$id)." wurde bei der Abstimmung zum Tode verurteilt, mit den Stimmen: $text");
                                toAllPlayerLog($mysqli,getName($mysqli,$id)." wurde vom Dorf zum Tode verurteilt, mit den Stimmen: $text");
                                endeDerAbstimmungEinfacheMehrheit($id,$mysqli);
                                $wahlErfolgreich = 1; 
                                break;
                              }
                            }
                            if ($wahlErfolgreich == 0)
                            {
                              //Es gibt keinen Spieler mit absoluter Mehrheit
                              //Nachschauen, ob alle Spieler abgestimmt haben und es genau zwei Spieler mit den meisten Stimmen gibt.
                              $abgestimmtRes = $mysqli->Query("SELECT * FROM $spielID"."_spieler WHERE lebt = 1 AND wahlAuf = -1");
                              if ($abgestimmtRes->num_rows > 0)
                              {
                                //Es haben noch nicht alle abgestimmt
                              }
                              else
                              {
                                //Alle haben abgestimmt, nachschauen, wer die meisten Stimmen hat.
                                $maxStimmen = 0;
                                $maxStimmenSpieler = -1;
                                foreach ($wahlAufSpieler as $id => $stimmen)
                                {
                                  if ($stimmen > $maxStimmen && $id > -1)
                                  {
                                    $maxStimmen = $stimmen;
                                    $maxStimmenSpieler = $id;
                                  }
                                }
                                //Jetzt ermittle den zweiten Spieler
                                $zweitMaxStimmen = 0;
                                $zweitMaxStimmenSpieler = -1;
                                foreach ($wahlAufSpieler as $id => $stimmen)
                                {
                                  if ($stimmen > $zweitMaxStimmen && $id != $maxStimmenSpieler && $id > -1)
                                  {
                                    $zweitMaxStimmen = $stimmen;
                                    $zweitMaxStimmenSpieler = $id;
                                  }
                                }
                                //Schaue, ob jemand gleich viele Stimmen wie zweitMaxStimmenSpieler hat
                                //Wenn dies der Fall ist, gibt es 3 Kandidaten für eine Stichwahl => Nicht möglich ...
                                $exequo = false;
                                foreach ($wahlAufSpieler as $id => $stimmen)
                                {
                                  if ($stimmen >= $zweitMaxStimmen && $id != $maxStimmenSpieler && $id != $zweitMaxStimmenSpieler && $id > -1)
                                  {
                                    $exequo = true;
                                  }
                                }
                                if (!$exequo)
                                { 
                                  //Starte eine Stichwahl
                                  endeDerAbstimmungStichwahl($maxStimmenSpieler,$zweitMaxStimmenSpieler,$mysqli);
                                }
                              }
                            }
                          }
                          echo "<form action='Werwolf.php' method='post'>";
                          echo "<p class='normal' >Für welchen der angeklagten Spieler möchten Sie stimmen?</p>";
                          echo "<p ><select name = 'dorfWahlID' size = 1>";
                          $alleSpielerRes = $mysqli->Query("SELECT * FROM $spielID"."_spieler WHERE lebt = 1 AND angeklagtVon > -1");
                          while ($temp = $alleSpielerRes->fetch_assoc())
                          {
                            echo "<option value = '".$temp['id']."'>".$temp['name']."</option>";
                          }
                          echo '</select></p><p id = "normal" align = "center"><input type="submit" value = "Für diesen Spieler stimmen"/></p></form>';
                          echo "</form>";
                          echo "<form action='Werwolf.php' method='post'>";
                          echo "<p class='normal' >Sie möchten für einen anderen Spieler stimmen?</p>";
                          echo "<p ><select name = 'dorfWahlID' size = 1>";
                          $alleSpielerRes = $mysqli->Query("SELECT * FROM $spielID"."_spieler WHERE lebt = 1");
                          while ($temp = $alleSpielerRes->fetch_assoc())
                          {
                            echo "<option value = '".$temp['id']."'>".$temp['name']."</option>";
                          }
                          echo '</select></p><p id = "normal" align = "center"><input type="submit" value = "Für diesen Spieler stimmen"/></p></form>';
                          echo "</form>";
                        }
                      }
                      elseif ($phase == PHASESTICHWAHL)
                      {
                        characterButton($mysqli); 
                        //Es kommt zu einer Stichwahl
                        echo "<p >Stichwahl</p>";
                        echo "<div  id='timerdiv'></div><br>";
                        echo "<form name='list'><div id='listdiv'></div></form>"; //Die Liste, was die anderen gewählt haben
                        $listReload=true;
                        $pageReload=true;
                        //Schaue zuerst, ob der timer noch nicht abgelaufen ist
                        $eigeneAssoc = eigeneAssoc($mysqli);
                        if ($eigeneAssoc['countdownBis']<= time())
                        {
                          //Zeit abgelaufen
                          endeDerStichwahl(-1,$mysqli);
                          toGameLog($mysqli,"Das Dorf konnte sich in der Stichwahl nicht auf einen Spieler einigen, den es töten will.");
                          toAllPlayerLog($mysqli,"Das Dorf konnte sich auch in der Stichwahl nicht auf einen Spieler einigen, den es töten will.");
                        }
                        else
                        {
                          $timerText = "Zeit, bis die Stichwahl erfolglos ist: ";
                          $timerZahl = $eigeneAssoc['countdownBis'] - time()+1;
                          $timerAb = $eigeneAssoc['countdownAb']- time()+1;
                          $aktBeiTime = true;
                          if (isset($_POST['dorfWahlID']))
                          {
                            //einmal die Wahl eintragen
                            $wahlID = (int)$_POST['dorfWahlID'];
                            $mysqli->Query("UPDATE $spielID"."_spieler SET wahlAuf = $wahlID WHERE id = $eigeneID");
                            //Dann schauen, ob wir schon eine Mehrheit haben
                            $text = "";
                            $alleSpielerRes = $mysqli->Query("SELECT * FROM $spielID"."_spieler WHERE lebt = 1");
                            $anzahlSpieler = $alleSpielerRes->num_rows;
                            while ($temp = $alleSpielerRes->fetch_assoc())
                            {
                              $w = $temp['wahlAuf'];
                              if (!isset($wahlAufSpieler[$w]))
                                $wahlAufSpieler[$w] = 0;
                              $wahlAufSpieler[$w]+=1;
                              //Falls es der Bürgermeister ist, zusätzliche 1/2 Stimme
                              if ($temp['buergermeister']==1)
                                $wahlAufSpieler[$w]+=0.5;
                              if ($w > -1)
                              {
                                  $text .= $temp['name']." -> ". getName($mysqli,$w). ", "; 
                              }
                              
                            }
                            //Schauen, ob jemand mehr als 50% der Stimmen hat
                            foreach ($wahlAufSpieler as $id => $stimmen)
                            {
                              if ($stimmen > (($anzahlSpieler+0.5)/2) && $id > -1)
                              {
                                //Dieser Spieler hat die Mehrheit
                                $mysqli->Query("UPDATE $spielID"."_spieler SET bereit = 1");
                                toGameLog($mysqli,getName($mysqli,$id)." wurde bei der Abstimmung zum Tode verurteilt, mit den Stimmen: $text");
                                toAllPlayerLog($mysqli,getName($mysqli,$id)." wurde vom Dorf zum Tode verurteilt, mit den Stimmen: $text");
                                endeDerStichwahl($id,$mysqli); 
                                break;
                              }
                            }
                          }
                          echo "<form action='Werwolf.php' method='post'>";
                          echo "<p class='normal' >Für welchen der angeklagten Spieler möchten Sie bei der Stichwahl stimmen?</p>";
                          echo "<p ><select name = 'dorfWahlID' size = 1>";
                          $alleSpielerRes = $mysqli->Query("SELECT * FROM $spielID"."_spieler WHERE lebt = 1 AND angeklagtVon > -1");
                          while ($temp = $alleSpielerRes->fetch_assoc())
                          {
                            echo "<option value = '".$temp['id']."'>".$temp['name']."</option>";
                          }
                          echo '</select></p><p id = "normal" align = "center"><input type="submit" value = "Für diesen Spieler stimmen"/></p></form>';
                          echo "</form>";
                        }
                      }
                      elseif ($phase == PHASESIEGEREHRUNG)
                      {
                        $pageReload=true;
                        echo "<h3 >Wir haben einen Sieger!</h3>";
                        $gameAssoc = gameAssoc($mysqli);
                        $tagestext = $gameAssoc['tagestext'];
                        echo "<p >$tagestext</p>";
                        //Der Spielleiter sollte ein neues Spiel starten können
                        $eigeneAssoc = eigeneAssoc($mysqli);
                        if ($eigeneAssoc['spielleiter']==1)
                        {
                          if (isset($_POST['neuesSpiel']))
                          {
                            //Starten wir ein neues Spiel
                            $mysqli->Query("UPDATE $spielID"."_game SET spielphase = ".PHASESETUP);
                            $mysqli->Query("UPDATE $spielID"."_spieler SET reload = 1");
                          }
                          echo "<form action='Werwolf.php' method='post'>
                          <input type='hidden' name='neuesSpiel' value=1 />
                          <p class='normal' ><input type='submit' value='Neues Spiel'/></p>
                          </form>";
                        }
                        echo "<form name='gameLogForm' id='gameLogForm' style='display:none'><div id='gamelogdiv'></div></form>";
                        echo "<input type='submit' value='spiellog anzeigen' onClick='showGameLog($spielID);'";
                      }
                    }
                  }
                }
              }
              else
              {
                //Das Spiel gibt es, aber nicht den Spieler
                echo "<p class='error' >Sie sind momentan nicht mit diesem Spiel verknüpft </p>";
                diesesSpielLoeschenButton();
              }
            }
            else
            {
              echo "<p class='error' >Es sieht so aus, als gäbe es das Spiel nicht mehr ...</p>";
              diesesSpielLoeschenButton();
              $logButtonAnzeigen = false;
            }
          }
      }
      else
      {
          // Wir befinden uns in keinem Spiel ->
          //Nachschauen, ob wir bereits ein Spiel erstellen wollen?
          if (isset ($_POST['neuesSpiel']))
          {
            if ($_POST['neuesSpiel'] == 1)
            {
              //nachschauen, ob ein Benutzername angegeben wurde ...
              $verboteneNamen = array("niemanden","niemand","keinen","keiner","dorfbewohner","werwolf","seher","seherin","hexe","hexer","jäger","amor","beschützer","paranormaler ermittler","lykantroph","lykantrophin","spion","spionin","mordlustiger","mordlustige","pazifist","pazifistin","alter mann","alter","alte","alte frau","die alten","alten");
              if (!in_array(strtolower($_POST['ihrName']),$verboteneNamen) && $_POST['ihrName'] != "" && strpos($_POST['ihrName'],"$")===false && strpos($_POST['ihrName'],";")===false && strpos($_POST['ihrName'],'"')===false && strpos($_POST['ihrName'],"'")===false && strpos($_POST['ihrName'],"=")===false)
              {
                //Ab und zu alte Spiele löschen
                if (rand(1,100)==50)
                  loescheAlteSpiele($mysqli);
                //Wir erstellen ein neues Spiel
                //Eine Schleife, die solange rennt, bis eine neue Zahl gefunden wurde
                for ($i = 1; $i <= 100000; $i++)
                {
                if ($i == 1000)
                {
                  //Vielleicht gibt es alte Spiele, die Platz verbrauchen
                  loescheAlteSpiele($mysqli);
                }
                $spielID = rand(10000,99999);
                //nachschauen, ob ein Spiel mit dieser Nummer bereits existiert
                $res = $mysqli->Query("SELECT * FROM $spielID"."_spieler");
                if(isset($res->num_rows)){
                    
                    //Tabelle existiert
                    }else{
                    //Tabelle existiert noch nicht
                    //erstellen wir eine neue Tabelle
                    //BEIM HINZUFÜGEN: Auch die SetSpielerDefaultFunction ändern
                    $sql = "
                      CREATE TABLE `$spielID"."_spieler" ."` (
                      `id` INT( 10 ) NULL,
                      `name` VARCHAR( 150 ) NOT NULL ,
                      `spielleiter` INT( 5 ) NULL ,
                      `lebt` INT (2) NULL,
                      `wahlAuf` INT ( 5 ) DEFAULT -1 ,
                      `angeklagtVon` INT ( 5 ) DEFAULT -1 ,
                      `nachtIdentitaet` INT( 10 ) NULL,
                      `buergermeister` INT ( 2 ) DEFAULT 0,
                      `hexeHeiltraenke` INT( 10 ) NULL,
                      `hexeTodestraenke` INT( 5 ) NULL ,
                      `hexenOpfer` INT ( 5 ) DEFAULT -1 ,
                      `hexeHeilt` INT (2) DEFAULT 0,
                      `beschuetzerLetzteRundeBeschuetzt` INT( 5 ) DEFAULT -1 ,
                      `parErmEingesetzt` INT (2) DEFAULT 0 ,
                      `verliebtMit` INT ( 5 ) DEFAULT -1 ,
                      `jaegerDarfSchiessen` INT (2) DEFAULT 0 ,
                      `buergermeisterDarfWeitergeben` INT (2) DEFAULT 0 ,
                      `urwolf_anzahl_faehigkeiten` INT ( 5 ) DEFAULT 0,
                      `dieseNachtGestorben` INT (2) DEFAULT 0 ,
                      `countdownBis` INT (10) DEFAULT 0 ,
                      `countdownAb` INT (10) DEFAULT 0 ,
                      `playerlog` LONGTEXT ,
                      `popup_text` TEXT ,
                      `bereit` INT (2) NULL ,
                      `reload` INT (2) NULL ,
                      `verifizierungsnr` INT ( 5 ) DEFAULT 0
                      )  ;
                      ";
                    $mysqli->Query($sql);
                    //Wähle ein Verifizierungs-Passwort aus:
                    //Dieses dient dazu, um festzustellen, ob es tatsächlich der richtige Spieler ist, der eine Seite lädt
                    $verifizierungsnr = rand(2,100000);
                    $stmt = $mysqli->prepare("INSERT INTO $spielID"."_spieler"." (id, name , spielleiter, lebt, reload, verifizierungsnr) VALUES ( 0 , ?, 1 , 0 , 1, ?)");
                    $stmt->bind_param('si',$_POST['ihrName'],$verifizierungsnr);
                    $stmt->execute();
                    $stmt->close();
                    $sql2 = "
                      CREATE TABLE `$spielID"."_game` (
                      `spielphase` INT( 5 ) DEFAULT 0,
                      `charaktereAufdecken` INT ( 2 ) DEFAULT 0,
                      `buergermeisterWeitergeben` INT ( 2 ) DEFAULT 0,
                      `seherSiehtIdentitaet` INT ( 2 ) DEFAULT 1,
                      `werwolfzahl` INT ( 5 ) DEFAULT 0 ,
                      `hexenzahl` INT ( 5 ) DEFAULT 0 ,
                      `seherzahl` INT ( 5 ) DEFAULT 0 ,
                      `jaegerzahl` INT ( 5 ) DEFAULT 0 ,
                      `amorzahl` INT ( 2 ) DEFAULT 0 ,
                      `beschuetzerzahl` INT ( 5 ) DEFAULT 0 ,
                      `parErmZahl` INT (5) DEFAULT 0 ,
                      `lykantrophenzahl` INT ( 5 ) DEFAULT 0 ,
                      `spionezahl` INT ( 5 ) DEFAULT 0 ,
                      `idiotenzahl` INT ( 5 ) DEFAULT 0 ,
                      `pazifistenzahl` INT ( 5 ) DEFAULT 0 ,
                      `altenzahl` INT ( 5 ) DEFAULT 0 ,
                      `urwolfzahl` INT ( 5 ) DEFAULT 0 ,
                      `zufaelligeAuswahl` INT ( 2 ) DEFAULT 0 ,
                      `zufaelligeAuswahlBonus` INT ( 5 ) DEFAULT 0 ,
                      `werwolfeinstimmig` INT ( 2 ) DEFAULT 1 ,
                      `werwolfopfer` INT ( 5 ) DEFAULT -1 ,
                      `werwolftimer1` INT ( 10 ) DEFAULT 60 ,
                      `werwolfzusatz1` INT ( 10 ) DEFAULT 4 ,
                      `werwolftimer2` INT ( 10 ) DEFAULT 50 ,
                      `werwolfzusatz2` INT ( 10 ) DEFAULT 3 ,
                      `dorftimer` INT ( 10 ) DEFAULT 550 ,
                      `dorfzusatz` INT ( 10 ) DEFAULT 10 ,
                      `dorfstichwahltimer` INT ( 10 ) DEFAULT 200 ,
                      `dorfstichwahlzusatz` INT ( 10 ) DEFAULT 5 ,
                      `tagestext` TEXT ,
                      `nacht` INT ( 5 ) DEFAULT 1 ,
                      `log` LONGTEXT ,
                      `list_lebe` LONGTEXT,
                      `list_lebe_aktualisiert` BIGINT DEFAULT 0,
                      `list_tot` LONGTEXT,
                      `list_tot_aktualisiert` BIGINT DEFAULT 0,
                      `letzterAufruf` BIGINT
                      ) ;";
                    $mysqli->Query($sql2);
                    $mysqli->Query("INSERT INTO $spielID"."_game (spielphase, letzterAufruf) VALUES (0 , ".time().")");
                    
                    //Die SpielID groß mitteilen
                    echo "<BR><h1 align = 'center'>$spielID</h1><BR>Mit dieser Zahl können andere deinem Spiel beitreten!";
                    
                    //Die eigene SpielID setzen
                    setcookie ("SpielID", $spielID, time()+172800); //Dauer 2 Tage, länger sollte ein Spiel nicht dauern ;)
                    setcookie ("eigeneID",0, time()+172800);
                    setcookie ("verifizierungsnr",$verifizierungsnr, time()+172800);
                    $_COOKIE["SpielID"]=$spielID;
                    $_COOKIE["eigeneID"] = 0;
                    $_COOKIE["verifizieren"] = $verifizierungsnr;
                    writeGameToLogSpielErstellen($mysqli,$spielID,$_POST['ihrName']);
                    break; //die Schleife beenden
                    }
                }
                $pageReload = true;
              }
              else
              {
                //kein Name eingegeben! erneut
                echo "<p class='error' >Sie müssen einen gültigen Namen eingeben</p>";
                start();
              }
            }
            elseif ($_POST['neuesSpiel'] == 2)
            {
              //Der Spieler versucht einem bestehenden Spiel beizutreten
              $spielID = $_POST['bestehendeSpielnummer'];
              if ($_POST['ihrName'] != "" && strpos($_POST['ihrName'],"$")===false && strpos($_POST['ihrName'],";")===false && strpos($_POST['ihrName'],'"')===false && strpos($_POST['ihrName'],"'")===false && strpos($_POST['ihrName'],"=")===false)
              {
                //Name wurde eingegeben. Existiert auch ein Spiel dieser Nummer?
                $res = $mysqli->Query("SELECT * FROM $spielID"."_spieler");
                if(isset($res->num_rows))
                {
                  //Ein Spiel dieser Nummer existiert!
                  $verboteneNamen = array("niemanden","niemand","keinen","keiner","dorfbewohner","werwolf","seher","seherin","hexe","hexer","jäger","amor","beschützer","paranormaler ermittler","lykantroph","lykantrophin","spion","spionin","mordlustiger","mordlustige","pazifist","pazifistin","alter mann","alter","alte","alte frau","die alten","alten");
                  //Nachschauen, ob mein Name noch nicht vorkommt...
                  $stmt = $mysqli->prepare("SELECT * FROM $spielID"."_spieler WHERE name = ?");
                  $stmt->bind_param('s',$_POST['ihrName']);
                  $stmt->execute();
                  $nameRes = $stmt->get_result();
                  if ($nameRes->num_rows <= 0 && !in_array(strtolower($_POST['ihrName']),$verboteneNamen))
                  {
                   //Name gültig
                    //Finde eine freie ID
                    for ($i = 1; $i <= 50; $i++)
                    {
                      $res = $mysqli->Query("SELECT * FROM $spielID"."_spieler WHERE id = $i");
                      //echo "SELECT * FROM $spielID"."_spieler WHERE id = $i";
                      if(isset($res->num_rows))
                      {
                        if ($res->num_rows > 0)
                        {
                        //Es existiert bereits ein Spieler mit dieser ID --> weiter
                        //echo "existiert<BR>";
                        }
                        else
                        {
                          //Es existiert kein Spieler mit dieser ID --> lege an
                          //Wähle ein Verifizierungs-Passwort aus:
                          //Dieses dient dazu, um festzustellen, ob es tatsächlich der richtige Spieler ist, der eine Seite lädt
                          $verifizierungsnr = rand(2,100000); // 0 als Standard darf nicht vorkommen
                          $stmt = $mysqli->prepare("INSERT INTO $spielID"."_spieler"." (id, name , spielleiter, reload, verifizierungsnr) VALUES ( ? , ?, 0 , 1, ?)");
                          $stmt->bind_param('isi', $i, $_POST['ihrName'], $verifizierungsnr);
                          $stmt->execute();
                          $stmt->close();
                          echo "<p >Sie sind dem Spiel erfolgreich beigetreten!</p>";
                          setcookie ("SpielID", $spielID, time()+172800);
                          setcookie ("eigeneID",$i, time()+172800);
                          setcookie ("verifizierungsnr",$verifizierungsnr, time()+172800);
                          $_COOKIE["SpielID"]=$spielID;
                          $_COOKIE["eigeneID"] = $i;
                          $_COOKIE["verifizierungsnr"] = $verifizierungsnr;
                          $eigeneID = $i;                        
                          break; //die Schleife beenden
                        }
                      }
                      else
                      {
                         //Dieser Punkt sollte eigentlich nicht erreicht werden, da num_rows definiert sein müsste
                         echo "<BR>ERROR 0001: num_rows ist aus irgendeinem Grund nicht definiert <BR>";
                      }
                    }
                    $pageReload = true;
                    //Ein neuer Spieler ist dem Spiel beigetreten, wenn wir uns also in der Spielphase 0 = Spielersuchen
                    //befinden, aktualisiere mich --> Die anderen zu aktualisieren ist nicht notwendig, da sie sowieso über javascript die Liste aktualisieren
                    $mysqli->Query("UPDATE $spielID"."_spieler SET reload = 1 WHERE id = $eigeneID");
                  }
                  else
                  {
                     echo "<p class='error' >Der angegebene Name ist bereits vorhanden oder ungültig</p>";
                  }
                  $stmt->close();
                }
                else
                {
                  //Es existiert kein Spiel mit dieser Nummer --> Neustart
                  echo "<p class='error' >Es existiert kein Spiel mit dieser Nummer! </p>";
                start();
                }
              }
              else
              {
                //kein Name eingegeben --> neustart
                echo "<p class='error' >Sie müssen einen gültigen Namen eingeben</p>";
                start();
              }
            } 
          }
          else
          {
            start();
          }
      }
      //Schauen, ob wir uns bereits in einem Spiel befinden!
      if (isset($_COOKIE['SpielID']) && isset($_COOKIE['eigeneID']) && $logButtonAnzeigen)
      {
        //Wenn ja, zeige Logbutton an
        playerLogButton($mysqli);
        //Der Spielleiter sollte die Möglichkeit bekommen, einen Spieler aus dem Spiel zu werfen (weil z.B. inaktiv)
        if (isset($eigeneID) && isset($spielID))
        {
          $res = $mysqli->Query("SELECT * FROM $spielID"."_spieler WHERE id = $eigeneID AND spielleiter = 1");
          if ($res->num_rows > 0)
          {
              echo "<p  ><input type='submit' value='Spieler entfernen' onClick='showRemovePlayerForm()'></p>";
              echo "<div id='sl_entfernen' style='display: none;'><hr>";
              $res = $mysqli->Query("SELECT * FROM $spielID"."_spieler WHERE lebt = 1");
              while ($a = $res->fetch_assoc())
              {
                  echo "<form action='Werwolf.php' method='post'>";
                  echo "<input type='hidden' name='entfernenID' value = ".$a['id'].">";
                  echo "<input type='hidden' name='spieler_entfernen' value = 1>";
                  echo "<p class='normal' >".$a['name'];
                  echo"<input type='submit' value='entfernen' onClick='if (confirm(\"Wirklich diesen Spieler entfernen? Sie sollten das nur tun, wenn er inaktiv ist!\")==false){return false;}'></p>";
                  echo "</form>";
              }                                                                       
              echo "<hr></div>";   
          }
        }
      }
      
?>
</section>
<section id="client-settings">
<form action="Werwolf.php" method="post">
  <p>Löst oft viele Probleme: <input type="submit" value = "Reload" /></p>
</form>
<?php
local_settings();
if (isset($_COOKIE['SpielID']))
{
  diesesSpielLoeschenButton();
}
?>
</section>
<footer id="info">
<?php echo "". _VERSION ?>, Erstellt von Florian Lindenbauer<br>
<a href="http://www.werwolfonline.eu/info" target="_blank">Was ist Werwolf</a><br>
<a href="http://www.werwolfonline.eu/info/index.php/anleitung" target="_blank">Anleitung</a><br>
</footer>

<script charset="ISO-8859-1" type="text/javascript">

var xmlhttp;
var xmlhttp2;
var xmlhttp3;
var refreshGameLog = 0;
var sekBisTime;
var sekBisTimerBeginn;

    if (window.XMLHttpRequest)
      {// code for IE7+, Firefox, Chrome, Opera, Safari
      xmlhttp=new XMLHttpRequest();
      xmlhttp2=new XMLHttpRequest();
      xmlhttp3=new XMLHttpRequest();
      }
    else
      {// code for IE6, IE5
      xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
      xmlhttp2=new ActiveXObject ("Microsoft.XMLHTTP");
      xmlhttp3=new ActiveXObject ("Microsoft.XMLHTTP");
      }


   
   function reloadmaintain(game, id){				
    xmlhttp.onreadystatechange=function()
    {
      if (xmlhttp.readyState==4)
      {
        if (xmlhttp.status == 200)
        {
         if (xmlhttp.responseText == "1")
         {
            setTimeout(self.location.href="Werwolf.php",1); 
         }                                                                                                                  
         else
         {
            setTimeout(reloadmaintain,3500,game,id);
         }
        }
        else
        {
          //Error
          setTimeout(reloadmaintain,2*3500,game,id);
        }
      }
    }
    xmlhttp.open("GET","reload.php?game="+ game +"&id="+ id,true);
    xmlhttp.send();
	}
  
  function listRefresh(game, id, reload=0)
  {
    xmlhttp2.open("GET","listreload.php?game="+ game +"&id="+ id +"&reload="+reload,true);
    xmlhttp2.setRequestHeader("Content-Type","application/x-www-form-urlencoded;charset=ISO-8859-1");
    xmlhttp2.setRequestHeader('Cache-Control', 'no-cache');
    xmlhttp2.mscaching = "disabled";
    xmlhttp2.onreadystatechange=function()
      {
        if (xmlhttp2.readyState==4)
        {
          if (xmlhttp2.status==200)
          {
            var arr = xmlhttp2.responseText.split("$");
            if (arr.length > 0)
            {
              if (reload == 1 && arr[0] == 1)
              {
                  setTimeout(self.location.href="Werwolf.php",1);
                  return;    
              }
              var count = (arr.length-1)/2;
              var para = document.getElementById("listdiv");
              while (para.firstChild) {
                  para.removeChild(para.firstChild);
              }
              
              
              for (var i = 0; i < count; i++)
              {
                var temp = document.createElement("p");
                temp.id = "liste";
                temp.appendChild(document.createTextNode(arr[2*i+1]));
                if (arr[2*i+2]==0)
                  temp.style.color = "black";
                else if (arr[2*i+2]==1)
                  temp.style.color = "green";
                else if (arr[2*i+2]==2)
                  temp.style.color = "red";
                else if (arr[2*i+2]==3)
                {
                  temp.style.fontSize="200%";
                  temp.style.lineHeight="3";
                  temp.style.color = "black";
                }
                else if (arr[2*i+2]==4)
                  temp.style.color = "grey";
                temp.align="center";
                para.appendChild(temp);
              }
            }
            setTimeout(listRefresh,3000,game,id, reload);
          }
          else
          {
            //Error
            setTimeout(listRefresh,2*3000,game,id, reload);
          } 
        }
      }
    xmlhttp2.send(null);
  }
  
  function gameLogRefresh(game)
  {
    xmlhttp3.onreadystatechange=function()
      {
        if (xmlhttp3.readyState==4)
        {
          if (xmlhttp3.status==200)
          {
            var text = xmlhttp3.responseText;
            var para = document.getElementById("gamelogdiv");
            while (para.firstChild) {
                para.removeChild(para.firstChild);
            }
            var temp = document.createElement("p");
            temp.id = "normal";
            var withBreaks = text.split("<br>");
            for(var i = 0; i < withBreaks.length; i++) {
              temp.appendChild(document.createTextNode(withBreaks[i]));
              temp.appendChild(document.createElement("br"));
            }
            temp.align="center";
            para.appendChild(temp);
            if (refreshGameLog == 1)
            {
              setTimeout(gameLogRefresh,8000,game);
            }
          }
          else
          {
            //Error
            setTimeout(gameLogRefresh,2*8000,game);
          }
        }
      }
    xmlhttp3.open("GET","gamelogreload.php?game="+ game,true);
    xmlhttp3.send();
  }
  
  function timerAkt(akt,text)
  {
    if (sekBisTime < 0)
      return;
    if (sekBisTimerBeginn <= 0)
    { 
      var timerDiv = document.getElementById("timerdiv");
      while (timerDiv.firstChild) {
              timerDiv.removeChild(timerDiv.firstChild);
          }
      timerDiv.appendChild(document.createTextNode(text + String(sekBisTime)));
    }
    if (sekBisTime <= 0 && akt)
      setTimeout(self.location.href="Werwolf.php",1);
    sekBisTime -= 1;
    sekBisTimerBeginn -= 1;
  }
  function timerInit(sek,akt,timerAb,timerText)
  {
    sekBisTimerBeginn = timerAb;
    sekBisTime = sek;
    timerAkt(akt,timerText);
    setInterval(function(){timerAkt(akt,timerText);},1000);
  }
  
  function setUpReload(game, id)
  {
    setTimeout(reloadmaintain,3500,game,id);
  }
  
  function setUpListReload(game, id)
  {
    setTimeout(listRefresh,3000,game,id);
  }
  
  function showGameLog(game)
  {
    var form = document.getElementById("gameLogForm");
    if (form.style.display == "block")
    {
      form.style.display = "none";
      refreshGameLog = 0;
    }
    else
    {
      form.style.display = "block";
      gameLogRefresh(game);
      refreshGameLog = 1;
    }
  }
  function showRemovePlayerForm()
  {
    var form = document.getElementById("sl_entfernen");
    if (form.style.display == "block")
    {
      form.style.display = "none";
    }
    else
    {
      form.style.display = "block";
    }
  }
  
  function jsstart()
  {
    <?php if ($pageReload && !$listReload) 
    {
      echo 'reloadmaintain('.(int)$_COOKIE['SpielID'].','.(int)$_COOKIE['eigeneID'].');';
    }
    elseif ($listReload  && !$pageReload)
    {
      echo 'listRefresh('.(int)$_COOKIE['SpielID'].','.(int)$_COOKIE['eigeneID'].');';
    }
    elseif ($listReload && $pageReload)
    {
        echo 'listRefresh('.(int)$_COOKIE['SpielID'].','.(int)$_COOKIE['eigeneID'].',1);';    
    }
    
    if ($timerZahl > 0)
    {
      echo 'timerInit('.$timerZahl.','.$aktBeiTime.','.$timerAb.',"'.$timerText.'");';
    }
    if ($displayFade == true)
    {
      if ($displayTag == true)
        echo 'displayFade(true,1);';
      else
        echo 'displayFade(false,1);';
    }
    elseif($displayTag == true)
    {
      echo "displayTag();";
    }
    ?>
  }
  
  function showHideCharacter()
  {
    var form = document.getElementById("CharacterInfo");
    if (form.style.display == "block")
    {
      form.style.display = "none";
    }
    else
    {
      form.style.display = "block";
    }
  }
  
  function show_settings()
  {
    var form = document.getElementById("player_settings");
    if (form.style.display == "block")
    {
      form.style.display = "none";
    }
    else
    {
      form.style.display = "block";
    }
  }
  
  function showHidePlayerLog()
  {
    var form = document.getElementById("PlayerLog");
    if (form.style.display == "block")
    {
      form.style.display = "none";
    }
    else
    {
      form.style.display = "block";
    }
  }
  function displayFade(tag,anzahl)
  {
    var d_r = <?php echo $c_back_d_r; ?>;
    var d_g = <?php echo $c_back_d_g; ?>;
    var d_b = <?php echo $c_back_d_b; ?>;
    var n_r = <?php echo $c_back_n_r; ?>;
    var n_g = <?php echo $c_back_n_g; ?>;
    var n_b = <?php echo $c_back_n_b; ?>;
    
    var rp = 1;
    var gp = 1;
    var bp = 1;
    if (d_r < n_r)
        rp = -1;
    if (d_g < n_g)
        gp = -1;
    if (d_b < n_b)
        bp = -1;
    if (tag == true)
    {
      <?php //Von 404050 nach bbaa80 
      ?>
      var r = n_r+anzahl*rp;
      if ((r > d_r && rp == 1) || (r < d_r && rp == -1))
        r=d_r;
      var g = n_g+anzahl*gp;
      if ((g > d_g && gp == 1) || (g < d_g && gp == -1))
        g=d_g;
      var b = n_b+anzahl*bp;
      if ((b > d_b && bp == 1) || (b < d_b && bp == -1))
        b=d_b;
      var RR = r.toString(16);
      if (RR.length < 2)
      { RR = "0"+RR;}
      var GG = g.toString(16);
      if (GG.length < 2)
      { GG = "0"+GG;}
      var BB = b.toString(16);
      if (BB.length < 2)
      { BB = "0"+BB;}
      if (r == d_r && b == d_b && g == d_g)
      {
        
      }
      else
      {
        setTimeout(function(){ displayFade(tag,anzahl+1); }, 50);
      }
      var color = "#"+ RR + GG + BB;
      document.getElementById("gameboard").style.backgroundColor = color;
    }
    else
    {
      var r = d_r-anzahl*rp;
      if ((r < n_r && rp == 1) || (r > n_r && rp == -1))
        r=n_r;
      var g = d_g-anzahl*gp;
      if ((g < n_g && gp == 1) || (g > n_g && gp == -1))
        g=n_g;
      var b = d_b-anzahl*bp;
      if ((b < n_b && bp == 1) || (b > n_b && bp == -1))
        b=n_b;
      
      var RR = r.toString(16);
      if (RR.length < 2)
      { RR = "0"+RR;}
      var GG = g.toString(16);
      if (GG.length < 2)
      { GG = "0"+GG;}
      var BB = b.toString(16);
      if (BB.length < 2)
      { BB = "0"+BB;}
      if (r == d_r && b == d_b && g == d_g)
      {
       
      }
      else
      {
        setTimeout(function(){ displayFade(tag,anzahl+1); }, 50);
      }
      var color = "#"+ RR + GG + BB;
      document.getElementById("gameboard").style.backgroundColor = color;
    }
  }
  function displayTag()
  {
     var d_r = <?php echo $c_back_d_r; ?>;
    var d_g = <?php echo $c_back_d_g; ?>;
    var d_b = <?php echo $c_back_d_b; ?>;
    var RR = d_r.toString(16);
      var GG = d_g.toString(16);
      var BB = d_b.toString(16);
      
      if (RR.length < 2)
      { RR = "0"+RR;}
      
      if (GG.length < 2)
      { GG = "0"+GG;}
      
      if (BB.length < 2)
      { BB = "0"+BB;}
      var color = "#"+ RR + GG + BB;
     document.getElementById("gameboard").style.backgroundColor = color; 
  }
  
</script>
</body>
</HTML>

<?php
function start()
{
  //Diese Funktion zeigt das Startformular an, bei dem der Nutzer aufgefordert wird, ein Spiel zu erstellen, oder eine ID einzugeben
   ?>
   <div id="gameselect">
    <h3>Willkommen</h3>
    <p class='normal'>Sie befinden sich momentan in keinem Spiel: Sie können entweder einem bereits erstellten Spiel beitreten oder ein neues Spiel erstellen!</p>
    <div><form action="Werwolf.php" method="post">
      <h3 >Einem Spiel beitreten</h3>
      <input type="hidden" name="neuesSpiel" value=2 />
      <span class='normal'><label for="bestehendeSpielnummerID">Eine bestehende Spielnummer eingeben: </label><input type="text" name = "bestehendeSpielnummer" id="bestehendeSpielnummerID"/></span>
      <span class='normal'><label for="ihrName1ID">Ihr Name: </label><input type="text" name = "ihrName" id="ihrName1ID"/></span>
      <span ><input type="submit" value = "Diesem Spiel beitreten"/></span>
    </form>
    <form action="Werwolf.php" method="post">
      <h3>Ein neues Spiel erstellen</h3>
      <input type="hidden" name="neuesSpiel" value=1 />
      <span class='normal'><label for="ihrName2ID">Ihr Name: </label><input type="text" name = "ihrName" id="ihrName2ID"/></span>
      <span ><input type="submit" value = "Ein neues Spiel erstellen"/></span>
    </form></div></div>
          <?php
}

function loescheAlteSpiele($mysqli)
{
  //Alte Spiele sollten gelöscht werden, damit wieder Platz für neue Spiele ist
  //Alle Spieler, die vor mehr als 2 Stunden das letzte Mal geändert wurden, werden gelöscht
  $zeitpunkt = time()-7200; //Zeit vor 2 Stunden
  
  for ($i = 10000; $i<= 99999; $i++)
  {
    $alleres = $mysqli ->Query("SELECT * FROM $i"."_game");
    if(isset($alleres->num_rows))
    {
      $temp = $alleres->fetch_assoc();
      if ($temp['letzterAufruf'] < $zeitpunkt)
      {
        //löschen
        $mysqli->query("DROP TABLE `$i"."_game`");
        $mysqli->query("DROP TABLE `$i"."_spieler`");
        //echo $mysqli->error;
      }
    }
  }
}
function local_settings()
{
  //Lässt lokale Einstellungen (in Cookies gespeichert) wie Farbe zu.
  ?>
  <div>
  <p ><button onClick='show_settings()'>Einstellungen</button></p>
  <div id ='player_settings' style='display: none;'>
  <form action="Werwolf.php" method="post">
      <h3>Farbeinstellungen</h3>
      <input type="hidden" name="settings_color" value=1 />
<?php
      global $c_p_r, $c_p_g, $c_p_b, $c_back_n_r, $c_back_n_g, $c_back_n_b, $c_back_d_r, $c_back_d_g, $c_back_d_b, $c_n_r, $c_n_g, $c_n_b;
      $color = sprintf("#%02x%02x%02x", $c_p_r, $c_p_g, $c_p_b);
      echo "<span ><label for='color_slider_pID'>Überschriften: </label><input type='color' name='color_slider_p' id='color_slider_pID' value='$color'></span>";
      $color = sprintf("#%02x%02x%02x", $c_back_n_r, $c_back_n_g, $c_back_n_b);
      echo "<span ><label for='color_slider_nID'>Hintergrund Nacht: </label><input type='color' name='color_slider_n' id='color_slider_nID' value='$color'></span>";
      $color = sprintf("#%02x%02x%02x", $c_back_d_r, $c_back_d_g, $c_back_d_b);
      echo "<span ><label for='color_slider_dID'>Hintergrund Tag: </label><input type='color' name='color_slider_d' id='color_slider_dID' value='$color'></span>";
      $color = sprintf("#%02x%02x%02x", $c_n_r, $c_n_g, $c_n_b);
      echo "<span ><label for='color_slider_normID'>Normaler Text: </label><input type='color' name='color_slider_norm' id='color_slider_normID' value='$color'></span>";
      ?>
      <span><input type="submit" value = "Speichern und reload" /></span>
    </form>
    <form action="Werwolf.php" method="post">
    <input type="hidden" name="settings_color" value=2 />
    <span ><input type="submit" value = "Standardwerte wiederherstellen" /></span>
    </form></div></div> <?php

}
function save_local_settings()
{
  //speichert die lokalen Einstellungen
  if (isset($_POST['settings_color']))
  {
    if ($_POST['settings_color']==2)
    {
      set_default_colors();
    }
    else
    {
      list($r, $g, $b) = sscanf($_POST['color_slider_p'], "#%02x%02x%02x");
      setcookie ("color_p_r", $r, time()+32000000);
      setcookie ("color_p_g", $g, time()+32000000);
      setcookie ("color_p_b", $b, time()+32000000);
      list($r, $g, $b) = sscanf($_POST['color_slider_d'], "#%02x%02x%02x");
      setcookie ("back_color_d_r", $r, time()+32000000);
      setcookie ("back_color_d_g", $g, time()+32000000);
      setcookie ("back_color_d_b", $b, time()+32000000);
      list($r, $g, $b) = sscanf($_POST['color_slider_n'], "#%02x%02x%02x");
      setcookie ("back_color_n_r", $r, time()+32000000);
      setcookie ("back_color_n_g", $g, time()+32000000);
      setcookie ("back_color_n_b", $b, time()+32000000);
      list($r, $g, $b) = sscanf($_POST['color_slider_norm'], "#%02x%02x%02x");
      setcookie ("color_n_r", $r, time()+32000000);
      setcookie ("color_n_g", $g, time()+32000000);
      setcookie ("color_n_b", $b, time()+32000000);
    }
    header("Refresh:0"); //refresh the page now!
  }
}
function set_default_colors()
{
  $c_p_r = 0;//= "#00AA00";
  $c_p_g = 170;
  $c_p_b = 0;
  $c_back_n_r = 64;// = "#404050";
  $c_back_n_g = 64;
  $c_back_n_b = 80;
  $c_back_d_r =187;//= "#BBAA80";
  $c_back_d_g = 170;
  $c_back_d_b = 128;
  $c_n_r = 49; //= "#3162dd";
  $c_n_g = 98;
  $c_n_b = 221;
  
  $r = 100;
  $g = 100;
  $b = 100;
  setcookie ("color_p_r", $r, time()-32000000);
  setcookie ("color_p_g", $g, time()-32000000);
  setcookie ("color_p_b", $b, time()-32000000);
  setcookie ("back_color_d_r", $r, time()-32000000);
  setcookie ("back_color_d_g", $g, time()-32000000);
  setcookie ("back_color_d_b", $b, time()-32000000);
  setcookie ("back_color_n_r", $r, time()-32000000);
  setcookie ("back_color_n_g", $g, time()-32000000);
  setcookie ("back_color_n_b", $b, time()-32000000);
  setcookie ("color_n_r", $r, time()-32000000);
  setcookie ("color_n_g", $g, time()-32000000);
  setcookie ("color_n_b", $b, time()-32000000);
}

function spielRegeln($mysqli)
{
  //Zuerst die vorhandenen Einstellungen laden
  $spielID = $_COOKIE['SpielID'];
  $gameResult = $mysqli->Query("SELECT * FROM $spielID"."_game");
  $gameResAssoc = $gameResult->fetch_assoc();
  $buergermeisterWeitergeben = $gameResAssoc['buergermeisterWeitergeben'];
  $charaktereAufdecken = $gameResAssoc['charaktereAufdecken'];
  $seherSiehtIdentitaet = $gameResAssoc['seherSiehtIdentitaet'];
  $werwolfzahl = $gameResAssoc['werwolfzahl'];
  $hexenzahl = $gameResAssoc['hexenzahl'];
  $jaegerzahl = $gameResAssoc['jaegerzahl'];
  $seherzahl = $gameResAssoc['seherzahl'];
  $amorzahl = $gameResAssoc['amorzahl'];
  $beschuetzerzahl = $gameResAssoc['beschuetzerzahl'];
  $parErmZahl = $gameResAssoc['parErmZahl'];
  $lykantrophenzahl = $gameResAssoc['lykantrophenzahl'];
  $spionezahl = $gameResAssoc['spionezahl'];
  $idiotenzahl = $gameResAssoc['idiotenzahl'];
  $pazifistenzahl = $gameResAssoc['pazifistenzahl'];
  $altenzahl = $gameResAssoc['altenzahl'];
  $urwolfzahl = $gameResAssoc['urwolfzahl'];
  $werwolftimer1 = $gameResAssoc['werwolftimer1'];
  $werwolfzusatz1 = $gameResAssoc['werwolfzusatz1'];
  $werwolftimer2 = $gameResAssoc['werwolftimer2'];
  $werwolfzusatz2 = $gameResAssoc['werwolfzusatz2'];
  $dorftimer = $gameResAssoc['dorftimer'];
  $dorfzusatz = $gameResAssoc['dorfzusatz'];
  $dorfstichwahltimer = $gameResAssoc['dorfstichwahltimer'];
  $dorfstichwahlzusatz = $gameResAssoc['dorfstichwahlzusatz'];
  $zufaelligauswaehlen = $gameResAssoc['zufaelligeAuswahl'];
  $zufaelligeAuswahlBonus = $gameResAssoc['zufaelligeAuswahlBonus'];
  echo "
  <form action='Werwolf.php' method='post' id='gamesettings' name='auswahl'>
    <input type='hidden' name='editierenAuswahl' value=1 />
    <div><h3 >Allgemein</h3>
    <span><label for='buergermeisterID' >Bürgermeister </label>";
  if ($buergermeisterWeitergeben == 0)
    echo "<select name = 'buergermeister' id='buergermeisterID' size = 1><option value = '0' selected=true>Beim Tod des Bürgermeisters wird ein neuer gewählt</option><option value = '1'>Der Bürgermeister gibt das Amt bei seinem Tod weiter</option></select></span>";
  else
    echo "<select name = 'buergermeister' id='buergermeisterID' size = 1><option value = '0'>Beim Tod des Bürgermeisters wird ein neuer gewählt</option><option value = '1' selected=true>Der Bürgermeister gibt das Amt bei seinem Tod weiter</option></select></span>";
  echo "<span><label for='charaufdeckenID' >Tote Charaktere </label>";
  if ($charaktereAufdecken == 0)
    echo "<select name = 'aufdecken' id='charaufdeckenID' size = 1><option value = '0' selected=true>nicht aufdecken</option><option value = '1'>aufdecken</option></select></span>";
  else
    echo "<select name = 'aufdecken' id='charaufdeckenID' size = 1><option value = '0'>nicht aufdecken</option><option value = '1' selected=true>aufdecken</option></select></span>";
  echo "<span><label for='sehersiehtID' >Seher </label>";
  if ($seherSiehtIdentitaet == 0)
    echo "<select name = 'seherSieht' id='sehersiehtID' size = 1><option value = '0' selected=true>Seher sieht die Gesinnung</option><option value = '1'>Seher sieht die Identität</option></select></span>";
  else
    echo "<select name = 'seherSieht' id='sehersiehtID' size = 1><option value = '0' >Seher sieht die Gesinnung</option><option value = '1' selected=true>Seher sieht die Identität</option></select></span>";
  echo "</div><div><h3 >Charaktere</h3>";
  if ($zufaelligauswaehlen == 0)
  {
    echo "<span class='normal' ><label for='werwolfanzahl'>Werwölfe: </label><INPUT TYPE='number'  id='werwolfanzahl' NAME='werwoelfe' value=$werwolfzahl MIN=0>
      <INPUT TYPE='button' class='inc-dec-button' VALUE='+' NAME ='werwolfbutton1' OnClick='auswahl.werwoelfe.value=parseInt(auswahl.werwoelfe.value) + 1; '>
      <INPUT TYPE='button' class='inc-dec-button' VALUE='-' NAME ='werwolfbutton2' OnClick='auswahl.werwoelfe.value -=1 '></span>";
  }
  else
  {
    echo "<span class='normal' ><label for='werwolfanzahl'>Werwölfe: </label><INPUT TYPE='number'  id='werwolfanzahl' NAME='werwoelfe' value=$werwolfzahl MIN=0 DISABLED = true>
      <INPUT TYPE='button' class='inc-dec-button' VALUE='+' NAME ='werwolfbutton1' DISABLED = true OnClick='auswahl.werwoelfe.value=parseInt(auswahl.werwoelfe.value) + 1; '>
      <INPUT TYPE='button' class='inc-dec-button' VALUE='-' NAME ='werwolfbutton2' DISABLED = true OnClick='auswahl.werwoelfe.value -=1 '></span>";
  }
  echo "<span class='normal' ><label for='seheranzahl'> Seher: </label><INPUT TYPE='number'   NAME='seher' id='seheranzahl' Size='2' value=$seherzahl MIN=0>
    <INPUT TYPE='button' class='inc-dec-button' VALUE='+' OnClick='auswahl.seher.value=parseInt(auswahl.seher.value) + 1; '>
    <INPUT TYPE='button' class='inc-dec-button' VALUE='-' OnClick='auswahl.seher.value -=1 '></span>";
  echo "<span class='normal' ><label for='hexenanzahl'>Hexe: </label><INPUT TYPE='number'   NAME='hexe' id='hexenanzahl' Size='2' value=$hexenzahl MIN=0>
    <INPUT TYPE='button' class='inc-dec-button' VALUE='+' OnClick='auswahl.hexe.value=parseInt(auswahl.hexe.value) + 1; '>
    <INPUT TYPE='button' class='inc-dec-button' VALUE='-' OnClick='auswahl.hexe.value -=1 '></span>";
  echo "<span class='normal' ><label for='jaegeranzahl'>Jäger: </label><INPUT TYPE='number'   NAME='jaeger' id='jaegeranzahl' Size='2' value=$jaegerzahl MIN=0>
    <INPUT TYPE='button' class='inc-dec-button' VALUE='+' OnClick='auswahl.jaeger.value=parseInt(auswahl.jaeger.value) + 1; '>
    <INPUT TYPE='button' class='inc-dec-button' VALUE='-' OnClick='auswahl.jaeger.value -=1 '></span>";
  echo "<span class='normal' ><label for='beschuetzeranzahl'>Beschützer: </label><INPUT TYPE='number'   NAME='beschuetzer' id='beschuetzeranzahl' Size='2' value=$beschuetzerzahl MIN=0>
    <INPUT TYPE='button' class='inc-dec-button' VALUE='+' OnClick='auswahl.beschuetzer.value=parseInt(auswahl.beschuetzer.value) + 1; '>
    <INPUT TYPE='button' class='inc-dec-button' VALUE='-' OnClick='auswahl.beschuetzer.value -=1 '></span>";
  echo "<span class='normal' ><label for='parermanzahl'>Paranormaler Ermittler: </label><INPUT TYPE='number'   NAME='parErm' id='parermanzahl' Size='2' value=$parErmZahl MIN=0>
    <INPUT TYPE='button' class='inc-dec-button' VALUE='+' OnClick='auswahl.parErm.value=parseInt(auswahl.parErm.value) + 1; '>
    <INPUT TYPE='button' class='inc-dec-button' VALUE='-' OnClick='auswahl.parErm.value -=1 '></span>";
  echo "<span class='normal' ><label for='lynkantrophenanzahl'>Lykantroph: </label><INPUT TYPE='number'   NAME='lykantrophen' id='lynkantrophenanzahl' Size='2' value=$lykantrophenzahl MIN=0>
    <INPUT TYPE='button' class='inc-dec-button' VALUE='+' OnClick='auswahl.lykantrophen.value=parseInt(auswahl.lykantrophen.value) + 1; '>
    <INPUT TYPE='button' class='inc-dec-button' VALUE='-' OnClick='auswahl.lykantrophen.value -=1 '></span>";
  echo "<span class='normal' ><label for='spionanzahl'>Spion: </label><INPUT TYPE='number'   NAME='spione' id='spionanzahl' Size='2' value=$spionezahl MIN=0>
    <INPUT TYPE='button' class='inc-dec-button' VALUE='+' OnClick='auswahl.spione.value=parseInt(auswahl.spione.value) + 1; '>
    <INPUT TYPE='button' class='inc-dec-button' VALUE='-' OnClick='auswahl.spione.value -=1 '></span>";
  echo "<span class='normal' ><label for='idiotenanzahl'>Mordlustige: </label><INPUT TYPE='number'   NAME='idioten' id='idiotenanzahl' Size='2' value=$idiotenzahl MIN=0>
    <INPUT TYPE='button' class='inc-dec-button' VALUE='+' OnClick='auswahl.idioten.value=parseInt(auswahl.idioten.value) + 1; '>
    <INPUT TYPE='button' class='inc-dec-button' VALUE='-' OnClick='auswahl.idioten.value -=1 '></span>";
  echo "<span class='normal' ><label for='pazifistenanzahl'>Pazifist: </label><INPUT TYPE='number'   NAME='pazifisten' id='pazifistenanzahl' Size='2' value=$pazifistenzahl MIN=0>
    <INPUT TYPE='button' class='inc-dec-button' VALUE='+' OnClick='auswahl.pazifisten.value=parseInt(auswahl.pazifisten.value) + 1; '>
    <INPUT TYPE='button' class='inc-dec-button' VALUE='-' OnClick='auswahl.pazifisten.value -=1 '></span>";
   echo "<span class='normal' ><label for='altenanzahl'>Die Alten: </label><INPUT TYPE='number'   NAME='alten' id='altenanzahl' Size='2' value=$altenzahl MIN=0>
    <INPUT TYPE='button' class='inc-dec-button' VALUE='+' OnClick='auswahl.alten.value=parseInt(auswahl.alten.value) + 1; '>
    <INPUT TYPE='button' class='inc-dec-button' VALUE='-' OnClick='auswahl.alten.value -=1 '></span>";
  echo "<span class='normal' ><label for='urwolfanzahl'>Urwolf / Urwölfin: </label><INPUT TYPE='number'   NAME='urwolf' id='urwolfanzahl' Size='2' value=$urwolfzahl MIN=0>
    <INPUT TYPE='button' class='inc-dec-button' VALUE='+' OnClick='auswahl.urwolf.value=parseInt(auswahl.urwolf.value) + 1; '>
    <INPUT TYPE='button' class='inc-dec-button' VALUE='-' OnClick='auswahl.urwolf.value -=1 '></span>";
  echo "<span class='normal' ><label for='amoranzahl'>Amor: </label><INPUT TYPE='number'   NAME='amor' id='armoranzahl' Size='2' value=$amorzahl MIN=0 MAX=1>
    <INPUT TYPE='button' class='inc-dec-button' VALUE='1' OnClick='auswahl.amor.value=1; '>
    <INPUT TYPE='button' class='inc-dec-button' VALUE='0' OnClick='auswahl.amor.value=0; '></span>";
  echo "<span class='normal' ><INPUT TYPE='checkbox' ";
  if ($zufaelligauswaehlen == 1)
    echo "CHECKED = true";
  echo " NAME='zufaelligauswaehlen' id='zufaelligauswaehlenID' VALUE='zufall' 
  onClick='if (auswahl.zufaelligauswaehlen.checked) {
    auswahl.werwoelfe.value=0;
    auswahl.werwoelfe.disabled = true; auswahl.werwolfbutton1.disabled=true; auswahl.werwolfbutton2.disabled=true;}
    else {
    auswahl.werwoelfe.disabled = false;auswahl.werwolfbutton1.disabled=false; auswahl.werwolfbutton2.disabled=false;}'><label for='zufaelligauswaehlenID'> Die Charaktere verdeckt und zufällig auswählen </label></span>
    <p class='normal' >Geben Sie dazu eine maximale Anzahl von Charakteren ein, die vorkommen sollen, bei Werwölfen müssen sie nichts eingeben<br>
    Zusätzlich können Sie noch einen Wert eingeben, der die Verteilung bestimmt. Ein positiver Wert erleichtert das Spiel für die Dorfbewohner, ein negativer für die Werwölfe (nur bei der zufälligen Charakterverteilung)</p>";
  echo "<span class='normal' ><label for='zufaelligeAuswahlBonusID'> Verteilung der zufälligen Charaktere </label><INPUT TYPE='number'   NAME='zufaelligeAuswahlBonus' id='zufaelligeAuswahlBonusID' Size='2' value=$zufaelligeAuswahlBonus MIN=-15 MAX=15></span>";
  echo "</div>";
  echo "<div><h3 >Countdown-Einstellungen</h3>";
  echo "<span class='normal' ><INPUT TYPE='button' VALUE='Countdowns zurücksetzen' OnClick='auswahl.werwolftimer1.value=60; auswahl.werwolfzusatz1.value=4; auswahl.werwolftimer2.value=50; auswahl.werwolfzusatz2.value=3; auswahl.dorftimer.value=550; auswahl.dorfzusatz.value=10; auswahl.dorfstichwahltimer.value=200; auswahl.dorfstichwahlzusatz.value=5'></span>";
  echo "<span class='normal' ><label for='werwolftimer1ID'>Sekunden, bis die Werwölfe nicht mehr einstimmig wählen müssen: </label>
    <INPUT TYPE='number' NAME='werwolftimer1' id='werwolftimer1ID' SIZE='2' VALUE=$werwolftimer1 MIN='20' MAX='500'><br>
    <label for='werwolfzusatz1ID'>Zusätzliche Zeit pro Werwolf: </label><INPUT TYPE='number' NAME='werwolfzusatz1' id='werwolfzusatz1ID' SIZE='2' VALUE=$werwolfzusatz1 MIN='0' MAX='60'></span>";
  echo "<span class='normal' ><label for='werwolftimer2ID'>Sekunden, bis nach Ablaufen der Einstimmigkeit die Wahl der Werwölfe erfolglos ist: </label>
    <INPUT TYPE='number' NAME='werwolftimer2' id='werwolftimer2ID' SIZE='2' VALUE=$werwolftimer2 MIN='10' MAX='500'><br>
    <label for='werwolfzusatz2ID'>Zusätzliche Zeit pro Werwolf: </label><INPUT TYPE='number' NAME='werwolfzusatz2' id='werwolfzusatz2ID' SIZE='2' VALUE=$werwolfzusatz2 MIN='0' MAX='60'></span>";
  echo "<span class='normal' ><label for='dorftimerID'>Sekunden, bis die normale Abstimmung des Dorfes am Tag erfolglos ist: </label>
    <INPUT TYPE='number' NAME='dorftimer' id='dorftimerID' SIZE='2' VALUE=$dorftimer MIN='60' MAX='7200'><br>
    <label for='dorfzusatzID'>Zusätzliche Zeit pro Dorfbewohner: </label><INPUT TYPE='number' NAME='dorfzusatz' id='dorfzusatzID' SIZE='2' VALUE=$dorfzusatz MIN='0' MAX='300'></span>";
  echo "<span class='normal' ><label for='dorfstichwahltimerID'>Sekunden, bis die Stichwahl am Tag erfolglos ist: </label>
    <INPUT TYPE='number' NAME='dorfstichwahltimer' id='dorfstichwahltimerID' SIZE='2' VALUE=$dorfstichwahltimer MIN='30' MAX='3600'><br>
    <label for='dorfstichwahlzusatzID'>Zusätzliche Zeit pro Dorfbewohner: </label><INPUT TYPE='number' NAME='dorfstichwahlzusatz' id='dorfstichwahlzusatzID' SIZE='2' VALUE=$dorfstichwahlzusatz MIN='0' MAX='300'></span>"; 
  echo "</div><span align = 'center'><input type='submit' value = 'Speichern'/></span>";
  echo "</form>";
}

function spielRegelnAnwenden($mysqli)
{
  $spielID = $_COOKIE['SpielID'];
  if (isset($_POST['buergermeister']))
  {
    //Zuerst überprüfen, ob die Variablen überhaupt existieren
    $buergermeisterWeitergeben = (int)$_POST['buergermeister'];
    $charaktereAufdecken = (int)$_POST['aufdecken'];
    $seherSiehtIdentitaet = (int)$_POST['seherSieht'];
    $hexenzahl = (int)$_POST['hexe'];
    $jaegerzahl = (int)$_POST['jaeger'];
    $seherzahl = (int)$_POST['seher'];
    $amorzahl = (int)$_POST['amor'];
    $beschuetzerzahl = (int)$_POST['beschuetzer'];
    $parErmZahl = (int)$_POST['parErm'];
    $lykantrophenzahl = (int)$_POST['lykantrophen'];
    $spionezahl = (int)$_POST['spione'];
    $idiotenzahl = (int)$_POST['idioten'];
    $pazifistenzahl = (int)$_POST['pazifisten'];
    $altenzahl = (int)$_POST['alten'];
    $urwolfzahl = (int)$_POST['urwolf'];
    $werwolftimer1 = (int)$_POST['werwolftimer1'];
    $werwolfzusatz1 = (int)$_POST['werwolfzusatz1'];
    $werwolftimer2 = (int)$_POST['werwolftimer2'];
    $werwolfzusatz2 = (int)$_POST['werwolfzusatz2'];
    $dorftimer = (int)$_POST['dorftimer'];
    $dorfzusatz = (int)$_POST['dorfzusatz'];
    $dorfstichwahltimer = (int)$_POST['dorfstichwahltimer'];
    $dorfstichwahlzusatz = (int)$_POST['dorfstichwahlzusatz'];
    $zufaelligeAuswahlBonus = (int)$_POST['zufaelligeAuswahlBonus'];
    $zufaelligauswaehlen = 0;
    if (isset($_POST['zufaelligauswaehlen']))
    {
      $zufaelligauswaehlen = 1;
      $werwolfzahl = 0;
    }
    else
    {
      $werwolfzahl = $_POST['werwoelfe'];
    }
    try
    {
      if ($werwolftimer1 < 15 || $werwolfzusatz1 < 0 || $werwolftimer2 < 15 || $werwolfzusatz2 < 0 || $dorftimer < 60 || $dorfzusatz < 0 || $dorfstichwahltimer < 30 || $dorfstichwahlzusatz < 0)
      {
        //Alles auf Standardwerte zurücksetzen
        throw new Exception("Eingabe ungültig");
      }
    }
    catch (Exception $e)
    {
      echo "<p class='error' >Eine oder mehrere Countdown-Einstellungen sind ungültig</p>";
      $werwolftimer1 = 60;
      $werwolfzusatz1 = 4;
      $werwolftimer2 = 50;
      $werwolfzusatz2 = 3;
      $dorftimer = 550;
      $dorfzusatz = 10;
      $dorfstichwahltimer = 200;
      $dorfstichwahlzusatz = 5;
    }
    $mysqli->Query("UPDATE $spielID"."_game SET buergermeisterWeitergeben = $buergermeisterWeitergeben,
     charaktereAufdecken = $charaktereAufdecken,
     seherSiehtIdentitaet = $seherSiehtIdentitaet,
     werwolfzahl = $werwolfzahl,
     hexenzahl = $hexenzahl,
     jaegerzahl = $jaegerzahl,
     seherzahl = $seherzahl,
     amorzahl = $amorzahl,
     beschuetzerzahl = $beschuetzerzahl,
     parErmZahl = $parErmZahl,
     lykantrophenzahl = $lykantrophenzahl,
     spionezahl = $spionezahl,
     idiotenzahl = $idiotenzahl,
     pazifistenzahl = $pazifistenzahl,
     altenzahl = $altenzahl,
     urwolfzahl = $urwolfzahl,
     zufaelligeAuswahl = $zufaelligauswaehlen,
     zufaelligeAuswahlBonus = $zufaelligeAuswahlBonus,
     werwolftimer1 = $werwolftimer1,
     werwolfzusatz1 = $werwolfzusatz1,
     werwolftimer2 = $werwolftimer2,
     werwolfzusatz2 = $werwolfzusatz2,
     dorftimer = $dorftimer,
     dorfzusatz = $dorfzusatz,
     dorfstichwahltimer = $dorfstichwahltimer,
     dorfstichwahlzusatz = $dorfstichwahlzusatz");
    //Fertig upgedated ;)
  }
}

function spielInitialisieren($mysqli,$spielerzahl)
{
  $spielID = $_COOKIE['SpielID'];
  //Zuerst überprüfen, ob mindestens 2 Spieler mitspielen
  if ($spielerzahl < 2)
  {
    echo "<p class='error' >Zu wenig Spieler, um ein Spiel zu starten!</p>";
    return false;
  }
  $gameResult = $mysqli->Query("SELECT * FROM $spielID"."_game");
  $gameResAssoc = $gameResult->fetch_assoc();
  $werwolfzahl = $gameResAssoc['werwolfzahl'];
  $hexenzahl = $gameResAssoc['hexenzahl'];
  $jaegerzahl = $gameResAssoc['jaegerzahl'];
  $seherzahl = $gameResAssoc['seherzahl'];
  $amorzahl = $gameResAssoc['amorzahl'];
  $beschuetzerzahl = $gameResAssoc['beschuetzerzahl'];
  $parErmZahl = $gameResAssoc['parErmZahl'];
  $lykantrophenzahl = $gameResAssoc['lykantrophenzahl'];
  $spionezahl = $gameResAssoc['spionezahl'];
  $idiotenzahl = $gameResAssoc['idiotenzahl'];
  $pazifistenzahl = $gameResAssoc['pazifistenzahl'];
  $altenzahl = $gameResAssoc['altenzahl'];
  $urwolfzahl = $gameResAssoc['urwolfzahl'];
  $zufaelligeAuswahl = $gameResAssoc['zufaelligeAuswahl'];
  $zufaelligeAuswahlBonus = $gameResAssoc['zufaelligeAuswahlBonus'];
  
  //Zähle alle Charaktere zusammen und schaue, ob es mehr als die Spieleranzahl sind
  $besondereCharaktere = $werwolfzahl + $hexenzahl + $jaegerzahl + $seherzahl + $amorzahl + $beschuetzerzahl + $parErmZahl
    + $lykantrophenzahl + $spionezahl + $idiotenzahl + $pazifistenzahl + $altenzahl + $urwolfzahl;
  if ($besondereCharaktere > $spielerzahl && $zufaelligeAuswahl == 0)
  {
    echo "<p class='error' >Nicht genug Spieler für Ihre Spielkonfiguration</p>";
    return false;
  }
  
  //Schau, ob es zumindest einen "bösen" Charakter gibt...
  $boeseCharaktere = $werwolfzahl + $urwolfzahl;
  if ($boeseCharaktere < 1 && $zufaelligeAuswahl == 0)
  {
    echo "<p class='error' >Ein Spiel mit dieser Konfiguration ist nicht möglich. Haben Sie mindestens einen Werwolf ausgewählt?</p>";
    return false;
  }
  
  //Schau, dass es max einen Amor gibt
  if ($amorzahl < 0 || $amorzahl > 1)
  {
    echo "<p class='error' >Ein Spiel mit dieser Anzahl an Amor(s) ist nicht möglich ... Nur 1 oder 0 auswählen</p>";
    return false;
  }
  
  //Die basic-Tests sind mal bestanden: Setzen wir den Spielmodus auf 1: Spielsetup
  $mysqli->Query("UPDATE $spielID"."_game SET spielphase = ".PHASESPIELSETUP);
  
  //Jetzt muss jeder bestätigen, dass er dabei ist.
  //Dazu setzen wir mal alle bereit auf 0, außer den Spielleiter
  $eigeneID = $_COOKIE['eigeneID'];
  $mysqli->Query("UPDATE $spielID"."_spieler SET reload = 1, bereit = 0");
  $mysqli->Query("UPDATE $spielID"."_spieler SET bereit = 1 WHERE id = $eigeneID");
}

function setSpielerDefault($mysqli)
{
  //Setzt die Werte aus $spielID_spieler auf die Defaultwerte zurück
  $spielID = $_COOKIE['SpielID'];
  $mysqli->query("UPDATE $spielID"."_spieler SET
    lebt = 0,
    wahlAuf = -1,
    angeklagtVon = -1,
    nachtIdentitaet = 0,
    buergermeister = 0,
    hexeHeiltraenke = 0,
    hexeTodestraenke = 0,
    hexenOpfer = -1,
    hexeHeilt = 0,
    beschuetzerLetzteRundeBeschuetzt = -1,
    verliebtMit = -1,
    jaegerDarfSchiessen = 0,
    parErmEingesetzt = 0,
    buergermeisterDarfWeitergeben = 0;
  ");
}

function spielStarten($mysqli)
{
  $spielID = $_COOKIE['SpielID'];
  
  //Alle löschen, die nicht bereit sind
  $mysqli->Query("DELETE FROM $spielID"."_spieler WHERE bereit = 0");
  
  setSpielerDefault($mysqli);
  
  //aktualisiere Spielerzahl
  $spielerQuery = $mysqli->Query("SELECT * FROM $spielID"."_spieler");
  $spielerzahl = $spielerQuery->num_rows;
  
  //Überprüfe nocheinmal die Bedingungen
  $gameResult = $mysqli->Query("SELECT * FROM $spielID"."_game");
  $gameResAssoc = $gameResult->fetch_assoc();
  $werwolfzahl = $gameResAssoc['werwolfzahl'];
  $hexenzahl = $gameResAssoc['hexenzahl'];
  $jaegerzahl = $gameResAssoc['jaegerzahl'];
  $seherzahl = $gameResAssoc['seherzahl'];
  $amorzahl = $gameResAssoc['amorzahl'];
  $beschuetzerzahl = $gameResAssoc['beschuetzerzahl'];
  $parErmZahl = $gameResAssoc['parErmZahl'];
  $lykantrophenzahl = $gameResAssoc['lykantrophenzahl'];
  $spionezahl = $gameResAssoc['spionezahl'];
  $idiotenzahl = $gameResAssoc['idiotenzahl'];
  $pazifistenzahl = $gameResAssoc['pazifistenzahl'];
  $altenzahl = $gameResAssoc['altenzahl'];
  $urwolfzahl = $gameResAssoc['urwolfzahl'];
  $zufaelligeAuswahl = $gameResAssoc['zufaelligeAuswahl'];
  $zufaelligeAuswahlBonus = $gameResAssoc['zufaelligeAuswahlBonus'];
  
  //Zähle alle Charaktere zusammen und schaue, ob es mehr als die Spieleranzahl sind
  $besondereCharaktere = $werwolfzahl + $hexenzahl + $jaegerzahl + $seherzahl + $amorzahl + $beschuetzerzahl + $parErmZahl
    + $lykantrophenzahl + $spionezahl + $idiotenzahl + $pazifistenzahl + $altenzahl + $urwolfzahl;
  if (($besondereCharaktere > $spielerzahl && $zufaelligeAuswahl == 0)|| $spielerzahl < 2)
  {
    echo "<p class='error' >Nicht genug Spieler für Ihre Spielkonfiguration</p>";
    //Setze die Spielphase wieder auf 0
    $mysqli->Query("UPDATE $spielID"."_game SET spielphase = 0");
    $mysqli->Query("UPDATE $spielID"."_spieler SET reload = 1");
    return false;
  }
  
  //Schau, ob es zumindest einen "bösen" Charakter gibt...
  $boeseCharaktere = $werwolfzahl + $urwolfzahl;
  if ($boeseCharaktere < 1 && $zufaelligeAuswahl == 0)
  {
    echo "<p class='error' >Ein Spiel mit dieser Konfiguration ist nicht möglich. Haben Sie mindestens einen Werwolf ausgewählt?</p>";
    //Setze die Spielphase wieder auf 0
    $mysqli->Query("UPDATE $spielID"."_game SET spielphase = 0");
    $mysqli->Query("UPDATE $spielID"."_spieler SET reload = 1");
    return false;
  }

  //Setze alle auf lebend, und gib allen die Identität 0 = keine
  //Außerdem lösche das Playerlog
  $mysqli->Query("UPDATE $spielID"."_spieler SET lebt = 1, nachtIdentitaet = ". CHARKEIN .", playerlog = ''");
  
  //Ändere noch einige Werte im _game
  //Lösche das Log, und setze die Nachtanzahl auf 1
  $mysqli->Query("UPDATE $spielID"."_game SET log = '', nacht = 1");
  
  if ($zufaelligeAuswahl == 1)
  {
    $werwolfzahl = 0;
    if ($zufaelligeAuswahlBonus < -15 || $zufaelligeAuswahlBonus > 15)
    {
      echo "<p class='error' >Der Spielbonus ist zu hoch oder zu niedrig</p>";
      //Setze die Spielphase wieder auf 0
      $mysqli->Query("UPDATE $spielID"."_game SET spielphase = 0");
      $mysqli->Query("UPDATE $spielID"."_spieler SET reload = 1");
      return false;
    }
    
    toGameLog($mysqli,"Ein neues Spiel wird gestartet mit $spielerzahl Spielern. Die Charaktere werden zufällig verteilt.");
    $spielinfo = "Maximal im Spiel: Werwölfe: $spielerzahl, Hexen: $hexenzahl, Seher: $seherzahl, Jäger: $jaegerzahl, Amor: $amorzahl, 
      Beschützer: $beschuetzerzahl, Paranormaler Ermittler: $parErmZahl, Lykantrophen: $lykantrophenzahl,
      Spione: $spionezahl, Mordlustige: $idiotenzahl, Pazifisten: $pazifistenzahl, Die Alten: $altenzahl, Urwölfe: $urwolfzahl";
    toGameLog($mysqli,$spielinfo);
    toAllPlayerLog($mysqli,"Ein neues Spiel wird gestartet mit $spielerzahl Spielern. Die Charaktere werden zufällig verteilt.");
    toAllPlayerLog($mysqli,$spielinfo); 
    //Starte eine zufällige Verteilung der Charaktere
    
    //Das sind die Gewichtungen, von allen Charakteren werden die Zahlen zusammengezählt, ein positiver Wert
    // bedeutet einen Vorteil für die Dorfbewohner, negativer Wert Vorteil für die Werwölfe
    $werwolfbonus = -6;
    $hexenbonus = +4;
    $jaegerbonus = +3;
    $seherbonus = +7;
    $amorbonus = -3;
    $beschuetzerbonus =+3;
    $parErmBonus =+3;
    $lykantrophenbonus = -1;
    $spionebonus = +6;
    $idiotenbonus = +1;
    $pazifistenbonus = -1;
    $altenbonus = 0;
    $urwolfbonus = -8;
    $dorfbewohnerbonus = +1;
    for ($i=0;$i<=1000;$i++)
    {
      //1000 Versuche
      $werwolfzahlN = rand(1,$spielerzahl/3);
      $hexenzahlN = rand(0,$hexenzahl);
      $jaegerzahlN = rand(0,$jaegerzahl);
      $seherzahlN = rand(0,$seherzahl);
      $amorzahlN = rand(0,$amorzahl);
      $beschuetzerzahlN = rand(0,$beschuetzerzahl);
      $parErmZahlN = rand(0,$parErmZahl);
      $lykantrophenzahlN = rand(0,$lykantrophenzahl);
      $spionezahlN = rand(0,$spionezahl);
      $idiotenzahlN = rand(0,$idiotenzahl);
      $pazifistenzahlN = rand(0,$pazifistenzahl);
      $altenzahlN = rand(0,$altenzahl);
      $urwolfzahlN = rand(0, $urwolfzahl);
      
      $dorfbewohnerzahlN = $spielerzahl - $werwolfzahlN - $hexenzahlN - $jaegerzahlN - $seherzahlN
        - $amorzahlN - $beschuetzerzahlN - $parErmZahlN - $lykantrophenzahlN - $spionezahlN
        - $idiotenzahlN - $pazifistenzahlN - $altenzahlN - $urwolfzahlN;
      //Jetzt überprüfe, ob die Aufteilung "fair ist"
      $aktBonus = $werwolfzahlN * $werwolfbonus
        + $hexenzahlN * $hexenbonus
        + $jaegerzahlN * $jaegerbonus
        + $seherzahlN * $seherbonus
        + $amorzahlN * $amorbonus
        + $beschuetzerzahlN * $beschuetzerbonus
        + $parErmZahlN * $parErmBonus
        + $lykantrophenzahlN * $lykantrophenbonus
        + $spionezahlN * $spionebonus
        + $idiotenzahlN * $idiotenbonus
        + $pazifistenzahlN * $pazifistenbonus
        + $altenzahlN * $altenbonus
        + $urwolfzahlN * $urwolfbonus
        + $dorfbewohnerzahlN * $dorfbewohnerbonus;
      if ($aktBonus + 1 <= $zufaelligeAuswahlBonus + 2 && $aktBonus + 1 >= $zufaelligeAuswahlBonus && $dorfbewohnerzahlN >= 0)
      {
        //Bonus ist um max. 1 abweichend --> succes!
        toGameLog($mysqli,"Zufällige Verteilung der Charaktere vorgenommen mit Bonus ". $aktBonus . ".");
        $werwolfzahl = $werwolfzahlN;
        $hexenzahl = $hexenzahlN;
        $jaegerzahl = $jaegerzahlN;
        $seherzahl = $seherzahlN;
        $amorzahl = $amorzahlN;
        $beschuetzerzahl = $beschuetzerzahlN;
        $parErmZahl = $parErmZahlN;
        $lykantrophenzahl = $lykantrophenzahlN;
        $spionezahl = $spionezahlN;
        $idiotenzahl = $idiotenzahlN;
        $pazifistenzahl = $pazifistenzahlN;
        $altenzahl = $altenzahlN;
        $urwolfzahl = $urwolfzahlN;
        break;
      }
    }
    if ($werwolfzahl == 0)
    {
      //Verteilung fehlgeschlagen!
      //Notverteilung
      toGameLog($mysqli,"Zufällige Verteilung der Charaktere fehlgeschlagen. Teile nur Werwölfe aus.");
      $werwolfzahl = round($spielerzahl/6)+1;
      $hexenzahl = 0;
      $jaegerzahl = 0;
      $seherzahl = 0;
      $amorzahl = 0;
      $beschuetzerzahl = 0;
      $parErmZahl = 0;
      $lykantrophenzahl = 0;
      $spionezahl = 0;
      $idiotenzahl = 0;
      $pazifistenzahl = 0;
      $altenzahl = 0;
      $urwolfzahl = 0;
    }
    $spielinfo = "Im Spiel befinden sich: Werwölfe: $werwolfzahl, Hexen: $hexenzahl, Seher: $seherzahl, Jäger: $jaegerzahl, Amor: $amorzahl, 
      Beschützer: $beschuetzerzahl, Paranormaler Ermittler: $parErmZahl, Lykantrophen: $lykantrophenzahl,
      Spione: $spionezahl, Mordlustige: $idiotenzahl, Pazifisten: $pazifistenzahl, Die Alten: $altenzahl, Urwölfe: $urwolfzahl";
    toGameLog($mysqli,$spielinfo);
  }
  else
  {
    //Logge den Spielstart
    toGameLog($mysqli,"Ein neues Spiel wird gestartet mit $spielerzahl Spielern.");
    $spielinfo = "Werwölfe: $werwolfzahl, Hexen: $hexenzahl, Seher: $seherzahl, Jäger: $jaegerzahl, Amor: $amorzahl, 
      Beschützer: $beschuetzerzahl, Paranormaler Ermittler: $parErmZahl, Lykantrophen: $lykantrophenzahl,
      Spione: $spionezahl, Mordlustige: $idiotenzahl, Pazifisten: $pazifistenzahl, Die Alten: $altenzahl, Urwölfe: $urwolfzahl";
    toGameLog($mysqli,$spielinfo);
    toAllPlayerLog($mysqli,"Ein neues Spiel wird gestartet mit $spielerzahl Spielern.");
    toAllPlayerLog($mysqli,$spielinfo);
  }

  //Teile die Charaktere aus!
  weiseCharakterZu($werwolfzahl,CHARWERWOLF,$mysqli);
  weiseCharakterZu($seherzahl,CHARSEHER,$mysqli);
  weiseCharakterZu($hexenzahl,CHARHEXE,$mysqli);
  weiseCharakterZu($jaegerzahl,CHARJAEGER,$mysqli);
  weiseCharakterZu($amorzahl,CHARAMOR,$mysqli);
  weiseCharakterZu($beschuetzerzahl,CHARBESCHUETZER,$mysqli);
  weiseCharakterZu($parErmZahl,CHARPARERM,$mysqli);
  weiseCharakterZu($lykantrophenzahl,CHARLYKANTROPH,$mysqli);
  weiseCharakterZu($spionezahl,CHARSPION,$mysqli);
  weiseCharakterZu($idiotenzahl,CHARMORDLUSTIGER,$mysqli);
  weiseCharakterZu($pazifistenzahl,CHARPAZIFIST,$mysqli);
  weiseCharakterZu($altenzahl,CHARALTERMANN,$mysqli);
  weiseCharakterZu($urwolfzahl, CHARURWOLF, $mysqli);
  
  //setze verschiedene Startwerte:
  //Bei allen Hexen setze die Heiltränke und Todestränke auf 1
  //Alle Spieler, die keinen Charakter haben, erhalten Dorfbewohner
  $res = $mysqli->Query("SELECT * FROM $spielID"."_spieler");
  while ($temp = $res->fetch_assoc())
  {
    $i = (int)$temp['id'];
    if ($temp['nachtIdentitaet']==CHARHEXE)
    {
      //4 = Hexe
      $mysqli->Query("UPDATE $spielID"."_spieler SET hexeHeiltraenke = 1, hexeTodestraenke = 1 WHERE id = $i");
    }
    elseif ($temp['nachtIdentitaet']==CHARBESCHUETZER)
    {
      //Beschützer
      $mysqli->Query("UPDATE $spielID"."_spieler SET beschuetzerLetzteRundeBeschuetzt = -1 WHERE id = $i");
    }
    elseif ($temp['nachtIdentitaet']==CHARPARERM)
    {
      $mysqli->Query("UPDATE $spielID"."_spieler SET parErmEingesetzt = 0 WHERE id = $i");
    }
    elseif ($temp['nachtIdentitaet']==CHARKEIN)
    {
      //Weise dem Spieler Dorfbewohner zu!
      $mysqli->Query("UPDATE $spielID"."_spieler SET nachtIdentitaet = ". CHARDORFBEWOHNER ." WHERE id = $i");
    }
    elseif ($temp['nachtIdentitaet'] == CHARURWOLF)
    {
      $mysqli->Query("UPDATE $spielID"."_spieler SET urwolf_anzahl_faehigkeiten = 1 WHERE id = $i");    
    }
  }
  $neuePhase = PHASENACHT3;
  if ($amorzahl >= 1)
  {
    $neuePhase = PHASENACHT1;
  }
  else
  {
    //Wenn es keinen Amor gibt, Spielphase "Nacht3"
    $neuePhase = PHASENACHT3;
  }
  $mysqli->Query("UPDATE $spielID"."_game SET spielphase = $neuePhase");
  $mysqli->Query("UPDATE $spielID"."_spieler SET reload = 1, bereit = 0");
  phaseInitialisieren($neuePhase,$mysqli);
}

function weiseCharakterZu($anzahlSpieler,$identitaet,$mysqli)
{
  //anzahlSpieler: Wieviele Spieler diesen Charakter erhalten sollen
  //Wichtig ist nicht die Spielerzahl, sondern die höchste ID
  $spielID = $_COOKIE['SpielID'];
  
  $freieSpieler = $mysqli->Query("SELECT * FROM $spielID"."_spieler WHERE nachtIdentitaet = ". CHARKEIN ." ORDER BY RAND()");
  for ($i = 0; $i < $anzahlSpieler; $i++)
  {
    $temp = $freieSpieler->fetch_assoc();
    //Diesem Spieler die Identität zuweisen
    $id = (int)$temp['id'];
    //Mache ihn zu dem Charakter
    $identitaet = (int)$identitaet;
    $mysqli->Query("UPDATE $spielID"."_spieler SET nachtIdentitaet = $identitaet WHERE id = $id");
    //echo "<BR>Weise $id die Identität $identitaet zu";
    toGameLog($mysqli,"Weise ".getName($mysqli,$id)." die Nachtidentitaet ".nachtidentitaetAlsString($identitaet)." zu.");
  }
  
}

function phaseInitialisieren($phase,$mysqli)
{
  //Wird am Beginn jeder Phase aufgerufen und initialisert sie
  $spielID = $_COOKIE['SpielID'];
  if ($phase == PHASENACHTBEGINN)
  {
    //Bereite timer vor, nachdem die Nacht beginnt ...
    $countdownBis = time()+5;
    $countdownAb = time();
    $mysqli->Query("UPDATE $spielID"."_spieler SET countdownBis = $countdownBis, countdownAb = $countdownAb WHERE lebt = 1");
  }
  elseif ($phase == PHASENACHT1)
  {
    //Neue verliebte ... Setze alle verliebten auf -1
    $mysqli->Query("UPDATE $spielID"."_spieler SET verliebtMit = -1");
  }
  elseif ($phase == PHASENACHT2)
  {
  }
  elseif ($phase == PHASENACHT3)
  {
    //Setze wahlAuf auf -1 bei allen, damit die Werwölfe mit einer neuen Abstimmung starten
    $mysqli->Query("UPDATE $spielID"."_spieler SET wahlAuf = -1");
    $gameAssoc = gameAssoc($mysqli);
    //Bei weniger als 2 Werwölfen kann werwolfeinstimmig gleich auf 0 gesetzt werden.
    $werwolfQ = $mysqli->Query("SELECT * FROM $spielID"."_spieler WHERE (nachtIdentitaet = ". CHARWERWOLF ." OR nachtIdentitaet = ".CHARURWOLF.") AND lebt = 1");
    $werwolfzahl = $werwolfQ->num_rows;
    if ($werwolfzahl > 2)
    {
      $mysqli->Query("UPDATE $spielID"."_game SET werwolfeinstimmig = 1");
      $countdownBis = time()+$gameAssoc['werwolftimer1']+$gameAssoc['werwolfzusatz1']*$werwolfzahl;
      if ($countdownBis >= time()+40)
        $countdownAb = time()+20;
      else
        $countdownAb = time();
      $mysqli->Query("UPDATE $spielID"."_spieler SET countdownBis = $countdownBis, countdownAb = $countdownAb WHERE (nachtIdentitaet = ".CHARWERWOLF." OR nachtIdentitaet = ".CHARURWOLF.")");
    }
    else
    {
      $mysqli->Query("UPDATE $spielID"."_game SET werwolfeinstimmig = 0");
      $countdownBis = time()+$gameAssoc['werwolftimer2']+$gameAssoc['werwolfzusatz2']*$werwolfzahl;
      if ($countdownBis >= time()+15)
        $countdownAb = time()+5;
      else
        $countdownAb = time();
      $mysqli->Query("UPDATE $spielID"."_spieler SET countdownBis = $countdownBis, countdownAb = $countdownAb WHERE (nachtIdentitaet = ".CHARWERWOLF." OR nachtIdentitaet = ".CHARURWOLF.")");
    }
    
  }
  elseif ($phase == PHASENACHTENDE)
  {
    //Timer, bis die Toten bekanntgegeben werden
    //Bereite timer vor, nachdem die Toten bekanntgegeben werden ...
    $countdownBis = time()+5;
    $countdownAb = time();
    $mysqli->Query("UPDATE $spielID"."_spieler SET countdownBis = $countdownBis, countdownAb = $countdownAb WHERE lebt = 1");
  }
  elseif ($phase == PHASETOTEBEKANNTGEBEN)
  {
    //Schau nach, wer diese Nacht gestorben ist und bereite den Tagestext vor
    
    //Eine Liste an Toten
    $tote[-1] = -1;
    
    //Zuerst mal schauen, ob das Opfer der Werwölfe gestorben ist.
    $gameA = gameAssoc($mysqli);
    $werwolfopfer = (int)$gameA['werwolfopfer'];
    if ($werwolfopfer > -1)
    {
      //Die Werwölfe haben ein Opfer ausgewählt
      //Nachschauen, ob die Hexe ihren Heiltrank eingesetzt hat
      $res = $mysqli->Query("SELECT * FROM $spielID"."_spieler WHERE nachtIdentitaet = ". CHARHEXE ." AND lebt = 1 AND hexeHeilt = 1");
      //Falls das zumindest eine Reihe zurückliefert, hat die Hexe geheilt
      if ($res->num_rows > 0)
      {
        //Hexe hat geheilt
      }
      else
      {
        //Nachschauen, ob nicht vom Beschützer beschützt
        $leibwRes = $mysqli->Query("SELECT * FROM $spielID"."_spieler WHERE nachtIdentitaet = ". CHARBESCHUETZER ." AND lebt = 1 AND beschuetzerLetzteRundeBeschuetzt = $werwolfopfer");
        if ($leibwRes->num_rows <= 0)
        {
          //Wurde nicht vom Leibwächter/Beschützer gerettet
          $tote[] = $werwolfopfer; //Töte diesen Spieler
        }
      } 
    }
    //Wir müssen auch nachschauen, ob die Hexe einen Todestrank eingesetzt hat
    $res = $mysqli->Query("SELECT * FROM $spielID"."_spieler WHERE nachtIdentitaet = ". CHARHEXE ." AND lebt = 1 AND hexenOpfer > -1");
    if ($res->num_rows > 0)
    {
      //Mindestens eine Hexe will jemanden töten
      while ($temp = $res->fetch_assoc())
      {
        //Nachschauen, ob nicht vom Leibwächter/Beschützer beschützt ...
        $leibwRes = $mysqli->Query("SELECT * FROM $spielID"."_spieler WHERE nachtIdentitaet = ". CHARBESCHUETZER ." AND lebt = 1 AND beschuetzerLetzteRundeBeschuetzt = ".$temp['hexenOpfer']);
        if ($leibwRes->num_rows <= 0)
        {
          //nicht beschützt ...
          //toete diesen Spieler
          $tote[] = $temp['hexenOpfer'];
        }
      }
    }
    
    //Nachschauen, ob wir der Alte stirbt
    $alteRes = $mysqli->Query("SELECT * FROM $spielID"."_spieler WHERE nachtIdentitaet = ". CHARALTERMANN ." AND lebt = 1");
    if ($alteRes->num_rows > 0)
    {
      //Nachschauen, wie viele Werwölfe noch leben
      $werwolfQuery = $mysqli->Query("SELECT * FROM $spielID"."_spieler WHERE nachtIdentitaet = ". CHARWERWOLF ." AND lebt = 1");
      $werwolfzahl = $werwolfQuery->num_rows;
      $nacht = $gameA['nacht'];
      if ($nacht >= $werwolfzahl +1)
      {
        //Töte alle Alten
        while ($temp = $alteRes->fetch_assoc())
        {
          $tote[] = $temp['id'];
        }
      }
    }
    
    //entferne doppelte Einträge:
    $tote = array_unique($tote, SORT_NUMERIC);

    //Nun töte alle Toten und schreib die Namen in einen String zwecks Anzeige
    $mysqli->query("UPDATE $spielID"."_spieler SET dieseNachtGestorben = 0");
    $anzeigeString = "Am Morgen findet das Dorf folgende Tote: ";
    foreach ($tote as $i => $id)
    {
      if ($id > -1)
      {
        toeteSpieler($mysqli,$id);
      }
    }
    
    $toteQuery = $mysqli->Query("SELECT * FROM $spielID"."_spieler WHERE dieseNachtGestorben = 1 ORDER BY RAND();");
    //Fals keiner getötet wurde:
    if ($toteQuery->num_rows < 1)
    {
      //keiner wurde getötet
      $anzeigeString = "Diese Nacht ist niemand gestorben.";
    }
    else
    {
      while ($temp = $toteQuery->fetch_assoc())
      {
        $anzeigeString = $anzeigeString.getName($mysqli,$temp['id'])."<BR>";
        if ($gameA['charaktereAufdecken'] == 1)
        {
          //Die Charaktere werden aufgedeckt
          $anzeigeString = $anzeigeString.getName($mysqli,$temp['id'])." war ".nachtidentitaetAlsString($temp['nachtIdentitaet'])."<BR>";
        }
      }
    }
    
    //Jetzt lösche noch die Zahlen, die in diesem Schritt gebraucht wurden
    $mysqli->Query("UPDATE $spielID"."_spieler SET hexeHeilt = 0, hexenOpfer = -1");
    $stmt = $mysqli->prepare("UPDATE $spielID"."_game SET werwolfopfer = -1, tagestext = ?");
    $stmt->bind_param('s',$anzeigeString);
    $stmt->execute();
    $stmt->close();
    //Schaue nach, ob wir schon einen Sieger haben
    checkeSiegbedingungen($mysqli);
  }
  elseif ($phase == PHASEBUERGERMEISTERWAHL)
  {
    //Bürgermeisterwahl
    //Setze wahlAuf bei allen auf -1, damit alle neu starten
    $mysqli->Query("UPDATE $spielID"."_spieler SET wahlAuf = -1");
  }
  elseif ($phase == PHASEDISKUSSION)
  {
    //gibt nichts zu aktualisieren
  }
  elseif ($phase == PHASEANKLAGEN)
  {
    //Anklagen
    $mysqli->Query("UPDATE $spielID"."_spieler SET wahlAuf = -1, angeklagtVon = -1");
  }
  elseif ($phase == PHASEABSTIMMUNG)
  {
    //Abstimmung
    
    //Timer, ab der die Abstimmung ungültig ist
    $gameAssoc = gameAssoc($mysqli);
    $dorfQ = $mysqli->Query("SELECT * FROM $spielID"."_spieler WHERE lebt = 1");
    $dorfbewohnerzahl = $dorfQ->num_rows;
    $countdownBis = time()+$gameAssoc['dorftimer']+$gameAssoc['dorfzusatz']*$dorfbewohnerzahl;
    if ($countdownBis >= time()+150)
      $countdownAb = time()+90;
    else
      $countdownAb = time();

    $mysqli->Query("UPDATE $spielID"."_spieler SET wahlAuf = -1, countdownBis = $countdownBis, countdownAb = $countdownAb WHERE lebt = 1");
  }
  elseif ($phase == PHASESTICHWAHL)
  {
    //Stichwahl
    $mysqli->Query("UPDATE $spielID"."_spieler SET wahlAuf = -1");
    $gameAssoc = gameAssoc($mysqli);
    $dorfQ = $mysqli->Query("SELECT * FROM $spielID"."_spieler WHERE lebt = 1");
    $dorfbewohnerzahl = $dorfQ->num_rows;
    $countdownBis = time()+$gameAssoc['dorfstichwahltimer']+$gameAssoc['dorfstichwahlzusatz']*$dorfbewohnerzahl;
    if ($countdownBis >= time()+60)
      $countdownAb = time()+30;
    else
      $countdownAb = time();
    $mysqli->Query("UPDATE $spielID"."_spieler SET wahlAuf = -1, countdownBis = $countdownBis, countdownAb = $countdownAb WHERE lebt = 1");
  }
}

function toeteSpieler($mysqli, $spielerID)
{
  //Wird aufgerufen, wenn dieser Spieler stirbt
  $spielID = $_COOKIE['SpielID'];
  $eigeneID = $_COOKIE['eigeneID'];
  $spielerID = (int)$spielerID;
  //Nachschauen, ob es der Jäger ist ...
  $res = $mysqli->Query("SELECT * FROM $spielID"."_spieler WHERE id = $spielerID");
  $temp = $res->fetch_assoc();
  if ($temp['nachtIdentitaet'] == CHARJAEGER)
  {
    //Der Jäger wurde getötet
    $mysqli->Query("UPDATE $spielID"."_spieler SET jaegerDarfSchiessen = 1 WHERE id = $spielerID");
    toGameLog($mysqli,"Der Jäger wurde getötet.");
  }
  
  //Nachschauen, ob er der Bürgermeister ist ... und wir Bürgermeister weitergeben aktiviert haben....
  if ($temp['buergermeister'] == 1)
  {
    $gameAssoc = gameAssoc($mysqli);
    if ($gameAssoc['buergermeisterWeitergeben']==1)
    {
      //Der alte Bürgermeister wählt den aus, an den er den Bürgermeister weitergeben will
      toGameLog($mysqli, "Der Bürgermeister wurde getötet. Er darf dieses Amt weitergeben ...");
      $mysqli->Query("UPDATE $spielID"."_spieler SET buergermeisterDarfWeitergeben = 1 WHERE id = $spielerID");
    }
  }
  
  //Töte den Spieler
  $mysqli->Query("UPDATE $spielID"."_spieler SET lebt = 0, buergermeister = 0, dieseNachtGestorben = 1 WHERE id = $spielerID");
  
  //Schreibe noch ins log
  //Nachschauen, ob Charaktere aufgedeckt werden
  $gameA = gameAssoc($mysqli);
  if ($gameA['charaktereAufdecken']==1)
  {
    toAllPlayerLog($mysqli,getName($mysqli,$spielerID)."(".nachtidentitaetAlsString($temp['nachtIdentitaet']).") stirbt.");
  }
  else
  {
    toAllPlayerLog($mysqli,getName($mysqli,$spielerID)." stirbt.");
  }
  toGameLog($mysqli,getName($mysqli,$spielerID)."(".nachtidentitaetAlsString($temp['nachtIdentitaet']).") stirbt.");
  
  //Nachschauen, ob der Spieler verliebt war, denn dann stirbt der andere auch
  if ($temp['verliebtMit']>-1)
  {
    //Ist verliebt, schauen, ob der andere noch lebt...
    $verliebtRes = $mysqli->Query("SELECT * FROM $spielID"."_spieler WHERE lebt = 1 AND id = ".$temp['verliebtMit']);
    if ($verliebtRes->num_rows > 0)
    {
      toGameLog($mysqli,"Der Verliebte stirbt mit dem anderen.");
      //Er lebt noch --> töte ihn
      toeteSpieler($mysqli,$temp['verliebtMit']);
    }
  }
  checkeSiegbedingungen($mysqli);
}

function warteAufAndere()
{
  //Zeigt das warteAufAnder an, damit es bei jedem gleich aussieht
  echo "<h3 >Warte auf andere Spieler</h3>";
}

function dorfbewohnerWeiterschlafen()
{
  //Zeigt einen kurzen Text an und einen Button, den jeder drücken muss, damit es weitergeht.
  $text = getDorfbewohnerText();
  echo '<form action="Werwolf.php" method="post">
      <input type="hidden" name="weiterschlafen" value=1 />
      <p id = "normal" align = "center">'.$text.'</p>
      <p id = "normal" align = "center"><input class="weiterschlafen-btn" type="submit" value = "Weiterschlafen"/></p>
    </form>';
}

function amorInitialisiere($mysqli)
{
  //Zeigt ein Formular an, in dem Armor die beiden Verliebten auswählen kann
  $spielID = $_COOKIE['SpielID'];
  
  //Im Prinzip besteht das Formular aus zwei Listen aller Lebenden Spieler
  echo "<form action='Werwolf.php' method='post'>";
  echo '<input type="hidden" name="amorHatAusgewaehlt" value=1 />';
  echo "<p class='normal' >Welche beiden Spieler möchten Sie verlieben?</p>";
  echo "<p ><select name = 'amorID1' size = 1>";
  $alleSpielerRes = $mysqli->Query("SELECT * FROM $spielID"."_spieler WHERE lebt = 1");
  while ($temp = $alleSpielerRes->fetch_assoc())
  {
    echo "<option value = '".$temp['id']."'>".$temp['name']."</option>";
  }
  echo '</select></p>';
  echo "<p ><select name = 'amorID2' size = 1>";
  $alleSpielerRes = $mysqli->Query("SELECT * FROM $spielID"."_spieler WHERE lebt = 1");
  while ($temp = $alleSpielerRes->fetch_assoc())
  {
    echo "<option value = '".$temp['id']."'>".$temp['name']."</option>";
  }
  echo '</select></p>';
  echo '<p id = "normal" align = "center"><input type="submit" value = "Diese Personen verlieben"/></p></form>';
}

function amorGueltig($mysqli,$wahl1,$wahl2)
{
  $spielID = $_COOKIE['SpielID'];
  $eigeneID = $_COOKIE['eigeneID'];
  //Überprüfe, ob die Wahl gültig ist ...
  if ($wahl1 == $wahl2)
  {
    echo "<p class='error' >Zwei verschiedene Spieler auswählen ...</p>";
    return false;
  }
  if ($wahl1 < 0 || $wahl2 < 0)
    return false;
  
  //Die Wahl scheint gültig zu sein--> eintragen
  $mysqli->Query("UPDATE $spielID"."_spieler SET verliebtMit = $wahl1 WHERE id = $wahl2");
  $mysqli->Query("UPDATE $spielID"."_spieler SET verliebtMit = $wahl2 WHERE id = $wahl1");
  toPlayerLog($mysqli,"Sie haben ".getName($mysqli,$wahl1)." mit ".getName($mysqli,$wahl2)." verliebt.",$eigeneID);
  toGameLog($mysqli,"Amor hat ".getName($mysqli,$wahl1)." mit ".getName($mysqli,$wahl2)." verliebt.");
  toPlayerLog($mysqli,"Amor hat Sie mit ".getName($mysqli,$wahl1). " verliebt.",$wahl2);
  toPlayerLog($mysqli,"Amor hat Sie mit ".getName($mysqli,$wahl2). " verliebt.",$wahl1);
  return true;
}

function spionInitialisiere($mysqli)
{
  $spielID = $_COOKIE['SpielID'];
  
  //Zeige eine Liste aller lebenden Spieler an
  echo "<form action='Werwolf.php' method='post'>";
  echo '<input type="hidden" name="spionHatAusgewaehlt" value=1 />';
  echo "<p class='normal' >Sie als Spion(in) dürfen die Identität eines Spielers überprüfen. Welchen Spieler möchten Sie näher betrachten?</p>";
  echo "<p ><select name = 'spionID' size = 1>";
  $alleSpielerRes = $mysqli->Query("SELECT * FROM $spielID"."_spieler WHERE lebt = 1");
  while ($temp = $alleSpielerRes->fetch_assoc())
  {
    echo "<option value = '".$temp['id']."'>".$temp['name']."</option>";
  }
  echo '</select></p><p align="center" class="normal">Welche Identität glauben Sie, besitzt dieser Spieler?</p>';
  echo "<p ><select name = 'spionIdentitaet' size = 1>";
  //Alle Identitäten auflisten
  for ($i = 1; $i< 100; $i++)
  {
    $char = nachtidentitaetAlsString($i);
    if ($char != "")
    {
      echo "<option value = $i>$char</option>";
    }
  }
  echo '</select></p><p id = "normal" align = "center"><input type="submit" value = "Die Identität dieser Person prüfen"/></p></form>';
}

function spionSehe($mysqli, $id, $identitaet)
{
  $spielID = $_COOKIE['SpielID'];
  $eigeneID = $_COOKIE['eigeneID'];
  $id = (int)$id;
  //schauen, ob es ein valider Spieler ist
  $spielerRes = $mysqli->Query("SELECT * FROM $spielID"."_spieler WHERE id = $id AND lebt = 1");
  if ($spielerRes->num_rows < 1)
    return false;
  $spielerAssoc=$spielerRes->fetch_assoc();
  $strIdentitaet = nachtidentitaetAlsString($identitaet);
  if ($spielerAssoc['nachtIdentitaet']==$identitaet)
  {
     $text = $spielerAssoc['name']." ist ". $strIdentitaet .".";
  }
  else
  {
    $text = $spielerAssoc['name']." ist nicht ". $strIdentitaet .".";
  }
  echo "<h1 >$text</h1>";
  
  //Schreibe es auch ins playerlog, damit es der Spieler nachlesen kann
  toPlayerLog($mysqli, $text,$eigeneID);
  toGameLog($mysqli,"Der Spion/Die Spionin (".getName($mysqli,$eigeneID).") sieht: $text");
  
  //Setze mich noch auf bereit ;)
  $mysqli->Query("UPDATE $spielID"."_spieler SET bereit = 1 WHERE id = $eigeneID");
  return true;
}

function urwolfInitialisiere($mysqli)
{
  $spielID = $_COOKIE['SpielID'];
  
  //Zeige eine Liste aller lebenden Spieler an
  echo "<form action='Werwolf.php' method='post'>";
  echo '<input type="hidden" name="urwolfHatAusgewaehlt" value=1 />';
  echo "<p class='normal' >Sie als Urwolf/Urwölfin können einmal im Spiel einen anderen Spieler zum Werwolf machen. Wen wollen Sie wählen?<br>
  Wenn Sie niemanden zum Werwolf machen wollen, wählen sie 'Niemand' aus</p>";
  echo "<p ><select name = 'urwolfID' size = 1>";
  echo "<option value = '-1'>Niemand</option>";
  $alleSpielerRes = $mysqli->Query("SELECT * FROM $spielID"."_spieler WHERE lebt = 1");
  while ($temp = $alleSpielerRes->fetch_assoc())
  {
    echo "<option value = '".$temp['id']."'>".$temp['name']."</option>";
  }
  echo '</select></p><p id = "normal" align = "center"><input type="submit" value = "Diesen Spieler zum Werwolf machen"/></p></form>';
}

function urwolfHandle($mysqli, $id)
{
 
  $spielID = $_COOKIE['SpielID'];
  $eigeneID = $_COOKIE['eigeneID'];
  if ($id == -1)
  {
    $mysqli->Query("UPDATE $spielID"."_spieler SET bereit = 1 WHERE id = $eigeneID");
    return true; //NIEMAND
  }
  if ($mysqli->Query("SELECT * FROM $spielID"."_spieler WHERE id = $eigeneID")->fetch_assoc()['urwolf_anzahl_faehigkeiten'] > 0)
  {
    //schauen, ob es ein valider Spieler ist
    $stmt = $mysqli->prepare("SELECT * FROM $spielID"."_spieler WHERE id = ? AND lebt = 1");
    $stmt->bind_param('i',$id);
    $stmt->execute();
    $spielerRes = $stmt->get_result();
    if ($spielerRes->num_rows < 1)
      return false;
    $spielerAssoc=$spielerRes->fetch_assoc();
    $identitaet = $spielerAssoc['nachtIdentitaet'];
    $strIdentitaet = nachtidentitaetAlsString($identitaet);
    
    $text = $spielerAssoc['name']." ist jetzt ein Werwolf!";
    $stmt->close();
    echo "<h1 >$text</h1>";
    
    //Schreibe es auch ins playerlog, damit es der Spieler nachlesen kann
    toPlayerLog($mysqli, $text, $eigeneID);
    toPlayerLog($mysqli, "Sie wurden vom Urwolf/von der Urwölfin zu einem Werwolf gemacht. Sie verlieren alle bisherigen Fähigkeiten und spielen nun für die Werwölfe! Viel Erfolg!", $id);
    toGameLog($mysqli,getName($mysqli,$eigeneID)."(Urwolf/Urwölfin)  macht ".$spielerAssoc['name']."($strIdentitaet) zum Werwolf.");
    
    $stmt = $mysqli->prepare("UPDATE $spielID"."_spieler SET nachtIdentitaet = ". CHARWERWOLF .", popup_text = 'Du wurdest vom Urwolf / von der Urwölfin zu einem Werwolf gemacht und spielst jetzt für die Werwölfe!' WHERE id = ?");
    $stmt->bind_param('i',$id);
    $stmt->execute();
    $stmt->close();
    
    //Setze mich noch auf bereit ;)
    $mysqli->Query("UPDATE $spielID"."_spieler SET bereit = 1, urwolf_anzahl_faehigkeiten = 0 WHERE id = $eigeneID");
    return true;
   }
   else
   {
        return false;
   }
}

function seherInitialisiere($mysqli)
{
  $spielID = $_COOKIE['SpielID'];
  
  //Zeige eine Liste aller lebenden Spieler an
  echo "<form action='Werwolf.php' method='post'>";
  echo '<input type="hidden" name="seherHatAusgewaehlt" value=1 />';
  echo "<p class='normal' >Sie als Seher(in) dürfen die Identität eines Spielers erfahren. Welchen Spieler möchten Sie näher betrachten?</p>";
  echo "<p ><select name = 'seherID' size = 1>";
  $alleSpielerRes = $mysqli->Query("SELECT * FROM $spielID"."_spieler WHERE lebt = 1");
  while ($temp = $alleSpielerRes->fetch_assoc())
  {
    echo "<option value = '".$temp['id']."'>".$temp['name']."</option>";
  }
  echo '</select></p><p id = "normal" align = "center"><input type="submit" value = "Die Identität dieser Person sehen"/></p></form>';
}

function seherSehe($mysqli, $id)
{
  //id = die Id des Spielers, der gesehen wird
  $id = (int)$id;
  $spielID = $_COOKIE['SpielID'];
  $eigeneID = $_COOKIE['eigeneID'];
  //schauen, ob es ein valider Spieler ist
  $spielerRes = $mysqli->Query("SELECT * FROM $spielID"."_spieler WHERE id = $id AND lebt = 1");
  if ($spielerRes->num_rows < 1)
    return false;
  $spielerAssoc=$spielerRes->fetch_assoc();
  //nachschauen, ob ich nur die Gesinnung sehen darf ...
  $gameAssoc = gameAssoc($mysqli);
  if ($gameAssoc['seherSiehtIdentitaet']==1)
  {
    if ($spielerAssoc['nachtIdentitaet']==CHARLYKANTROPH)
      $identitaet = nachtidentitaetAlsString(CHARWERWOLF);
    else
      $identitaet = nachtidentitaetAlsString($spielerAssoc['nachtIdentitaet']);
  }
  else
  {
    if ($spielerAssoc['nachtIdentitaet']==CHARLYKANTROPH)
      $identitaet = getGesinnung(CHARWERWOLF)." (Gesinnung)";
    else
      $identitaet = getGesinnung($spielerAssoc['nachtIdentitaet'])." (Gesinnung)";
  }
  echo "<h1 >".$spielerAssoc['name']." = $identitaet</h1>";
  
  //Schreibe es auch ins playerlog, damit es der Spieler nachlesen kann
  toPlayerLog($mysqli, $spielerAssoc['name']." = $identitaet",$eigeneID);
  toGameLog($mysqli,"Der Seher/Die Seherin(".getName($mysqli,$eigeneID).") sieht die Nachtidentitaet von Spieler ".$spielerAssoc['name']." = $identitaet .");
  
  //Setze mich noch auf bereit ;)
  $mysqli->Query("UPDATE $spielID"."_spieler SET bereit = 1 WHERE id = $eigeneID");
  return true;
}

function beschuetzerInitialisiere($mysqli)
{
  $spielID = $_COOKIE['SpielID'];
  
  //Zeige an, wer letzte Nacht beschützt wurde...
  $eigeneAssoc = eigeneAssoc($mysqli);
  if ($eigeneAssoc['beschuetzerLetzteRundeBeschuetzt'] >= 0)
    echo "<p class='normal' >Letzte Nacht beschützten Sie ".getName($mysqli,$eigeneAssoc['beschuetzerLetzteRundeBeschuetzt'])."</p>";
  
  //Zeige eine Liste aller lebenden Spieler an
  echo "<form action='Werwolf.php' method='post'>";
  echo '<input type="hidden" name="beschuetzerHatAusgewaehlt" value=1 />';
  echo "<p class='normal' >Sie als Beschützer(in) dürfen einen Spieler diese Nacht beschützen (Auch Sie selbst)</p>";
  echo "<p ><select name = 'beschuetzerID' size = 1>";
  $alleSpielerRes = $mysqli->Query("SELECT * FROM $spielID"."_spieler WHERE lebt = 1");
  while ($temp = $alleSpielerRes->fetch_assoc())
  {
    echo "<option value = '".$temp['id']."'>".$temp['name']."</option>";
  }
  echo '</select></p><p id = "normal" align = "center"><input type="submit" value = "Diese Person beschützen"/></p></form>';
}

function beschuetzerAuswahl($mysqli,$id)
{
  $spielID = $_COOKIE['SpielID'];
  $eigeneID = $_COOKIE['eigeneID'];
  $id = (int)$id;
  //schauen, ob es ein valider Spieler ist
  $spielerRes = $mysqli->Query("SELECT * FROM $spielID"."_spieler WHERE id = $id AND lebt = 1");
  if ($spielerRes->num_rows < 1)
    return false;
  
  //Nachschauen, ob ich ihn nicht vorige Nacht bereits beschützt habe ...
  $eigeneAssoc = eigeneAssoc($mysqli);
  if ($eigeneAssoc['beschuetzerLetzteRundeBeschuetzt']==$id)
    return false;
  
  $gameAssoc = gameAssoc($mysqli);
  //Schreibe es auch ins playerlog, damit es der Spieler nachlesen kann
  toPlayerLog($mysqli, "In Nacht ".$gameAssoc['nacht']." beschützen Sie ".getName($mysqli,$id).".",$eigeneID);
  toGameLog($mysqli,"Der Beschützer/Die Beschützerin(".getName($mysqli,$eigeneID).") beschützt in Nacht ".$gameAssoc['nacht']." ".getName($mysqli,$id).".");
  
  //Setze mich noch auf bereit ;)
  setBereit($mysqli,$eigeneID,1);
  $mysqli->Query("UPDATE $spielID"."_spieler SET beschuetzerLetzteRundeBeschuetzt = $id WHERE id = $eigeneID");
  return true;
}

function parErmInitialisiere($mysqli)
{
  $spielID = $_COOKIE['SpielID'];
  
  echo "<p>Möchten Sie Ihre Fähigkeit als Paranormaler Ermittler einsetzen?</p>";
  //Zeige eine Liste aller lebenden Spieler an
  echo "<form action='Werwolf.php' method='post'>";
  echo '<input type="hidden" name="parErmHatAusgewaehlt" value=1 />';
  echo "<p class='normal' >Welchen Spieler + (lebende) Nachbarn wollen Sie näher beobachten?</p>";
  echo "<p ><select name = 'parErmID' size = 1>";
  $alleSpielerRes = $mysqli->Query("SELECT * FROM $spielID"."_spieler WHERE lebt = 1");
  while ($temp = $alleSpielerRes->fetch_assoc())
  {
    echo "<option value = '".$temp['id']."'>".$temp['name']."</option>";
  }
  echo '</select></p><p id = "normal" align = "center"><input type="submit" value = "Diese Person(en) beobachten"/></p></form>';
  
  echo "<form action='Werwolf.php' method = 'post'>
    <input type = 'hidden' name='parErmNichtAuswaehlen' value=1 />
    <p align= 'center'><input type='submit' value='Diese Runde nicht einsetzen'/></p></form>";
}

function parErmAusgewaehlt($mysqli, $id)
{
  $spielID = $_COOKIE['SpielID'];
  $eigeneID = $_COOKIE['eigeneID'];
  $id = (int)$id;
  //schauen, ob es ein valider Spieler ist
  $spielerRes = $mysqli->Query("SELECT * FROM $spielID"."_spieler WHERE id = $id AND lebt = 1");
  if ($spielerRes->num_rows < 1)
    return false;
  //Überprüfe, ob ich noch einsetzen darf
  $eigen = $mysqli->Query("SELECT * FROM $spielID"."_spieler WHERE id = $eigeneID AND parErmEingesetzt = 0");
  if ($eigen->num_rows <= 0)
    return false;  
  
  //Finde die ids der beiden Nachbarn heraus ...
  $alleRes = $mysqli->Query("SELECT * FROM $spielID"."_spieler WHERE lebt = 1");
  $nachbar1 = -1;
  $nachbar2 = -1;
  $erster = -1;
  $letzter = -1;
  while ($temp = $alleRes->fetch_assoc())
  {
    if ($erster == -1)
      $erster = $temp['id'];
    if ($temp['id']!=$id && $temp['id'] < $id)
      $nachbar1 = $temp['id'];
    if ($temp['id'] > $id && $nachbar2 == -1)
      $nachbar2 = $temp['id'];
    $letzter = $temp['id'];
  }
  if ($nachbar1 == -1)
  {
    //Wir waren der erste
    $nachbar1 = $letzter;
  }
  if ($nachbar2 == -1)
  {
    //Wir waren der letzte
    $nachbar2 = $erster;
  }
  $werwoelfe = $mysqli->Query("SELECT * FROM $spielID"."_spieler WHERE (nachtIdentitaet = ". CHARWERWOLF ." OR nachtIdentitaet = ". CHARLYKANTROPH ." OR nachtIdentitaet = ". CHARURWOLF .") AND (ID = $nachbar1 OR ID = $nachbar2 OR ID = $id)");
  if ($werwoelfe->num_rows > 0)
  {
    //Es ist zumindest ein Werwolf darunter
    toPlayerLog ($mysqli,"Sie beobachteten die Spieler ". getName($mysqli,$nachbar1) .", ". getName($mysqli,$id) ." und ".getName($mysqli,$nachbar2)." Es fällt Ihnen eine bedrohliche Aura auf, unter den dreien ist zumindest ein Werwolf.",$eigeneID);
    toGameLog($mysqli,"Der Paranormale Ermittler beobachtete die Spieler ". getName($mysqli,$nachbar1) .", ". getName($mysqli,$id) ." und ".getName($mysqli,$nachbar2).". Er sieht, dass sich unter ihnen zumindest ein Werwolf befindet.");
    echo ("<p align = 'center'>Sie beobachteten die Spieler ". getName($mysqli,$nachbar1) .", ". getName($mysqli,$id) ." und ".getName($mysqli,$nachbar2)." Es fällt Ihnen eine bedrohliche Aura auf, unter den dreien ist zumindest ein Werwolf.</p>");
  }
  else
  {
    toPlayerLog ($mysqli,"Sie beobachteten die Spieler ". getName($mysqli,$nachbar1) .", ". getName($mysqli,$id) ." und ".getName($mysqli,$nachbar2).". Es fällt Ihnen nichts Besonderes auf, unter den dreien befindet sich kein Werwolf.",$eigeneID);
    toGameLog($mysqli,"Der Paranormale Ermittler beobachtete die Spieler ". getName($mysqli,$nachbar1) .", ". getName($mysqli,$id) ." und ".getName($mysqli,$nachbar2).". Er sieht, dass sich unter ihnen kein Werwolf befindet.");
    echo ("<p align = 'center'>Sie beobachteten die Spieler ". getName($mysqli,$nachbar1) .", ". getName($mysqli,$id) ." und ".getName($mysqli,$nachbar2).". Es fällt Ihnen nichts Besonderes auf, unter den dreien befindet sich kein Werwolf.</p>");
  }
  
  $mysqli->Query("UPDATE $spielID"."_spieler SET parErmEingesetzt = 1 WHERE id = $eigeneID");
  return true;
}

function hexeInitialisieren($mysqli)
{
  $spielID = $_COOKIE['SpielID'];
  $eigeneID = $_COOKIE['eigeneID'];
  $eigeneAss = eigeneAssoc($mysqli);
  $heiltraenke = $eigeneAss['hexeHeiltraenke'];
  $todestraenke = $eigeneAss['hexeTodestraenke'];
  
  echo "<form action='Werwolf.php' method='post'>";
  echo '<input type="hidden" name="hexeAuswahl" value=1 />';
  //Der Hexe das Opfer der Werwölfe bekanntgeben
  $gameAss = gameAssoc($mysqli);
  if ($gameAss['werwolfopfer']!=-1)
  {
    echo "<p  class='normal'>Opfer der Werwölfe: ";
    echo getName($mysqli,$gameAss['werwolfopfer']);
    echo "</p>";
    
    //Schreibe es auch in das Hexe log
    $nacht = $gameAss['nacht'];
    $name = getName($mysqli,$gameAss['werwolfopfer']);
    toPlayerLog($mysqli,"In Nacht $nacht wählten die Werwölfe $name als Opfer.",$eigeneID);
    
    if ($heiltraenke > 0)
    {
      //Die Hexe fragen, ob sie das Opfer heilen will
      echo "<p ><select name = 'hexeHeilen' size = 1><option value = '0' selected=true>Das Opfer nicht heilen</option><option value = '1'>Das Opfer heilen</option></select></p>";
    }
  }
  //Die Hexe fragen, ob sie jemanden töten will, wenn sie denn noch einen Trank hat ...
  if ($todestraenke > 0)
  {
    echo "<p class='normal' >Sie dürfen Ihren Todestrank verwenden und jemanden töten. Wen wollen Sie töten?</p>";
    echo "<p ><select name = 'toeten' size = 1>";
    echo "<option value = '-1'>Niemanden</option>";
    $alleSpielerRes = $mysqli->Query("SELECT * FROM $spielID"."_spieler WHERE lebt = 1");
    while ($temp = $alleSpielerRes->fetch_assoc())
    {
      echo "<option value = '".$temp['id']."'>".$temp['name']."</option>";
    }
     echo '</select></p>';
  }
  echo '<p id = "normal" align = "center"><input type="submit" value = "So als Hexe die Nacht beenden"/></p></form>';
  echo "</form>";
}

function jaegerInitialisiere($mysqli)
{
  $spielID = $_COOKIE['SpielID'];
  $eigeneID = $_COOKIE['eigeneID'];
  
  //Zeige eine Liste aller noch lebenden an, die der Jäger töten kann
  echo "<form action='Werwolf.php' method='post'>";
  echo '<input type="hidden" name="jaegerHatAusgewaehlt" value=1 />';
  echo "<p class='normal' >Sie als Jäger(in) dürfen einen Spieler mit in den Tod reißen: </p>";
  echo "<p ><select name = 'jaegerID' size = 1>";
  $alleSpielerRes = $mysqli->Query("SELECT * FROM $spielID"."_spieler WHERE lebt = 1");
  while ($temp = $alleSpielerRes->fetch_assoc())
  {
    echo "<option value = '".$temp['id']."'>".$temp['name']."</option>";
  }
  echo '</select></p><p id = "normal" align = "center"><input type="submit" value = "Diesen Spieler töten"/></p></form>';
}

function buergermeisterInitialisiere($mysqli)
{
  //Der Bürgermeister wurde getötet und darf das Amt an seinen Nachfolger weitergeben...
  $spielID = $_COOKIE['SpielID'];
  $eigeneID = $_COOKIE['eigeneID'];
  
  //Zeige eine Liste aller noch Lebenden an, an die der Bürgermeister das Amt weitergeben kann
  echo "<form action='Werwolf.php' method='post'>";
  echo '<input type="hidden" name="buergermeisterNachfolger" value=1 />';
  echo "<p class='normal' >Sie als Bürgermeister(in) dürfen einen Spieler als Ihren Nachfolger bestimmen: </p>";
  echo "<p ><select name = 'buergermeisterID' size = 1>";
  $alleSpielerRes = $mysqli->Query("SELECT * FROM $spielID"."_spieler WHERE lebt = 1");
  while ($temp = $alleSpielerRes->fetch_assoc())
  {
    echo "<option value = '".$temp['id']."'>".$temp['name']."</option>";
  }
  echo '</select></p><p id = "normal" align = "center"><input type="submit" value = "Diesen Spieler als Nachfolger bestimmen"/></p></form>';
}

function phaseBeendenWennAlleBereit($phase,$mysqli)
{
  //Springt zur nächsten Phase, wenn alle bereit sind
  $spielID = $_COOKIE['SpielID'];
  //Schauen wir zuerst mal, ob schon alle bereit sind ...
  $nichtBereitResult = $mysqli->Query("SELECT * FROM $spielID"."_spieler WHERE bereit = 0 AND lebt = 1");
  if ($nichtBereitResult->num_rows > 0)
  {
    return false;
  }
  //Es sind wohl schon alle bereit
  if ($phase == PHASENACHT1)
  {
    $mysqli->Query("UPDATE $spielID"."_game SET spielphase = ".PHASENACHT2);
    //alle müssen reloaden
    $mysqli->Query("UPDATE $spielID"."_spieler SET reload = 1, bereit = 0");
    phaseInitialisieren(PHASENACHT2,$mysqli);
  }
  elseif ($phase == PHASENACHT2)
  {
    $mysqli->Query("UPDATE $spielID"."_game SET spielphase = ".PHASENACHT3);
    //alle müssen reloaden
    $mysqli->Query("UPDATE $spielID"."_spieler SET reload = 1, bereit = 0");
    phaseInitialisieren(PHASENACHT3,$mysqli);
  }
  elseif ($phase == PHASENACHT3)
  {
    //springen wir zu phase 5, wenn es eine Hexe gibt
    $gameResult = $mysqli->Query("SELECT * FROM $spielID"."_game");
    $gameResAssoc = $gameResult->fetch_assoc();
    $hexenzahl = $gameResAssoc['hexenzahl'];
    //Setze Phase auf 5
    $mysqli->Query("UPDATE $spielID"."_game SET spielphase = ".PHASENACHT4);
    //alle müssen reloaden
    $mysqli->Query("UPDATE $spielID"."_spieler SET reload = 1, bereit = 0");
    phaseInitialisieren(PHASENACHT4,$mysqli);
  }
  elseif ($phase == PHASENACHT4)
  {
    //Wir überspringen Phase 6 und kommen gleich zu Phase 7
    $mysqli->Query("UPDATE $spielID"."_game SET spielphase = ".PHASENACHTENDE);
    //alle müssen reloaden
    $mysqli->Query("UPDATE $spielID"."_spieler SET reload = 1, bereit = 0");
    phaseInitialisieren(PHASENACHTENDE,$mysqli);
  }
  elseif ($phase == PHASETOTEBEKANNTGEBEN)
  {
    //Wenn es keinen Bürgermeister gibt, zur Wahl des Bürgermeisters übergehen
    $bres = $mysqli->Query("SELECT * FROM $spielID"."_spieler WHERE buergermeister = 1 AND lebt = 1");
    if ($bres->num_rows > 0)
    {
      //Es gibt bereits einen Bürgermeister, wir können zu Phase 9 übergehen
      $mysqli->Query("UPDATE $spielID"."_game SET spielphase = ".PHASEDISKUSSION);
      //alle müssen reloaden
      $mysqli->Query("UPDATE $spielID"."_spieler SET reload = 1, bereit = 0");
      phaseInitialisieren(PHASEDISKUSSION,$mysqli);
    }
    else
    {
      //Es gibt keinen Bürgermeister --> Bürgermeisterwahl = Phase 8
      $mysqli->Query("UPDATE $spielID"."_game SET spielphase = ".PHASEBUERGERMEISTERWAHL);
      //alle müssen reloaden
      $mysqli->Query("UPDATE $spielID"."_spieler SET reload = 1, bereit = 0");
      phaseInitialisieren(PHASEBUERGERMEISTERWAHL,$mysqli);
    }
  }
  elseif ($phase == PHASEBUERGERMEISTERWAHL)
  {
    $mysqli->Query("UPDATE $spielID"."_game SET spielphase = ".PHASEDISKUSSION);
    //alle müssen reloaden
    $mysqli->Query("UPDATE $spielID"."_spieler SET reload = 1, bereit = 0");
    phaseInitialisieren(PHASEDISKUSSION,$mysqli);  
  }
  elseif ($phase == PHASEDISKUSSION)
  {
    //Gehe zur Anklage über
    $mysqli->Query("UPDATE $spielID"."_game SET spielphase = ".PHASEANKLAGEN);
    //alle müssen reloaden
    $mysqli->Query("UPDATE $spielID"."_spieler SET reload = 1, bereit = 0");
    phaseInitialisieren(PHASEANKLAGEN,$mysqli);
  }
  elseif ($phase == PHASEANKLAGEN)
  {
    //Gehe zur Abstimmung über
    $mysqli->Query("UPDATE $spielID"."_game SET spielphase = ".PHASEABSTIMMUNG);
    //alle müssen reloaden
    $mysqli->Query("UPDATE $spielID"."_spieler SET reload = 1, bereit = 0");
    phaseInitialisieren(PHASEABSTIMMUNG,$mysqli);
  }
}

function endeDerAbstimmungStichwahl($id1, $id2, $mysqli)
{
  //Stichwahl zwischen id1 und id2
  $spielID = $_COOKIE['SpielID'];
  $id1 = (int)$id1;
  $id2 = (int)$id2;
  //Diesmal gehe ich nicht über phaseBeendenWennAlleBereit...
  $mysqli->Query("UPDATE $spielID"."_spieler SET angeklagtVon = -1");
  $mysqli->Query("UPDATE $spielID"."_spieler SET angeklagtVon = 0 WHERE id = $id1");
  $mysqli->Query("UPDATE $spielID"."_spieler SET angeklagtVon = 0 WHERE id = $id2");
  //Gehe zur Stichwahl über = Phase 12
  $mysqli->Query("UPDATE $spielID"."_game SET spielphase = ".PHASESTICHWAHL);
  //alle müssen reloaden
  $mysqli->Query("UPDATE $spielID"."_spieler SET reload = 1, bereit = 0");
  phaseInitialisieren(PHASESTICHWAHL,$mysqli);
}

function endeDerStichwahl($id, $mysqli)
{
  $spielID = $_COOKIE['SpielID'];
  //Stichwahl ist beendet
  if ($id > -1)
  {
    toeteSpieler($mysqli,$id);
  }
  
  if (!checkeSiegbedingungen($mysqli))
  {
    //Gehe wieder zu Beginn der Nacht -> PHASENACHTBEGINN
    $mysqli->Query("UPDATE $spielID"."_game SET spielphase = ".PHASENACHTBEGINN);
    //alle müssen reloaden
    $mysqli->Query("UPDATE $spielID"."_spieler SET reload = 1, bereit = 0");
    
    //Zähle einen Tag dazu
    $gameAssoc = gameAssoc($mysqli);
    $nacht = $gameAssoc['nacht'];
    $nacht +=1;
    $mysqli->Query("UPDATE $spielID"."_game SET nacht = $nacht");
    phaseInitialisieren(PHASENACHTBEGINN,$mysqli);
  }
}

function endeDerAbstimmungEinfacheMehrheit($id, $mysqli)
{
  $spielID = $_COOKIE['SpielID'];
  //Ein Spieler mit der id $id wurde bei der Abstimmung des Dorfes im ersten Wahlgang für schuldig befunden.
  if ($id >-1)
  {
    toeteSpieler($mysqli,$id);
  }
  if (!checkeSiegbedingungen($mysqli))
  {
    //Gehe wieder zu Beginn der Nacht
    $mysqli->Query("UPDATE $spielID"."_game SET spielphase = ".PHASENACHTBEGINN);
    //alle müssen reloaden
    $mysqli->Query("UPDATE $spielID"."_spieler SET reload = 1, bereit = 0");
    
    //Zähle einen Tag dazu
    $gameAssoc = gameAssoc($mysqli);
    $nacht = $gameAssoc['nacht'];
    $nacht +=1;
    $mysqli->Query("UPDATE $spielID"."_game SET nacht = $nacht");
    phaseInitialisieren(PHASENACHTBEGINN,$mysqli);
  }
}

function writeGameToLog($mysqli)
{
  $spielID = $_COOKIE['SpielID'];
  $fileName = "log/Werwolf_log_".date("Y_m").".log";
  $myfile = fopen($fileName, "a");
  fwrite($myfile,"\n--- SPIEL BEENDET --- \n");
  fwrite($myfile,"SpielID: $spielID \n");
  fwrite($myfile,"SpielEnde: ".date("d.m.Y, H:i:s")."\n");
  //Alle Spieler hineinschreiben:
  fwrite($myfile,"Spieler:\n");
  $playerQ = $mysqli->Query("SELECT * FROM $spielID"."_spieler");
  while ($temp = $playerQ->fetch_assoc())
  {
    fwrite($myfile,$temp['name']."\n");
  }
  fwrite($myfile,"Spielverlauf:\n");
  $gameAssoc = gameAssoc($mysqli);
  $mitUmbruch = str_replace("<br>","\n",$gameAssoc['log']);
  fwrite($myfile,$mitUmbruch);
  
  //Schreibe noch die Überlebenden
  fwrite($myfile,"Die Überlebenden:\n");
  $lebendQuery = $mysqli->Query("SELECT * FROM $spielID"."_spieler WHERE lebt = 1");
  while ($temp = $lebendQuery->fetch_assoc())
  {
    fwrite($myfile,$temp['name']."(".nachtidentitaetAlsString($temp['nachtIdentitaet'],$mysqli).")\n");
  }
  fwrite($myfile,"--- ENDE DES SPIELLOGS ---\n");
  fclose($myfile);
}

function writeGameToLogSpielErstellen($mysqli, $spielID, $name)
{
  $fileName = "log/Werwolf_log_".date("Y_m").".log";
  $myfile = fopen($fileName, "a");
  fwrite($myfile,"\n--- NEUES SPIEL ERSTELLT --- \n");
  fwrite($myfile,"SpielID: $spielID \n");
  fwrite($myfile,"Zeit: ".date("d.m.Y, H:i:s")."\n");
  fwrite($myfile,"Name des Erstellers: $name \n");
  fclose($myfile);
}

function checkeSiegbedingungen($mysqli)
{
  $spielID = $_COOKIE['SpielID'];
  
  //Zuerst schauen, ob wir nicht bereits gewonnen haben ;)
  $gameAssoc = gameAssoc($mysqli);
  if ($gameAssoc['spielphase']==PHASESIEGEREHRUNG)
    return true;
  
  
  //Schaue, ob es keine Werwölfe mehr gibt
  $werwoelfeRes = $mysqli->Query("SELECT * FROM $spielID"."_spieler WHERE lebt = 1 AND (nachtIdentitaet = ".CHARWERWOLF. " OR nachtIdentitaet = ".CHARURWOLF.")");
  if ($werwoelfeRes->num_rows > 0)
  {
    //Es gibt noch Werwölfe, schaue, ob sie gewonnen haben
    $dorfbewohnerRes = $mysqli->Query("SELECT * FROM $spielID"."_spieler WHERE lebt = 1 AND nachtIdentitaet <> ".CHARWERWOLF. " AND nachtIdentitaet <> ".CHARURWOLF);
    if ($dorfbewohnerRes->num_rows <= 0)
    {
      //Die Werwölfe haben gewonnen ...
      toGameLog($mysqli,"Die Werwölfe haben gewonnen.");
      toAllPlayerLog($mysqli,"Die Werwölfe haben gewonnen.");
      $mysqli->Query("UPDATE $spielID"."_game SET spielphase = ". PHASESIEGEREHRUNG .", tagestext = 'Die Werwölfe haben gewonnen'");
      //alle müssen reloaden
      $mysqli->Query("UPDATE $spielID"."_spieler SET reload = 1, bereit = 0");
      writeGameToLog($mysqli);
      return true;
    }
  }
  else
  {
    //Die Dorfbewohner haben gewonnen
    toGameLog($mysqli,"Die Dorfbewohner haben gewonnen.");
    toAllPlayerLog($mysqli,"Die Dorfbewohner haben gewonnen.");
    $mysqli->Query("UPDATE $spielID"."_game SET spielphase = ". PHASESIEGEREHRUNG .", tagestext = 'Die Dorfbewohner haben gewonnen'");
    //alle müssen reloaden
    $mysqli->Query("UPDATE $spielID"."_spieler SET reload = 1, bereit = 0");
    writeGameToLog($mysqli);
    return true;
  }
  //Es hat nicht eine Gruppierung gewonnen ... Wie sieht es mit den Verliebten aus?
  //Schaue zuerst, ob es keine Verliebten mehr gibt ...
  $verliebteRes = $mysqli->Query("SELECT * FROM $spielID"."_spieler WHERE verliebtMit <> -1 AND lebt = 1");
  if ($verliebteRes->num_rows > 0)
  {
    //Wenn es noch genau zwei Spieler gibt, sind die beiden Spieler die Verliebten und sie haben gewonnen
    $alleRes = $mysqli->Query("SELECT * FROM $spielID"."_spieler WHERE lebt = 1");
    if ($alleRes->num_rows <= 2)
    {
      //Die verliebten haben gewonnen!
      toGameLog($mysqli,"Die Verliebten haben gewonnen.");
      toAllPlayerLog($mysqli,"Die Verliebten haben gewonnen.");
      $mysqli->Query("UPDATE $spielID"."_game SET spielphase = ". PHASESIEGEREHRUNG .", tagestext = 'Die Verliebten haben gewonnen'");
      //alle müssen reloaden
      $mysqli->Query("UPDATE $spielID"."_spieler SET reload = 1, bereit = 0");
      writeGameToLog($mysqli);
      return true;
    } 
  }
  return false;
}

function binTot()
{
  //Zeige einen Button an, mit dem die Spiellog datei angezeigt werden kann.
  $spielID = $_COOKIE['SpielID'];
  echo "<h3 >Sie sind leider bereits gestorben ...</h3>";
  echo "<p class='normal' >Verhalten Sie sich ruhig und stören Sie nicht das Spiel der anderen. Verraten Sie keine Informationen, damit die anderen Spieler ihr Spielerlebnis genießen können.</p>";
  echo "<form name='list'><div id='listdiv'></div></form>"; //Auch als Toter will ich eine Liste haben :)
  echo "<form name='gameLogForm' id='gameLogForm' style='display:none'><div id='gamelogdiv'></div></form>";
  echo "<input type='submit' value='spiellog anzeigen' onClick='showGameLog($spielID);'";
}

function diesesSpielLoeschenButton()
{ ?>
    <form action="Werwolf.php" method="post">
      <input type="hidden" name="spielLoeschen" value=1 />
      <p id = 'normal' align = "center"><input type="submit" value = "Das aktuelle Spiel beenden" onClick="if (confirm('Wollen Sie dieses Spiel wirklich aus Ihrer Liste Löschen? Sie können dann diesem Spiel nicht mehr beitreten')==false){return false;}"/></p>
    </form>
    <?php
}
function aus_spiel_entfernen($spielerID, $mysqli)
{
  $spielID = $_COOKIE['SpielID'];
  $spielerID = (int)$spielerID;
  $mysqli->Query("UPDATE $spielID"."_spieler SET lebt = 0 WHERE id = $spielerID");   
}

function alleReloadAusser($spielerID,$mysqli)
{
  $spielID = $_COOKIE['SpielID'];
  $spielerID = (int)$spielerID;
  $mysqli->Query("UPDATE $spielID"."_spieler SET reload = 1");
  $mysqli->Query("UPDATE $spielID"."_spieler SET reload = 0 WHERE id = $spielerID");
}

function setReloadZero($spielerID, $mysqli)
{
  $spielID = $_COOKIE['SpielID'];
  $spielerID = (int)$spielerID;
  $mysqli->Query("UPDATE $spielID"."_spieler SET reload = 0 WHERE id = $spielerID");
}

function setBereit($mysqli,$spielerID,$bereit)
{
  $spielID = $_COOKIE['SpielID'];
  $spielerID = (int)$spielerID;
  $bereit = (int)$bereit;
  $mysqli->Query("UPDATE $spielID"."_spieler SET bereit = $bereit WHERE id = $spielerID");
}

function gameAssoc($mysqli)
{
  $spielID = $_COOKIE['SpielID'];
  $gameRes = $mysqli->Query("SELECT * FROM $spielID"."_game");
  $gameA = $gameRes->fetch_assoc();
  return $gameA;
}

function eigeneAssoc($mysqli)
{
  $spielID = $_COOKIE['SpielID'];
  $eigeneID = $_COOKIE['eigeneID'];
  $eigeneRes = $mysqli->Query("SELECT * FROM $spielID"."_spieler WHERE id = $eigeneID");
  $eigeneA = $eigeneRes->fetch_assoc();
  return $eigeneA;
}

function getName($mysqli, $spielerID)
{
  //Gibt den Namen des Spielers mit der $spielerID zurück
  $spielID = $_COOKIE['SpielID'];
  $spielerID = (int)$spielerID;
  $res = $mysqli->Query("SELECT * FROM $spielID"."_spieler WHERE id = $spielerID");
  $temp = $res->fetch_assoc();
  return $temp['name'];
}

function getGesinnung($identitaet)
{
  if ($identitaet == CHARWERWOLF || $identitaet == CHARURWOLF)
    return "Werwölfe";
  else
    return "Dorfbewohner";
}

function nachtidentitaetAlsString($identitaet)
{
  switch($identitaet)
  {
    case CHARKEIN:
      return "keine";
      break;
    case CHARDORFBEWOHNER:
      return "Dorfbewohner";
      break;
    case CHARWERWOLF:
      return "Werwolf";
      break;
    case CHARSEHER:
      return "Seher/in";
      break;
    case CHARHEXE:
      return "Hexe/r";
      break;
    case CHARJAEGER:
      return "Jäger/in";
      break;
    case CHARAMOR:
      return "Amor";
      break;
    case CHARBESCHUETZER:
      return "Beschützer/in";
      break;
    case CHARPARERM:
      return "Paranormaler Ermittler";
      break;
    case CHARLYKANTROPH:
      return "Lykantroph/in";
      break;
    case CHARSPION:
      return "Spion/in";
      break;
    case CHARMORDLUSTIGER:
      return "Mordlustige(r)";
      break;
    case CHARPAZIFIST:
      return "Pazifist/in";
      break;
    case CHARALTERMANN:
      return "Die/Der Alte";
      break;
    case CHARURWOLF:
      return "Urwolf/Urwölfin";
      break;
    default:
      return "";
      break;
  }
}

function nachtidentitaetKurzerklaerung($identitaet)
{
  switch($identitaet)
  {
    case CHARKEIN:
      return "keine";
      break;
    case CHARDORFBEWOHNER:
      //Dorfbewohner
      return "Beunruhigt durch das Auftauchen von Werwölfen, versuchen die Dorfbewohner wieder Frieden in das Dorf zu bringen, indem sie alle Werwölfe ausforschen und töten wollen.";
      break;
    case CHARWERWOLF:
      //Werwolf
      return "Die Werwölfe töten jede Nacht einen Dorfbewohner, verhalten sich aber am Tag, als gehörten sie zu ihnen. Achtung: Die Dorfbewohner wollen den Werwölfen auf die Schliche kommen ...";
      break;
    case CHARSEHER:
      //Seher/in
      return "Sie können jede Nacht die Nachtidentität eines Spielers sehen. Alternative: Sie sehen, welcher Gruppe derjenige angehört";
      break;
    case CHARHEXE:
      //Hexe
      return "Sie können ein Mal im Spiel jemanden mit Ihrem Todestrank töten, ein Mal im Spiel das Opfer der Werwölfe retten. Entscheiden Sie weise, viel hängt davon ab ...";
      break;
    case CHARJAEGER:
      //Jäger/in
      return "Wenn Sie getötet werden, können Sie nach einem letzten Griff zu Ihrer Flinte einen anderen Spieler mit in den Tod reißen";
      break;
    case CHARAMOR:
      //Amor
      return "Zu Beginn des Spieles dürfen Sie zwei Personen bestimmen, die sich verlieben. Stirbt die eine Person, begeht die andere aus Kummer Selbstmord";
      break;
    case CHARBESCHUETZER:
      //Leibwächter/Beschützer
      return "Sie können jede Nacht einen Spieler beschützen, der in dieser Nacht nicht sterben kann (Sie können sich auch selbst wählen). Sie dürfen nicht zwei Nächte hintereinander dieselbe Person schützen.";
      break;
    case CHARPARERM:
      return "Sie können einmal im Spiel einen Spieler bestimmen und erfahren, ob sich unter diesem und den beiden Nachbarn zumindest ein Werwolf (oder Urwolf) befindet.";
      break;
    case CHARLYKANTROPH:
      return "Sie sehen aus wie ein Werwolf, sind aber keiner. Sie spielen also für die Dorfbewohner";
      break;
    case CHARSPION:
      //Spion
      return "Sie können jede Nacht einen Spieler auswählen und eine Identität, die dieser Spieler haben könnte. Sie erfahren, ob dieser
      Spieler tatsächlich diese Identität besitzt";
      break;
    case CHARMORDLUSTIGER:
      return "Sie wollen Blut sehen und argumentieren daher immer für das Töten eines Spielers";
      break;
    case CHARPAZIFIST:
      return "Sie wollen, dass alle möglichst friedlich zusammenleben und argumentieren daher immer gegen das Töten eines Spielers";
      break;
    case CHARALTERMANN:
      return "Sie sterben in der x. Nacht, wobei x die Anzahl der lebenden Werwölfe + 1 ist. Es kann also sein, dass sie früher sterben als gedacht ...";
      break;
    case CHARURWOLF:
      return "Sie gehören zu den Werwölfen und gewinnen bzw. verlieren mit ihnen. Einmal pro Spiel können Sie einen Spieler zum Werwolf machen, der dann alle bisherigen Fähigkeiten verliert ...";
      break;
  }
}

function toGameLog($mysqli,$logeintrag)
{
  //Fügt dem gamelog den $logeintrag hinzu
  $spielID = $_COOKIE['SpielID'];
  $gameAssoc = gameAssoc($mysqli);
  $aktLog = $gameAssoc['log'];
  $neuLog = $aktLog.date("H:i:s").": ".$logeintrag."<br>";
  $neuLog = str_replace("'",'"',$neuLog); //ersetze alle ' mit "
  $stmt = $mysqli->prepare("UPDATE $spielID"."_game SET log = ?");
  $stmt->bind_param("s",$neuLog);
  $stmt->execute();
  $stmt->close();
}

function toPlayerLog($mysqli, $logeintrag, $spieler)
{
  //Fügt dem Spielerlog des Spielers den logEintrag hinzu
  $spielID = $_COOKIE['SpielID'];
  $spieler = (int)$spieler;
  $res = $mysqli->Query("SELECT * FROM $spielID"."_spieler WHERE id = $spieler");
  $temp = $res->fetch_assoc();
  $aktLog = $temp['playerlog'];
  $neuLog = $aktLog.date("H:i:s").": ".$logeintrag."<BR>";
  $neuLog = str_replace("'",'"',$neuLog); //ersetze alle ' mit "
  $stmt = $mysqli->prepare("UPDATE $spielID"."_spieler SET playerlog = ? WHERE id = ?");
  $stmt->bind_param("si",$neuLog, $spieler);
  $stmt->execute();
  $stmt->close();
}

function toAllPlayerLog($mysqli, $logeintrag)
{
  //Fügt allen Spielern diesen Logeintrag hinzu
  $spielID = $_COOKIE['SpielID'];
  $res = $mysqli->Query("SELECT * FROM $spielID"."_spieler");
  while ($temp = $res->fetch_assoc())
  {
    toPlayerLog($mysqli,$logeintrag,$temp['id']);
  }
}

function playerLogButton($mysqli)
{
  //Zeigt einen Button an, auf den der Spieler klicken kann, um sein Playerlog zu sehen
  $eigeneAss = eigeneAssoc($mysqli);
  $myLog = $eigeneAss['playerlog'];
  echo "<p class='normal' ><input type='submit' value = 'Log anzeigen/verbergen' onClick='showHidePlayerLog();'/></p>";
  echo "<form id = 'PlayerLog' style='display: none;'>";
  echo "<p id = 'normal' >$myLog</p>";
  echo "</form>";
}

function characterButton($mysqli)
{
  //Zeigt einen Button an, auf den der Spieler klicken kann, dann wird sein Character angezeigt
  $eigeneAss = eigeneAssoc($mysqli);
  $charString = nachtidentitaetAlsString($eigeneAss['nachtIdentitaet']);
  $charErklaerung = nachtidentitaetKurzerklaerung($eigeneAss['nachtIdentitaet']);
  echo "<hr><p class='normal' ><input type='submit' value = 'Charakter anzeigen/verbergen' onClick='showHideCharacter();'/></p>";
  echo "<form id='CharacterInfo' style='display: none;'>";
  echo "<h3 >$charString</h3><p class='normal'>$charErklaerung</p>";
  echo "</form><hr><br>";
}

function getDorfbewohnerText()
{
  //Liefert verschiedene Texte zurück, die angezeigt werden, wenn der Weiterschlafen button aktiv wird
  $i = rand(0,20);
  $text = "";
  switch($i)
  {
    case 0:
      $text = "Du erwachst aus deinem Schlaf, siehst dich um, bemerkst aber nichts Außergewöhnliches.
      Du legst dich wieder hin und versuchst weiterzuschlafen. Was für ein seltsamer Traum ...";
      break;
    case 1:
      $text = "Schweißgebadet erwachst du ... doch nichts hat sich bewegt, alles ist so, wie es auch
      am Tag davor war ... oder etwa nicht? Irgendwie hast du ein seltsames Gefühl ...";
      break;
    case 2:
      $text = "Ein Albtraum war es, aus dem du erwachst ... Was hat dies zu bedeuten?
      Wie soll es mit dem Dorf weitergehen? Fragen über Fragen ...";
      break;
    case 3:
      $text = "Das ungute Gefühl verlässt dich auch im Schlaf nicht ... Bist du vielleicht der Nächste?
      Wer hat sich gegen dich verschworen? Wer will dich tot sehen? Alles Fragen über Fragen, auf die du keine Antwort kennst ...";
      break;
    case 4:
      $text = "Der Button mit der Aufschrift 'Log anzeigen' ermöglicht dir das Nachlesen von wichtigen Ereignissen,
      die sich bereits im Spiel ereignet haben. Außerdem sind darin bei verschiedenen Charakteren wichtige Informationen gespeichert,
      wie beispielsweise, wen der Seher aller gesehen hat ...";
      break;
    case 5:
      $text = "Der Bürgermeister / Die Bürgermeisterin hält eine gewisse Machtposition inne. Er/Sie entscheidet, wann von der Diskussion
      zur Anklage und dann zur Abstimmung übergegangen werden soll. Entscheidet also weise, wen ihr in diesem Amt sehen wollt.";
      break;
    case 6:
      $text = "Der Seher/Die Seherin kann jede Runde die Identität einer Person sehen ... Das könnte sich für die Dorfbewohenr als nützlich,
      für die Werwölfe aber als Bedrohung erweisen ...";
      break;
    case 7:
      $text = "Die Hexe kann einmal im Spiel jemanden töten, einmal im Spiel das Opfer der Werwölfe heilen. Sie ist somit ein starker
      Charakter auf Seiten der Dorfbewohner ... Mit ihrer Hilfe wird es möglich sein, die Werwölfe zu stoppen";
      break;
    case 8:
      $text = "Blutrünstige Werwölfe, die des Nachts über Unschuldige herfallen, haben das Dorf heimgesucht. Nun ist es an den Dorfbewohnern,
      die Werwölfe zu entlarven und sie ihrer gerechten Strafe zuzuführen ... Bedenkt aber, die Werwölfe verhalten sich am Tag wie
      Dorfbewohner und versuchen, jeden Verdacht von ihnen abzulenken ... Seid also wachsam...";
      break;
    case 9:
      $text = "Der Jäger / Die Jägerin ist ein Charakter mit erstaunlicher Reaktionszeit ... Wenn er den Tod an seine Tür pochen hört, kann
      er nach einem letzten Griff zu seiner Flinte einen anderen Spieler mit in den Tod reißen ... Der/Die Jäger(in) sollte jedoch weise
      entscheiden, wen er mit in den Tod reißt";
      break;
    case 10:
      $text = "Zu Beginn des Spieles kann Amor zwei Personen bestimmen, die sich verlieben. Stirbt eine der beiden, begeht die andere aus Kummer Selbstmor.
      Ziel der Verliebten ist es, mit ihrer Gruppierung (Dorfbewohner, Werwölfe) zu gewinnen, wenn sie der gleichen angehören.
      Sollten Sie verschiedenen Gruppierungen angehören, gewinnen sie, wenn sie alle anderen töten ... Also Achtung Dorfbewohner,
      vielleicht gibt es Verliebte unter euch, die nach eurem Untergang trachten ...";
      break;
    case 11:
      $text = "Die Dorfbewohner sind verzweifelt ... Wer wird der nächste sein? Wen aus ihrer Mitte werden die Werwölfe als nächstes
      zum Tode verdammen? Es ist ein riskantes Spiel, das sie treiben, und doch scheinen sie davonzukommen ... doch wie lange noch?";
      break;
    case 12:
      $text = "Der Paranormale Ermittler kann einmal im Spiel fühlen, ob sich unter drei benachbarten Personen ein Werwolf befindet.
      Können die Dorfbewohner mit seiner Hilfe den Werwölfen das Handwerk legen?";
      break;
    case 13:
      $text = "Der Lykantroph / Die Lykantrophin sieht bloß aus wie ein Werwolf, obwohl sie selbst keiner ist ...
      Eine gefährliche Tatsache ...";
      break;
    case 14:
      $text = "Der Spion / Die Spionin kann jede Nacht die Identität eines Spielers überprüfen. Er / Sie kann so herausfinden,
      ob der Spieler wirklich der ist, für den er sich ausgibt ...";
      break;
    case 15:
      $text = "Die/Der Mordlustige will Blut sehen und argumentiert daher immer für den Tod eines Spielers.";
      break;
    case 16:
      $text = "Der Pazifist / Die Pazifistin ist zutiefst unzufrieden und will nicht, dass überhaupt jemand getötet wird,
      daher argumentiert sie / er stets gegen das Töten eines Spielers";
      break;
    case 17:
      $text = "Die/Der Alte stirbt im Laufe des Spiels, und zwar abhängig davon, wie viele Werwölfe noch am Leben sind.";
      break;
    case 18:
      $text = "Der Urwolf / Die Urwölfin spielt gemeinsam mit den Werwölfen, kann aber einmal im Spiel einen Spieler zum Werwolf machen, der daraufhin alle seine bisherigen Fähigkeiten verliert.";
      break;
    default:
      $text = "Ein dunkler Schatten hat sich über das Dorf gelegt. Beunruhigt und verängstigt versuchen die übrig gebliebenen Dorfbewohner
      die drohende Gefahr der Werwölfe abzuwehren. Doch sie werden immer weniger. Schon wieder gab es ein Opfer aus ihren Reihen ...
      Wer ist unschuldig, und wer ein Lügner? Wer sagt die Wahrheit, wer steckt hinter alledem? Es sind düstere Zeiten, in denen
      das Dorf nun ums Überleben kämpfen muss ...";
      break;
  }
  return $text;
}


/*
Erklärungen:
Zu den Datenbank-Einträgen:
[ID]_Game

Spielphase  //ALT, jetzt über Konstanten gelöst
0: Setup -> Spieler suchen  (PHASESETUP)
1: Spielsetup -> jeder muss bestätigen, dass er dabei ist (PHASESPIELSETUP)
2: Nacht Teil 1: Amor (PHASENACHT1)
3: Nacht Teil 2: Verliebte (PHASENACHT2)
4: Nacht Teil 3: Alle bis Werwölfe  (PHASENACHT3)
5: Nacht Teil 4: Hexe (PHASENACHT4)
6: Nacht Teil 5: Weitergabe des Amuletts (PHASENACHT5)
7: Tag, Tote werden bekanntgegeben (PHASETOTEBEKANNTGEBEN)
8: Tag, Bürgermeisterwahl (PHASEBUERGERMEISTERWAHL)
9: Tag, Diskussion (PHASEDISKUSSION)
10: Tag, Anklagen (PHASEANKLAGEN)
11: Tag, Abstimmung (PHASEABSTIMMUNG)
12: Tag, Stichwahl der Abstimmung (PHASESTICHWAHL)
13: Tag, nach Abstimmung (PHASENACHABSTIMMUNG)
14: Siegerehrung (PHASESIEGEREHRUNG)

charaktereAufdecken
0: Die Charaktere werden nicht aufgedeckt
1: Die Charaktere werden aufgedeckt

buergermeisterWeitergeben
0: Beim Tod des Bürgermeisters wird ein neuer gewählt.
1: Beim Tod des Bürgermeisters entscheidet der Bürgermeister, wer sein Nachfolger wird.

werwolfzahl
Gibt die Anzahl der Werwölfe beim Spielsetup an

hexenzahl
Gibt die Anzahl der Hexen beim Spielsetup an

seherzahl
Gibt die Anzahl der Seher beim Spielsetup an

jaegerzahl
Gibt die Anzahl der Jäger beim Spielsetup an

amorzahl
Gibt die Anzahl der Amor(s) an (max 1)

letzterAufruf
gibt den letzten Aufruf an, kann später einmal verwendet werden, um alte Spiele zu löschen.

werwolfopfer
gibt das Opfer der Werwölfe an

log
Eine Log-Datei des gesamten Spiels
Diese Datei soll das Spiel nachvollziehbar machen

Nacht
gibt die Anzahl der Nächte seit Spielbeginn an

tagestext
Gibt den Text an, der in Phase 7 allen angezeigt wird
= Diese Nacht wurden getötet:
SpielerX
SpielerZ



[ID]_Spieler

Nachtidentitaet
0: keine (CHARKEIN)
1: Dorfbewohner (CHARDORFBEWOHNER)
2: Werwolf (CHARWERWOLF)
3: Seher (CHARSEHER)
4: Hexe (CHARHEXE)
5: Jäger (CHARJAEGER)
6: Amor  (CHARAMOR)
7: Leibwächter/Beschützer (CHARBESCHUETZER)
8: Paranormaler Ermittler  (CHARPARERM)
9: Lykantroph  (CHARLYKANTROPH)
10: Spion (CHARSPION)
11: Mordlustige(r), intern Idiot (CHARMORDLUSTIGER)
12: Pazifist (CHARPAZIFIST)
13: Alter Mann  (CHARALTERMANN)

hexenOpfer
Wen die Hexe töten will

hexeHeilt
0: Hexe heilt das Opfer der Werwölfe nicht
1: Hexe heilt das Opfer der Werwölfe

verliebtMit
mit wem dieser Spieler vom Amor verliebt wurde

jaegerDarfSchiessen
0: Nichts Besonderes
1: Der Jäger wurde getötet und darf jemanden mit in den Tod reißen

buergermeisterDarfWeitergeben
0: Nichts Besonderes
1: Der Bürgermeister wurde getötet und gibt sein Amt weiter...

playerlog
Hier werden Sachen hineingeschrieben, die sich der Spieler wieder anschaun können soll
z.B. als Seher wen er gesehen hat

SESSION-Variablen Übersicht
$_SESSION['SpielID'] gibt die ID des Spiels an
$_SESSION['eigeneID'] gibt die eigene ID an



ToDO:
#1: DONE: Spieler mit gleichem Namen dürfen sich nicht in einem Spiel befinden  DONE
#2: DONE: Verschiedene Texte der Dorfbewohner (einschlafen) DONE
#3: DONE: Seher sollte gleich reloaden ...  DONE
#4: DONE: Bei Anklagen nicht standardmäßig ein Spieler ausgewählt sein... DONE
#5: DONE: Bei der Abstimmung sollte ersichtlich sein, wieviele Stimmen jeder (Angeklagte) erhalten hat EVTL. DONE
#6: DONE: Wenn Bürgermeister in der Nacht stirbt, kommt es nicht zu einer neuen Abstimmung EVTL. DONE
#7: DONE: In showGameLog fehlen die Zeilenumbrüche ... DONE
#8: DONE: gameLog sollte auch wieder verborgen werden können ... DONE
#9: DONE: In gameLog werden keine Umlaute angezeigt  ... DONE [Encoding auf ISO-8859-1 umgestellt]
#10: DONE: Der eigene Name sollte angezeigt werden ... DONE
#11: DONE: Es sollte dem Bürgermeister angezeigt werden, dass er Bürgermeister ist ... DONE
#12: DONE: Beim Starten eines neuen Spieles sollten alte Spiele gelöscht werden ...
#13: Javascript, das beim Spielerstellen anzeigt, wieviele Charaktere ausgewählt wurden ...
#14: Die Spieler, die nicht Spielleiter sind, sollten sehen können, welche Regeln ausgewählt wurden ...
#15: DONE: Bei der Stimmenanzahl soll erkennbar sein, dass der Bürgermeister 2 Stimmen hat [EDIT: bzw. 1,5]
#16: DONE: Paranormalen Ermittler hinzufügen
#17: DONE: Alten Mann hinzufügen
#18: Trunkenbold hinzufügen
#19: Amulett des Schutzes hinzufügen
#20: Wolfsjunges hinzufügen
#21: Einsamen Wolf hinzufügen
#22: DONE: Lykantrophen hinzufügen
#23: DONE: Bürgermeister nur 1/2 Stimme geben, Fixen, dass Abstimmungen nicht zu früh abgebrochen werden.
#24: Abstimmungsergebnis anzeigen
#25: DONE: Bug beim Entfernen von Spielern
#26: Spieler sollten unter dem Spiel das Spiel verlassen können
#27: DONE: Den Verstorbenen eine Liste aller Spieler anzeigen
#28: Kultführer hinzufügen
#29: Strolch hinzufügen
#30: DONE: SQL injection unterbinden
#31: Hintergrundgrafik (verschieden Tag/Nacht)
#31: DONE: Als Option machen, dass niemand erfährt, wie die Charaktere verteilt sind
#32: Wenn jemand während des Spiels aussteigt, sollte das Spiel damit zurechtkommen
#33: DONE: Wenn einer der Verliebten stirbt, sollten beide am Tag auf der Totenliste erscheinen
#34: DONE: Beim Spielbeitritt sollte jeder Spieler ein vom Server zugeteiltes persönliches Passwort bekommen (Cookie), dass sich niemand anders für ihn ausgeben kann.
#35: DONE: Idiot und Pazifist hinzufügen [EDIT: Umbenennung von Idiot in Mordlustiger]
#36: DONE: "Seher" hinzufügen, der eine Identität überprüfen kann --> der "Spion"
#37: DONE: Umbenennen von Leibwächter in Beschützer
#38: DONE: Umbenennen von Idiot in Mordlustige(r)
#39: DONE: Timer einfügen, ab wann die Abstimmung am Tag zu keinem Ergebnis führt
#40: DONE: Timer einfügen, ab wann die Werwölfe nicht mehr einstimmig abstimmen müssen, zweiten Timer, ab wann kein Opfer gewählt wird
#41: Funktion für alle Buttons erstellen, dass die Buttons in Zukunft leicht mit Grafik ausgetauscht werdne können
#42: DONE: Timereinstellungen in den Spieleinstellungen bearbeiten können
#43: Zeige im Log an, wer wen anklagt
#44: DONE: Charaktere und Phasen durch Konstanten ersetzt, die in constants.php definiert werden
#45: DONE: Verbiete, dass sich jemand wie ein Charakter nennt (WERWOLF, HEXE, AMOR)
#46: Bots hinzufügen, die von einem "BotController"="Spieler, der für refreshen zuständig ist" zB ein Laptop
#47: DONE: Umstellen der Farben ermöglichen (v1.0.1, 30.12.2019)

*/
?>
