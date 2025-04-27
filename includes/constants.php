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

//Settings:
define ("_NOGAMECREATIONERRORMESSAGE", ""); //Falls nicht "": Kein Spiel kann erstellt werden, stattdessen wird der string angezeigt.
//define ("_NOGAMECREATIONERRORMESSAGE", "Wartungsarbeiten bis ..."); //Falls nicht "": Kein Spiel kann erstellt werden, stattdessen wird der string angezeigt.
define("_LISTMAXRELOADTIME",3000);
define("_MAXPLAYERS",50);


///////////////////////////////////
// Constants, do not change!
///////////////////////////////////
define("_VERSION","v1.2.10");

//Phasen 
define ("PHASESETUP",0);
define ("PHASESPIELSETUP",1);
define ("PHASENACHTBEGINN",2);
define ("PHASENACHT1",3);
define ("PHASENACHT2",4);
define ("PHASENACHT3",5);
define ("PHASENACHT4",6);
define ("PHASENACHT5",7);
define ("PHASENACHTENDE",8); 
define ("PHASETOTEBEKANNTGEBEN",9);
define ("PHASEBUERGERMEISTERWAHL",10);
define ("PHASEDISKUSSION",11);
define ("PHASEANKLAGEN",12);
define ("PHASEABSTIMMUNG",13);
define ("PHASESTICHWAHL",14);
define ("PHASENACHABSTIMMUNG",15);
define ("PHASESIEGEREHRUNG",16);

//Charaktere
define ("CHARKEIN",0);
define ("CHARDORFBEWOHNER",1);
define ("CHARWERWOLF",2);
define ("CHARSEHER",3);
define ("CHARHEXE",4);
define ("CHARJAEGER",5);
define ("CHARAMOR",6);
define ("CHARBESCHUETZER",7);
define ("CHARPARERM",8);
define ("CHARLYKANTROPH",9);
define ("CHARSPION",10);
define ("CHARMORDLUSTIGER",11);
define ("CHARPAZIFIST",12);
define ("CHARALTERMANN",13);
define ("CHARURWOLF",14);
?>
