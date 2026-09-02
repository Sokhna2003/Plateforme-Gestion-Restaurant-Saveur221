<?php

/**
 * Front controller : point d'entree unique de l'application (pattern
 * front-controller, comme vu en cours). Toutes les requetes HTTP passent
 * par ce fichier, qui delegue au Router.
 */

require __DIR__ . '/../vendor/autoload.php';

use App\Core\Router;

session_start();

$router = new Router();

// Les routes sont declarees dans routes/web.php, separement de ce fichier
// pour rester lisible a mesure que l'appli grandit.
require __DIR__ . '/../routes/web.php';

$router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
