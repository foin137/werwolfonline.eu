<?php
  header("Content-Type: text/html; charset=utf-8");
  header('Cache-Control: no-cache, no-store, max-age=0, must-revalidate');
  header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
  header('Pragma: no-cache');
  include "includes/includes.php";
  $spielID = (int)$_GET['game'];
  $id = (int)$_GET['id'];
  if ($Result = $mysqli->query("SELECT * FROM ".$spielID."_spieler WHERE id = $id"))
  {
    $temp = $Result->fetch_assoc();
    echo $temp['reload'];
  }

?>
