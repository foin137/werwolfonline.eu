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

$host = "db";    // Container name
$id = "werwolf"; // Nutzername
$pw = "mariadb"; // Pwd
$db = "werwolf"; // Db name

$mysqli = new MySQLi(
    $host,
    $id,
    $pw,
    $db
);
      
if (mysqli_connect_errno()) {
    printf(
        "Can't connect to MySQL Server. Errorcode: %s\n",
        mysqli_connect_error()
    );   
    exit;
}
?>