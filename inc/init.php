<?php

// Fuseau horaire et langue locale
date_default_timezone_set('Europe/Paris');
setlocale(LC_ALL,'fr_FR.utf8','french_FRANCE.utf8','fr.utf8','french.utf8','fra.utf8');

// echo strftime('%A %d %B %Y',time());
// Ouverture de session
session_name('CAPTAINMARVEL');
session_start();

// Connexion à la BDD
$pdo = new PDO(
    'mysql:host=localhost;charset=utf8;dbname=marvel', // DSN Data Source Name
    'root', // user
    '', // password
    array(
       PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING, // Mode d'erreur : affichage des avertissements
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC // Mode d'obtention des lignes après un select
    )
);

// Définition de constantes  (en PHP, --> php echo URL; ne fonctione pas sur certains chemin puisque nous sommes déjà dans le projet)
define("URL","/admin/index.php");

// Inclusion des fonctions
require_once('functions.php');