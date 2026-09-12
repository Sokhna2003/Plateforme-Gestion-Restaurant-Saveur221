<?php

declare(strict_types=1);

$message = flash_messages();
$nomComplet = nomComplet();

// Profil (avatar + menu deroulant)
$sessionClient = $_SESSION['client'] ?? [];
$photo = $sessionClient['photo'] ?? null;
$email = $sessionClient['email'] ?? '';
$lienPhoto = (is_string($photo) && $photo !== '')
    ? (str_starts_with($photo, 'http') ? $photo : $base . $photo)
    : null;
$initiale = strtoupper(substr($nomComplet, 0, 1));
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

<!-- Navbar client connecte -->
<header class="bg-white border-b border-brand-brown/10">
    <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
        <a href="<?= $base ?>/" class="flex items-center gap-2">
            <span class="w-7 h-7 rounded-full bg-brand-orange text-white flex items-center justify-center font-serif font-bold text-sm">S</span>
            <span class="font-serif font-bold text-lg">SAVEUR 221</span>
        </a>

        <nav class="hidden md:flex items-center gap-8 text-sm font-medium">
            <a href="<?= $base ?>/" class="hover:text-brand-orange">Accueil</a>
            <a href="<?= $base ?>/menu" class="hover:text-brand-orange">Menu</a>
            <a href="<?= $base ?>/panier" class="relative hover:text-brand-orange">
                Panier
                <?php if (panier_count() > 0): ?>
                    <span class="absolute -top-2 -right-4 bg-green-600 text-white text-[10px] w-4 h-4 rounded-full flex items-center justify-center"><?= (int) panier_count() ?></span>
                <?php endif; ?>
            </a>
            <a href="<?= $base ?>/client/commandes" class="hover:text-brand-orange">Mes commandes</a>
        </nav>

        <div class="relative" id="profil-bouton">
            <button type="button" onclick="toggleProfilMenu()" class="flex items-center gap-2 text-sm font-medium cursor-pointer group">
                <div class="w-8 h-8 rounded-full bg-brand-orange/10 flex items-center justify-center text-brand-orange font-bold text-xs overflow-hidden shrink-0">
                    <?php if ($lienPhoto !== null): ?>
                        <img src="<?= e($lienPhoto) ?>" alt="Photo de profil" class="w-full h-full object-cover">
                    <?php else: ?>
                        <?= e($initiale) ?>
                    <?php endif; ?>
                </div>
                <span class="hidden md:inline"><?= e($nomComplet) ?></span>
                <i class="fa-solid fa-chevron-down text-xs text-brand-brown/40 hidden md:inline group-hover:text-brand-orange transition"></i>
            </button>

            <div id="profil-menu" class="hidden absolute right-0 top-full mt-3 w-64 bg-white rounded-xl shadow-xl border border-brand-brown/10 overflow-hidden z-30 text-left">
                <div class="px-4 py-3 bg-brand-cream border-b border-brand-brown/5 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-brand-orange/10 flex items-center justify-center text-brand-orange font-bold text-sm overflow-hidden shrink-0">
                        <?php if ($lienPhoto !== null): ?>
                            <img src="<?= e($lienPhoto) ?>" alt="Photo de profil" class="w-full h-full object-cover">
                        <?php else: ?>
                            <?= e($initiale) ?>
                        <?php endif; ?>
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-semibold truncate"><?= e($nomComplet) ?></p>
                        <p class="text-xs text-brand-brown/50 truncate"><?= e((string) $email) ?></p>
                    </div>
                </div>
                <a href="<?= $base ?>/client"
                   class="flex items-center gap-3 px-4 py-3 text-sm text-brand-brown/80 hover:bg-brand-orange/5 hover:text-brand-orange transition">
                    <i class="fa-solid fa-house w-4 text-center"></i> Mon espace
                </a>
                <a href="<?= $base ?>/profil"
                   class="flex items-center gap-3 px-4 py-3 text-sm text-brand-brown/80 hover:bg-brand-orange/5 hover:text-brand-orange transition">
                    <i class="fa-solid fa-user-pen w-4 text-center"></i> Modifier mes informations
                </a>
                <a href="<?= $base ?>/deconnexion"
                   class="flex items-center gap-3 px-4 py-3 text-sm text-red-600 hover:bg-red-50 transition border-t border-brand-brown/5">
                    <i class="fa-solid fa-right-from-bracket w-4 text-center"></i> Déconnexion
                </a>
            </div>
        </div>
    </div>
</header>

<!-- Contenu -->
<main class="max-w-7xl mx-auto px-6 py-10">

    <?php if ($message): ?>
        <div class="mb-6 px-4 py-3 rounded-xl text-sm
            <?= $message['type'] === 'error' ? 'bg-red-50 text-red-700 border border-red-200' : 'bg-green-50 text-green-700 border border-green-200' ?>">
            <?= e($message['message']) ?>
        </div>
    <?php endif; ?>

    <?= $content ?>

</main>

<!-- Footer -->
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

    <div class="border-t border-white/10 text-center text-xs py-4 pb-24 md:pb-4">
        &copy; <?= date('Y') ?> Saveur 221. Tous droits réservés.
    </div>
</footer>

<script>
    function toggleProfilMenu() {
        const menu = document.getElementById('profil-menu');
        if (menu) menu.classList.toggle('hidden');
    }

    document.addEventListener('click', function (evenement) {
        const bouton = document.getElementById('profil-bouton');
        const menu = document.getElementById('profil-menu');
        if (menu && bouton && !bouton.contains(evenement.target)) {
            menu.classList.add('hidden');
        }
    });
</script>

<?php require __DIR__ . '/../partials/mobile_nav.php'; ?>

</body>
</html>
