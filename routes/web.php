<?php

declare(strict_types=1);

/**
 * Toutes les routes de l'application. $router est deja instancie par
 * public/index.php avant d'inclure ce fichier.
 *
 * Format des routes : [NomDuController::class, 'methode'].
 * Le Router resout le controller via le conteneur.
 */

use App\Controllers\HomeController;
use App\Controllers\ProduitController;

$router->get('/', [HomeController::class, 'index']);

$router->get('/menu', [ProduitController::class, 'index']);
$router->get('/produits/{id}', [ProduitController::class, 'detail']);
