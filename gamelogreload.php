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
  if (isset($_ENV['ENVIRONMENT']) && $_ENV['ENVIRONMENT'] === "development") {
    include "includes/includes.dev.php";
  }
  else {
    include "includes/includes.php";
  }

  header("Content-Type: text/html; charset=utf-8");
  header('Cache-Control: no-cache, no-store, max-age=0, must-revalidate');
  header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
  header('Pragma: no-cache');

  $spielID = (int)$_GET['game'];
  $id = (int)$_GET['id'];
  $verifizierungsNr = (int)$_COOKIE['verifizierungsnr'];

  // Verifizieren, dass es sich um einen spieler des spiels handelt
  $meinSpieler = $mysqli->query("SELECT 1 FROM ".$spielID."_spieler WHERE id = $id AND verifizierungsnr = $verifizierungsNr");
  if ($meinSpieler->num_rows != 1)
  {
    die("<p class='error'>Sie sind momentan nicht mit diesem Spiel verknüpft!</p>");
  }

  if ($Result = $mysqli->query("SELECT * FROM ".$spielID."_game"))
  {
    $temp = $Result->fetch_assoc();
    echo $temp['log'];
  }

?>
