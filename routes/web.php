<?php

/**
 * Toutes les routes de l'application. $router est deja instancie par
 * public/index.php avant d'inclure ce fichier.
 *
 * Format des routes : 'NomDuController@methode' (sans le namespace,
 * le Router ajoute lui-meme le prefixe App\Controllers\).
 */

$router->get('/', 'HomeController@index');

$router->get('/menu', 'ProduitController@index');
$router->get('/produits/{id}', 'ProduitController@detail');
