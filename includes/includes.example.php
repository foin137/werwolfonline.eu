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


$id = "id"; // Ihre ID (username) zum MySQL Server
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