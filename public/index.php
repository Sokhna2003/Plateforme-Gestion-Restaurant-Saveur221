<?php

/**
 * Front controller : point d'entree unique de l'application.
 * Toutes les requetes HTTP passent par ce fichier, qui delegue au Router.
 */

require __DIR__ . '/../vendor/autoload.php';

use App\Core\Router;

session_start();


$scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
$baseDir = str_replace('\\', '/', dirname($scriptName));
define('BASE_URL', rtrim($baseDir, '/'));

$router = new Router();

require __DIR__ . '/../routes/web.php';

$router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
