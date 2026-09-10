<?php

declare(strict_types=1);

use App\Controllers\AuthController;
use App\Controllers\CategorieController;
use App\Controllers\ClientController;
use App\Controllers\CorbeilleController;
use App\Controllers\UtilisateurController;
use App\Controllers\HomeController;
use App\Controllers\ProduitController;
use App\Controllers\StockController;

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

// --- Gestion des categories (admin + gerant) ---
$router->get('/admin/categories', [CategorieController::class, 'index'], ['role:ADMIN']);
$router->get('/admin/categories/creer', [CategorieController::class, 'createForm'], ['role:ADMIN']);
$router->post('/admin/categories/creer', [CategorieController::class, 'store'], ['role:ADMIN']);
$router->get('/admin/categories/{id}/modifier', [CategorieController::class, 'editForm'], ['role:ADMIN']);
$router->post('/admin/categories/{id}/modifier', [CategorieController::class, 'update'], ['role:ADMIN']);
$router->post('/admin/categories/{id}/supprimer', [CategorieController::class, 'delete'], ['role:ADMIN']);

$router->get('/gerant/categories', [CategorieController::class, 'index'], ['role:GERANT,ADMIN']);
$router->get('/gerant/categories/creer', [CategorieController::class, 'createForm'], ['role:GERANT,ADMIN']);
$router->post('/gerant/categories/creer', [CategorieController::class, 'store'], ['role:GERANT,ADMIN']);
$router->get('/gerant/categories/{id}/modifier', [CategorieController::class, 'editForm'], ['role:GERANT,ADMIN']);
$router->post('/gerant/categories/{id}/modifier', [CategorieController::class, 'update'], ['role:GERANT,ADMIN']);
$router->post('/gerant/categories/{id}/supprimer', [CategorieController::class, 'delete'], ['role:GERANT,ADMIN']);

// --- Corbeille (admin + gerant) ---
$router->get('/admin/corbeille', [CorbeilleController::class, 'index'], ['role:ADMIN']);
$router->post('/admin/corbeille/{entite}/{id}/restaurer', [CorbeilleController::class, 'restaurer'], ['role:ADMIN']);
$router->post('/admin/corbeille/{entite}/{id}/supprimer-definitif', [CorbeilleController::class, 'supprimerDefinitivement'], ['role:ADMIN']);

$router->get('/gerant/corbeille', [CorbeilleController::class, 'index'], ['role:GERANT,ADMIN']);
$router->post('/gerant/corbeille/{entite}/{id}/restaurer', [CorbeilleController::class, 'restaurer'], ['role:GERANT,ADMIN']);
$router->post('/gerant/corbeille/{entite}/{id}/supprimer-definitif', [CorbeilleController::class, 'supprimerDefinitivement'], ['role:GERANT,ADMIN']);

// --- Gestion des produits (admin + gerant) ---
$router->get('/admin/produits', [ProduitController::class, 'gestion'], ['role:ADMIN']);
$router->get('/admin/produits/creer', [ProduitController::class, 'createForm'], ['role:ADMIN']);
$router->post('/admin/produits/creer', [ProduitController::class, 'store'], ['role:ADMIN']);
$router->get('/admin/produits/{id}/modifier', [ProduitController::class, 'editForm'], ['role:ADMIN']);
$router->post('/admin/produits/{id}/modifier', [ProduitController::class, 'update'], ['role:ADMIN']);
$router->post('/admin/produits/{id}/supprimer', [ProduitController::class, 'delete'], ['role:ADMIN']);
$router->post('/admin/produits/{id}/disponibilite', [ProduitController::class, 'basculerDisponibilite'], ['role:ADMIN']);

$router->get('/gerant/produits', [ProduitController::class, 'gestion'], ['role:GERANT,ADMIN']);
$router->get('/gerant/produits/creer', [ProduitController::class, 'createForm'], ['role:GERANT,ADMIN']);
$router->post('/gerant/produits/creer', [ProduitController::class, 'store'], ['role:GERANT,ADMIN']);
$router->get('/gerant/produits/{id}/modifier', [ProduitController::class, 'editForm'], ['role:GERANT,ADMIN']);
$router->post('/gerant/produits/{id}/modifier', [ProduitController::class, 'update'], ['role:GERANT,ADMIN']);
$router->post('/gerant/produits/{id}/supprimer', [ProduitController::class, 'delete'], ['role:GERANT,ADMIN']);
$router->post('/gerant/produits/{id}/disponibilite', [ProduitController::class, 'basculerDisponibilite'], ['role:GERANT,ADMIN']);

// --- État des stocks (admin + gerant) ---
$router->get('/admin/stock', [StockController::class, 'index'], ['role:ADMIN']);
$router->post('/admin/stock/{id}/reapprovisionner', [StockController::class, 'reapprovisionner'], ['role:ADMIN']);
$router->post('/admin/stock/{id}/seuil', [StockController::class, 'modifierSeuil'], ['role:ADMIN']);

$router->get('/gerant/stock', [StockController::class, 'index'], ['role:GERANT,ADMIN']);
$router->post('/gerant/stock/{id}/reapprovisionner', [StockController::class, 'reapprovisionner'], ['role:GERANT,ADMIN']);
$router->post('/gerant/stock/{id}/seuil', [StockController::class, 'modifierSeuil'], ['role:GERANT,ADMIN']);
