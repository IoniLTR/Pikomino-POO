<?php

// Note importante : pour pouvoir écrire le json, il faut les droits : sudo chmod -R 777 /opt/lampp/htdocs/Pikomino


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once("JoueurBasique.php");
include_once("JoueurAgressif.php");
include_once("Jeu.php");

// Utilisation dans Jeu
$joueurs = [new JoueurAgressif('Pique'), new JoueurBasique('Coeur'), new JoueurBasique('Carreau'), new JoueurBasique('Trefle')];
$jeu = new Jeu($joueurs);
$jeu->jouer();
?>

