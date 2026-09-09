<?php

declare(strict_types=1);

$message = flash_messages();

$estConnecte = estConnecte();
$isClient = isset($_SESSION['client']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? '') ?> - Saveur 221</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            brown: '#5C2E1A',
                            orange: '#D95F02',
                            cream: '#FFF9F3',
                        }
                    },
                    fontFamily: {
                        serif: ['Fraunces', 'serif'],
                    }
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:wght@600;700&display=swap" rel="stylesheet">
</head>
<body class="bg-brand-cream text-brand-brown font-sans">

<header class="bg-white border-b border-brand-brown/10">
    <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
        <a href="<?= $base ?>/" class="flex items-center gap-2">
            <span class="w-7 h-7 rounded-full bg-brand-orange text-white flex items-center justify-center font-serif font-bold text-sm">S</span>
            <span class="font-serif font-bold text-lg">SAVEUR 221</span>
        </a>

        <nav class="hidden md:flex items-center gap-8 text-sm font-medium">
            <a href="<?= $base ?>/" class="hover:text-brand-orange">Accueil</a>
            <a href="<?= $base ?>/menu" class="hover:text-brand-orange">Menu</a>
            <?php if ($estConnecte): ?>
                <?php if ($isClient): ?>
                    <a href="<?= $base ?>/client" class="hover:text-brand-orange">Mes commandes</a>
                <?php endif; ?>
            <?php endif; ?>
        </nav>

        <div class="flex items-center gap-4">
            <?php if ($estConnecte): ?>
                <a href="<?= $base ?><?= $isClient ? '/client' : '/admin' ?>" class="flex items-center gap-2 text-sm font-medium">
                    <div class="w-8 h-8 rounded-full bg-brand-orange/10 flex items-center justify-center text-brand-orange font-bold text-xs">
                        <?= strtoupper(substr(nomComplet(), 0, 1)) ?>
                    </div>
                    <span class="hidden md:inline"><?= e(nomComplet()) ?></span>
                </a>
                <a href="<?= $base ?>/deconnexion" class="text-sm text-brand-brown/50 hover:text-brand-orange">Déconnexion</a>
            <?php else: ?>
                <a href="<?= $base ?>/connexion" class="text-sm font-medium hover:text-brand-orange">Connexion</a>
                <a href="<?= $base ?>/inscription"
                   class="bg-brand-orange text-white px-4 py-2 rounded-xl text-sm font-medium hover:opacity-90">
                    S'inscrire
                </a>
            <?php endif; ?>
        </div>
    </div>
</header>

<main class="max-w-7xl mx-auto px-6 py-10">

    <?php if ($message): ?>
        <div class="mb-6 px-4 py-3 rounded-xl text-sm
            <?= $message['type'] === 'error' ? 'bg-red-50 text-red-700 border border-red-200' : 'bg-green-50 text-green-700 border border-green-200' ?>">
            <?= e($message['message']) ?>
        </div>
    <?php endif; ?>

    <?= $content ?>

</main>

<footer class="bg-brand-brown text-brand-cream/80 mt-16">
    <div class="max-w-7xl mx-auto px-6 py-8 text-center text-xs">
        &copy; <?= date('Y') ?> Saveur 221. Tous droits réservés.
    </div>
</footer>

</body>
</html>
