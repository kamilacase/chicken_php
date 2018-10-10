<?php

// On enregistre notre autoload.
function loadClass($class)
{
    require "classes/{$class}.php";
}

spl_autoload_register('loadClass');

session_start(); // On appelle session_start() APRÈS avoir enregistré l'autoload.

include_once 'config/settings.php';

if (DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', true);
    ini_set('display_startup_errors', true);
}

$db = new PDO('mysql:host='.DB_HOSTNAME.';dbname='.DB_DATABASE, DB_USERNAME, DB_PASSWORD);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING); // On émet une alerte à chaque fois qu'une requête a échoué.
