<?php

declare(strict_types=1);

/**
 * Exemple de configuration Cloudinary (upload des images des catégories et produits).
 *
 * Copiez ce fichier vers `config/cloudinary.php` puis renseignez vos identifiants
 * (Dashboard de votre compte Cloudinary : https://cloudinary.com/).
 *
 *   cp config/cloudinary.example.php config/cloudinary.php
 *
 * `cloudinary.php` est ignoré par git : vos identifiants ne sont jamais commités.
 *
 * Sans identifiants, les images sont enregistrées localement dans `public/uploads/`.
 */

return [
    'enabled'    => true,
    'cloud_name' => '',
    'api_key'    => '',
    'api_secret' => '',
    'folder'     => 'saveur221/categories',
];