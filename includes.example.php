<?php 
$id = "id"; // Ihre ID zum MySQL Server
  $pw = "pw"; // Passwort zum MySQL Server
  $host = "localhost"; // Host ("localhost" oder "IP-Adresse")
  $db = "werwolf"; // Name Ihrer Datenbank
   $mysqli = new MySQLi(
        $host,
        $id,
        $pw,
        $db
      );
       
      if (mysqli_connect_errno()) {
        /*printf(
          "Can't connect to MySQL Server. Errorcode: %s\n",
          mysqli_connect_error()
        );*/
       
        exit;
      }
      ?>