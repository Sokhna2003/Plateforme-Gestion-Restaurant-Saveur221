<?php

/**
 * Configuration centrale de l'application.
 * Meme base de donnees que le Module A (Java) : ne change pas ces valeurs
 * sans les repercuter aussi dans DatabaseConfig.java.
 */

return [
    'db' => [
        'host' => 'localhost',
        'port' => 3306,
        'name' => 'restaurant_saveur221',
        'user' => 'root',
        'password' => '',
        'charset' => 'utf8mb4',
    ],

    'app' => [
        // Nom du dossier du projet vu par le navigateur, ex: /saveur221-php/public
        // Laisse vide si le projet est servi a la racine du serveur.
        'base_path' => '',
    ],
];
