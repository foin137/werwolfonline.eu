<?php

/*

werwolfonline, a php web game
    Copyright (C) 2023

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <https://www.gnu.org/licenses/>.

*/

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
    $existiert = True;
    try{
      $alleres = $mysqli ->Query("SELECT * FROM $i"."_game");
    }
    catch (mysqli_sql_exception $e){ 
      $existiert = False;
    }
    if($existiert && isset($alleres->num_rows))
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

  $inaktivzeit = $gameResAssoc['inaktivzeit'];
  $inaktivzeitzusatz = $gameResAssoc['inaktivzeitzusatz'];

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
  echo "<span class='normal' ><label for='zufaelligeAuswahlBonusID'> Verteilung der zufälligen Charaktere </label><INPUT TYPE='number'   NAME='zufaelligeAuswahlBonus' id='zufaelligeAuswahlBonusID' Size='4' value=$zufaelligeAuswahlBonus MIN=-15 MAX=15></span>";
  echo "</div>";
  echo "<div><h3 >Countdown-Einstellungen</h3>";
  echo "<span class='normal' ><INPUT TYPE='button' VALUE='Countdowns zurücksetzen' OnClick='auswahl.werwolftimer1.value=60; auswahl.werwolfzusatz1.value=4; auswahl.werwolftimer2.value=50; auswahl.werwolfzusatz2.value=3; auswahl.dorftimer.value=550; auswahl.dorfzusatz.value=10; auswahl.dorfstichwahltimer.value=200; auswahl.dorfstichwahlzusatz.value=5'></span>";
  echo "<span class='normal' ><label for='werwolftimer1ID'>Sekunden, bis die Werwölfe nicht mehr einstimmig wählen müssen: </label>
    <INPUT TYPE='number' NAME='werwolftimer1' id='werwolftimer1ID' SIZE='4' VALUE=$werwolftimer1 MIN='20' MAX='500'><br>
    <label for='werwolfzusatz1ID'>Zusätzliche Zeit pro Werwolf: </label><INPUT TYPE='number' NAME='werwolfzusatz1' id='werwolfzusatz1ID' SIZE='4' VALUE=$werwolfzusatz1 MIN='0' MAX='60'></span>";
  echo "<span class='normal' ><label for='werwolftimer2ID'>Sekunden, bis nach Ablaufen der Einstimmigkeit die Wahl der Werwölfe erfolglos ist: </label>
    <INPUT TYPE='number' NAME='werwolftimer2' id='werwolftimer2ID' SIZE='4' VALUE=$werwolftimer2 MIN='10' MAX='500'><br>
    <label for='werwolfzusatz2ID'>Zusätzliche Zeit pro Werwolf: </label><INPUT TYPE='number' NAME='werwolfzusatz2' id='werwolfzusatz2ID' SIZE='4' VALUE=$werwolfzusatz2 MIN='0' MAX='60'></span>";
  echo "<span class='normal' ><label for='dorftimerID'>Sekunden, bis die normale Abstimmung des Dorfes am Tag erfolglos ist: </label>
    <INPUT TYPE='number' NAME='dorftimer' id='dorftimerID' SIZE='4' VALUE=$dorftimer MIN='60' MAX='7200'><br>
    <label for='dorfzusatzID'>Zusätzliche Zeit pro Dorfbewohner: </label><INPUT TYPE='number' NAME='dorfzusatz' id='dorfzusatzID' SIZE='4' VALUE=$dorfzusatz MIN='0' MAX='300'></span>";
  echo "<span class='normal' ><label for='dorfstichwahltimerID'>Sekunden, bis die Stichwahl am Tag erfolglos ist: </label>
    <INPUT TYPE='number' NAME='dorfstichwahltimer' id='dorfstichwahltimerID' SIZE='4' VALUE=$dorfstichwahltimer MIN='30' MAX='3600'><br>
    <label for='dorfstichwahlzusatzID'>Zusätzliche Zeit pro Dorfbewohner: </label><INPUT TYPE='number' NAME='dorfstichwahlzusatz' id='dorfstichwahlzusatzID' SIZE='4' VALUE=$dorfstichwahlzusatz MIN='0' MAX='300'></span>";
  echo "<span class='normal' ><label for='inaktivzeitID'>Sekunden, nach denen angezeigt wird, auf wen noch gewartet wird: </label>
    <INPUT TYPE='number' NAME='inaktivzeit' id='inaktivzeitID' SIZE='4' VALUE=$inaktivzeit MIN='20' MAX='3600'><br>
    <label for='inaktivzeitzusatzID'>Zusätzliche Zeit pro Spieler: </label><INPUT TYPE='number' NAME='inaktivzeitzusatz' id='inaktivzeitzusatzID' SIZE='4' VALUE=$inaktivzeitzusatz MIN='0' MAX='300'></span>";
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
    $inaktivzeit = (int)$_POST['inaktivzeit'];
    $inaktivzeitzusatz = (int)$_POST['inaktivzeitzusatz'];
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
     dorfstichwahlzusatz = $dorfstichwahlzusatz,
     inaktivzeit = $inaktivzeit,
     inaktivzeitzusatz = $inaktivzeitzusatz");
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
    $waiting_for_others_time = time() + get_waiting_for_others_time($mysqli);
    $mysqli->Query("UPDATE $spielID"."_game SET `waiting_for_others_time` = $waiting_for_others_time");
  }
  elseif ($phase == PHASENACHT2)
  {
    $waiting_for_others_time = time() + get_waiting_for_others_time($mysqli);
    $mysqli->Query("UPDATE $spielID"."_game SET `waiting_for_others_time` = $waiting_for_others_time");
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
    $waiting_for_others_time = time() + get_waiting_for_others_time($mysqli);
    $mysqli->Query("UPDATE $spielID"."_game SET `waiting_for_others_time` = $waiting_for_others_time");
  }
  elseif ($phase == PHASENACHT4)
  {
    $waiting_for_others_time = time() + get_waiting_for_others_time($mysqli);
    $mysqli->Query("UPDATE $spielID"."_game SET `waiting_for_others_time` = $waiting_for_others_time");
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

function get_waiting_for_others_time($mysqli)
{
  //Gibt die Anzahl der Sekunden zurück, wie lange wir warten wollen, bis wir anzeigen, auf wen wir noch warten.
  $spielID = (int)$_COOKIE['SpielID'];
  if ($result = $mysqli->query("SELECT * FROM $spielID"."_spieler"))
  {
    $spielerzahl = $result->num_rows;
    if ($g = gameAssoc($mysqli))
    {
      return $g['inaktivzeit'] + $g['inaktivzeitzusatz'] * $spielerzahl;
    }
  }
  return 0;
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

function warteAufAndere($mysqli)
{
  //Zeigt das warteAufAnder an, damit es bei jedem gleich aussieht
  echo "<h3 >Warte auf andere Spieler</h3>";

  //Output the players we are waiting for (if enough time has passed)
  $gameAssoc = gameAssoc($mysqli);
  if ($gameAssoc['waiting_for_others_time'] < time())
  {
    $spielID = (int)$_COOKIE['SpielID'];
    $nichtBereitResult = $mysqli->Query("SELECT * FROM $spielID"."_spieler WHERE bereit = 0 AND lebt = 1");
    if (!is_bool($nichtBereitResult)) {
      echo("Warte noch auf: ");
      while($row = $nichtBereitResult->fetch_row()){
        if (count($row) > 1) {
          error_log($row[1]);
          echo("$row[1]\t");
        }
      }
    }
  }
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
  if ($myfile){
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
}

function writeGameToLogSpielErstellen($mysqli, $spielID, $name)
{
  $fileName = "log/Werwolf_log_".date("Y_m").".log";
  $myfile = fopen($fileName, "a");
  if ($myfile){
    fwrite($myfile,"\n--- NEUES SPIEL ERSTELLT --- \n");
    fwrite($myfile,"SpielID: $spielID \n");
    fwrite($myfile,"Zeit: ".date("d.m.Y, H:i:s")."\n");
    fwrite($myfile,"Name des Erstellers: $name \n");
    fclose($myfile);
  }
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
  $spielID = (int)$_COOKIE['SpielID'];
  $spielerID = (int)$spielerID;
  $mysqli->Query("UPDATE $spielID"."_spieler SET lebt = 0 WHERE id = $spielerID");
  //Überprüfe, ob sonst schon alle bereit sind
  if ($g = gameAssoc($mysqli))
  {
    phaseBeendenWennAlleBereit($g['spielphase'],$mysqli);
  }
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
  try{
    if ($gameRes = $mysqli->Query("SELECT * FROM $spielID"."_game"))
    {
      $gameA = $gameRes->fetch_assoc();
      return $gameA;
    }
  }catch(mysqli_sql_exception $e) {}
  return false;
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
  try{
    if ($res = $mysqli->Query("SELECT * FROM $spielID"."_spieler WHERE id = $spielerID"))
    {
      $temp = $res->fetch_assoc();
      return $temp['name'];
    }
  } catch(mysqli_sql_exception $e) {}
  return "Unknown";
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
  if ($gameAssoc = gameAssoc($mysqli))
  {
    $aktLog = $gameAssoc['log'];
    $neuLog = $aktLog.date("H:i:s").": ".$logeintrag."<br>";
    $neuLog = str_replace("'",'"',$neuLog); //ersetze alle ' mit "
    $stmt = $mysqli->prepare("UPDATE $spielID"."_game SET log = ?");
    $stmt->bind_param("s",$neuLog);
    $stmt->execute();
    $stmt->close();
  }
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

?>
