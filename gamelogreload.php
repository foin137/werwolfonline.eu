<?php

  include "includes/includes.php";
  header("Content-Type: text/html; charset=utf-8");
  header('Cache-Control: no-cache, no-store, max-age=0, must-revalidate');
  header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
  header('Pragma: no-cache');
  $spielID = (int)$_GET['game'];

  if ($Result = $mysqli->query("SELECT * FROM ".$spielID."_game"))
  {
    $temp = $Result->fetch_assoc();
    echo $temp['log'];
  }

?>
