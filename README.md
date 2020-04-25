# werwolfonline.eu
This is the sourcecode of a php web game "werwolfonline" I wrote a few years ago. The game can be played in German at www.werwolfonline.eu

German version is below ...

Although I did not initially plan to publish its source code, a few people asked me for it, so I figured I might as well publish it open source.
However, I would not consider myself a professional programmer, even less at the time I wrote most parts of this code, and therefore the code (and project overall) might probably be a bit clumsy.

If you have any comments or tips, please let me know! If you like, you may also contribute, but I am new to Git(Hub), so please be patient with me ;)

# What is required?
The game is written in php, so you will need a php parser on your webserver.

Output buffering needs to be enabled due to some maybe not quite ideal programming. Otherwise, cookies cannot be set.
You can find the corresponding setting "output_buffering" in the php.ini file. Setting it to 4096 should work.

You also need a database called "werwolf", you do not need to insert any tables, this is done by the script automatically.

Rename 'includes.example.php' to 'includes.php' and insert your id, passwort and host.

If you want to use a different database name, you can change it in 'includes.php'.

# Deutsche Anleitung
Das ist Quellcode einer php Internetanwendung "werwolfonline", die ich vor ein paar Jahren geschrieben habe. Man findet eine spielbare Version auf www.werwolfonline.eu

Ursprünglich war es nicht geplant, den Quellcode zu veröffentlichen, aber nachdem dann ein (kleines) Interesse bestand, dachte ich mir, könnte ich den Quellcode auch open source veröffentlichen. Vielleicht kann jemand etwas damit anfangen!
Allerdings würde ich mich nicht als professionellen Programmierer bezeichnen, daher sind sicher große Teile des Projekts sperrig, umständlich oder mühsam zu verstehen.

Ich würde mich freuen, wenn jemand Tipps oder weitere Vorschläge hat! Wer will, kann natürlich auch via Git(Hub) beitragen, ich bin nur leider noch nicht sehr erfahren im Umgang auf GitHub - sry ...

# Was braucht man?
Einen Webserver mit php interpreter und einer Datenbank namens "werwolf".

Auf dem Webserver muss output buffering aktiviert sein, ansonsten können keine Cookies gespeichert werden.
Die Einstellung findet man in der php.ini Datei unter "output_buffering". Ein Wert von 4096 sollte funktionieren.

Die Datenbank muss keine Tabellen enthalten, die werden vom php Skript selbst erstellt.

Benenne 'includes.example.php' in 'includes.php' um und füge id, passwort und host für die Anmeldung in der Datenbank hinzu!

Es kann natürlich auch ein anderer Name für die Datenbank verwendet werden. Die zugehörige Einstellung kann in 'includes.php' vorgenommen werden.
