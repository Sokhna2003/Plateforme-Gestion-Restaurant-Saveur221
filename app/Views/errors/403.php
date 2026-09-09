<?php

declare(strict_types=1);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>403 - Accès refusé | Saveur 221</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-brand-cream text-brand-brown font-sans flex items-center justify-center min-h-screen">
    <div class="text-center px-6">
        <h1 class="font-serif text-6xl font-bold text-brand-orange mb-4">403</h1>
        <h2 class="font-serif text-2xl font-bold mb-2">Accès refusé</h2>
        <p class="text-brand-brown/60 mb-8">Vous n'avez pas les droits nécessaires pour accéder à cette page.</p>
        <a href="<?= $base ?>/" class="inline-block bg-brand-orange text-white px-6 py-3 rounded-full font-medium hover:opacity-90">
            Retour à l'accueil
        </a>
    </div>
</body>
</html>
