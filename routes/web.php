<?php

declare(strict_types=1);

use App\Controllers\AuthController;
use App\Controllers\ClientController;
use App\Controllers\UtilisateurController;
use App\Controllers\HomeController;
use App\Controllers\ProduitController;

// --- Pages publiques ---
$router->get('/', [HomeController::class, 'index']);
$router->get('/menu', [ProduitController::class, 'index']);
$router->get('/produits/{id}', [ProduitController::class, 'detail']);

// --- Authentification ---
$router->get('/connexion', [AuthController::class, 'loginForm']);
$router->post('/connexion', [AuthController::class, 'login']);
$router->get('/inscription', [AuthController::class, 'registerForm']);
$router->post('/inscription', [AuthController::class, 'register']);
$router->get('/deconnexion', [AuthController::class, 'logout']);

// --- Dashboard (unifie selon role) ---
$router->get('/client', [ClientController::class, 'dashboard'], ['auth']);
$router->get('/gerant', [UtilisateurController::class, 'dashboard'], ['role:GERANT,ADMIN']);
$router->get('/admin', [UtilisateurController::class, 'dashboard'], ['role:ADMIN']);
