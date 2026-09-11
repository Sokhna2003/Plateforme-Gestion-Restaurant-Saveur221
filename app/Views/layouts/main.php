<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) . ' - ' : '' ?>Saveur 221</title>

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
            <a href="<?= $base ?>/" class="hover:text-brand-orange <?= ($activeNav ?? '') === 'accueil' ? 'text-brand-orange' : '' ?>">Accueil</a>
            <a href="<?= $base ?>/menu" class="hover:text-brand-orange <?= ($activeNav ?? '') === 'menu' ? 'text-brand-orange' : '' ?>">Menu</a>
            <a href="#" class="hover:text-brand-orange">À propos</a>
            <a href="#" class="hover:text-brand-orange">Contact</a>
        </nav>

        <div class="flex items-center gap-4">
            <button class="text-brand-brown/70 hover:text-brand-orange" aria-label="Rechercher">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 104.5 4.5a7.5 7.5 0 0012.15 12.15z" />
                </svg>
            </button>
            <a href="<?= $base ?>/panier" class="relative text-brand-brown/70 hover:text-brand-orange" aria-label="Panier">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l3-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m-9-1a1 1 0 102 0 1 1 0 00-2 0zm9 0a1 1 0 102 0 1 1 0 00-2 0z" />
                </svg>
                <?php if (panier_count() > 0): ?>
                    <span class="absolute -top-2 -right-2 bg-green-600 text-white text-[10px] w-4 h-4 rounded-full flex items-center justify-center"><?= (int) panier_count() ?></span>
                <?php endif; ?>
            </a>
            <?php if (estConnecte()): ?>
                <a href="<?= $base ?><?= isset($_SESSION['client']) ? '/client' : '/admin' ?>" class="text-brand-orange text-sm font-medium hover:underline">Mon compte</a>
                <a href="<?= $base ?>/deconnexion" class="text-brand-brown/50 text-sm hover:text-brand-orange">Déconnexion</a>
            <?php else: ?>
                <a href="<?= $base ?>/connexion" class="border border-brand-orange text-brand-orange text-sm px-4 py-1.5 rounded-full hover:bg-brand-orange hover:text-white transition">Connexion</a>
            <?php endif; ?>
        </div>
    </div>
</header>

<?php if ($message = flash_messages()): ?>
    <div class="max-w-7xl mx-auto px-6 py-3">
        <div class="px-4 py-3 rounded-xl text-sm
            <?= $message['type'] === 'error' ? 'bg-red-50 text-red-700 border border-red-200' : 'bg-green-50 text-green-700 border border-green-200' ?>">
            <?= e($message['message']) ?>
        </div>
    </div>
<?php endif; ?>

<?= $content ?>

<footer class="bg-brand-brown text-brand-cream/80">
    <div class="max-w-7xl mx-auto px-6 py-12 grid grid-cols-1 md:grid-cols-3 gap-10">
        <div>
            <div class="flex items-center gap-2 mb-3">
                <span class="w-6 h-6 rounded-full bg-brand-orange text-white flex items-center justify-center font-serif font-bold text-xs">S</span>
                <span class="font-serif font-bold text-white">SAVEUR 221</span>
            </div>
            <p class="text-sm leading-relaxed">
                La première plateforme de commande en ligne dédiée à l'authentique
                gastronomie sénégalaise. Savourez nos spécialités préparées avec passion.
            </p>
        </div>

        <div>
            <h3 class="text-brand-orange font-semibold mb-3">Navigation</h3>
            <ul class="space-y-2 text-sm">
                <li><a href="<?= $base ?>/" class="hover:text-white">Accueil</a></li>
                <li><a href="<?= $base ?>/menu" class="hover:text-white">Menu</a></li>
                <li><a href="#" class="hover:text-white">À propos</a></li>
                <li><a href="#" class="hover:text-white">Contact</a></li>
            </ul>
        </div>

        <div>
            <h3 class="text-brand-orange font-semibold mb-3">Restez connecté</h3>
            <p class="text-sm">Email : contact@saveur221.sn</p>
            <p class="text-sm">Téléphone : +221 33 800 00 00</p>
        </div>
    </div>

    <div class="border-t border-white/10 text-center text-xs py-4">
        &copy; <?= date('Y') ?> Saveur 221. Tous droits réservés.
    </div>
</footer>
>
        &copy; <?= date('Y') ?> Saveur 221. Tous droits réservés.
    </div>
</footer>

</body>
</html>
