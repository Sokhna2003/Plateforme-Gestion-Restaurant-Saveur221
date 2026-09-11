<?php

declare(strict_types=1);

/**
 * Configuration Cloudinary (upload des images des catégories et produits).
 *
 * Si le service est activé et que les identifiants sont renseignés, les images
 * sont envoyées vers Cloudinary (API REST via cURL, signature SHA-1) et l'URL
 * distante est enregistrée en base.
 *
 * Sans identifiants (par défaut), les images sont simplement enregistrées
 * localement dans `public/uploads/` : l'application fonctionne sans compte.
 *
 * Identifiants : https://cloudinary.com/ (compte gratuit), rubrique Dashboard.
 * Voir l'atelier 21 — Cloudinary.
 */

return [
    'enabled'    => true,
    'cloud_name' => $_ENV['CLOUDINARY_CLOUD_NAME'] ?? '',
    'api_key'    => $_ENV['CLOUDINARY_API_KEY'] ?? '',
    'api_secret' => $_ENV['CLOUDINARY_API_SECRET'] ?? '',
    'folder'     => $_ENV['folder'] ?? 'restaurant_saveur221',
];