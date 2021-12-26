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
require_once('includes/functions.php');

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
  include "includes/includes.php"; //Datenbank
  include "includes/constants.php"; //Hier werden Konstanten für Phasen und Charaktere definiert
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
                          warteAufAndere($mysqli);
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
                                warteAufAndere($mysqli);
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
                              warteAufAndere($mysqli);
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
                          warteAufAndere($mysqli);
                          $pageReload = true;
                        }
                        else
                        {
                          if ($eigeneAssoc['verliebtMit'] > -1)
                          {
                            if (isset($_POST['verliebteWeiter']))
                            {
                              setBereit($mysqli,$eigeneID,1);
                              warteAufAndere($mysqli);
                              $pageReload = true;
                              phaseBeendenWennAlleBereit(PHASENACHT2,$mysqli);
                            }
                            else
                            {
                              //Zeige an, mit wem ich verliebt bin
                              echo "<form action='Werwolf.php' method='post'>";
                              echo "<p class='normal' >Der Pfeil des Amor, der nie sein Ziel verfehlt, trifft Sie und Sie verlieben sich unsterblich ...</p>";
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
                              warteAufAndere($mysqli);
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
                          warteAufAndere($mysqli);
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
                                warteAufAndere($mysqli);
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
                                warteAufAndere($mysqli);
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
                                warteAufAndere($mysqli);
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
                                  warteAufAndere($mysqli);
                                  $pageReload = true;
                                  phaseBeendenWennAlleBereit(PHASENACHT3,$mysqli);
                                }
                              }
                              elseif (isset ($_POST['parErmNichtAuswaehlen']))
                              {
                                //Diese Runde nicht auswählen
                                setBereit($mysqli,$eigeneID,1);
                                warteAufAndere($mysqli);
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
                                warteAufAndere($mysqli);
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
                              warteAufAndere($mysqli);
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
                          warteAufAndere($mysqli);
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
                                warteAufAndere($mysqli);
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
                                  warteAufAndere($mysqli);
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
                              warteAufAndere($mysqli);
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
                              warteAufAndere($mysqli);
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
                                warteAufAndere($mysqli);
                              }
                            }
                          }
                          else
                          {
                            //Ich bin nicht Bürgermeister --> Warte, bis es weitergeht
                            $pageReload = true;
                            setBereit($mysqli,$eigeneID,1);
                            phaseBeendenWennAlleBereit(PHASETOTEBEKANNTGEBEN,$mysqli);
                            warteAufAndere($mysqli);
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
                              warteAufAndere($mysqli);
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
                                warteAufAndere($mysqli);
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
                      `inaktivzeit` INT ( 10 ) DEFAULT 40 ,
                      `inaktivzeitzusatz` INT ( 10 ) DEFAULT 0 ,
                      `tagestext` TEXT ,
                      `nacht` INT ( 5 ) DEFAULT 1 ,
                      `log` LONGTEXT ,
                      `list_lebe` LONGTEXT,
                      `list_lebe_aktualisiert` BIGINT DEFAULT 0,
                      `list_tot` LONGTEXT,
                      `list_tot_aktualisiert` BIGINT DEFAULT 0,
                      `waiting_for_others_time` BIGINT,
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
                    for ($i = 1; $i <= _MAXPLAYERS; $i++)
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
