<?php

declare(strict_types=1);

$message = flash_messages();

// Determiner le role depuis la session
$role = $_SESSION['user']['role'] ?? ($_SESSION['client'] ?? null !== null ? 'CLIENT' : '');
$isAdmin = ($role === 'ADMIN');
$nomComplet = nomComplet();
$chemin = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$prefixe = $isAdmin ? '/admin' : '/gerant';

// Profil (avatar + menu deroulant)
$photo = $_SESSION['user']['photo'] ?? ($_SESSION['client']['photo'] ?? null);
$email = $_SESSION['user']['email'] ?? ($_SESSION['client']['email'] ?? '');
$lienPhoto = (is_string($photo) && $photo !== '')
    ? (str_starts_with($photo, 'http') ? $photo : $base . $photo)
    : null;
$initiale = strtoupper(substr($nomComplet, 0, 1));

// Focus (lien actif) : correspondance exacte ou sous-route
$estActif = static function (string $url) use ($chemin): bool {
    return $chemin === $url || str_starts_with($chemin, $url . '/');
};

$rendreTitre = static function (string $titre): string {
    return '<p class="px-4 mt-3 mb-1 text-[10px] font-semibold uppercase tracking-[0.2em] text-white/40">' . $titre . '</p>';
};

$rendreLien = static function (array $lien, bool $actif) use ($base): string {
    $classe = $actif
        ? 'bg-brand-orange text-white shadow-sm'
        : 'text-white/70 hover:bg-white/10 hover:text-white';
    return '<a href="' . $base . $lien['url'] . '" class="flex items-center gap-3 px-4 py-2 rounded-xl text-sm transition ' . $classe . '">'
        . '<i class="' . $lien['icon'] . ' text-sm w-5 text-center"></i>'
        . '<span>' . $lien['label'] . '</span>'
        . '</a>';
};

// Menu partagé par l'admin et le gérant
$liensGestion = [
    ['label' => 'Catégories', 'icon' => 'fa-solid fa-folder-open', 'url' => $prefixe . '/categories'],
    ['label' => 'Produits', 'icon' => 'fa-solid fa-utensils', 'url' => $prefixe . '/produits'],
    ['label' => 'Stock', 'icon' => 'fa-solid fa-boxes-stacked', 'url' => $prefixe . '/stock'],
    ['label' => 'Commandes', 'icon' => 'fa-solid fa-receipt', 'url' => $prefixe . '/commandes'],
    ['label' => 'Paiements', 'icon' => 'fa-solid fa-credit-card', 'url' => $prefixe . '/paiements'],
    ['label' => 'Corbeille', 'icon' => 'fa-solid fa-trash-can', 'url' => $prefixe . '/corbeille'],
    ['label' => 'Statistiques', 'icon' => 'fa-solid fa-chart-line', 'url' => $prefixe . '/stats'],
];

// Menu réservé à l'admin
$liensAdministration = $isAdmin ? [
    ['label' => 'Utilisateurs', 'icon' => 'fa-solid fa-users', 'url' => '/admin/utilisateurs'],
    ['label' => 'Clients', 'icon' => 'fa-solid fa-user', 'url' => '/admin/clients'],
    ['label' => 'Avis', 'icon' => 'fa-solid fa-star', 'url' => '/admin/avis'],
] : [];
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="bg-gray-50 text-brand-brown font-sans min-h-screen flex">

<!-- Sidebar -->
<aside class="w-64 bg-brand-brown text-white h-screen fixed left-0 top-0 flex flex-col z-20 -translate-x-full md:translate-x-0">
    <!-- Logo -->
    <div class="px-6 py-4">
        <div class="flex items-center gap-2">
            <span class="w-8 h-8 rounded-full bg-brand-orange flex items-center justify-center font-serif font-bold text-sm">S</span>
            <span class="font-serif font-bold">SAVEUR 221</span>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 p-3">
        <a href="<?= $base . $prefixe ?>"
           class="flex items-center gap-3 px-4 py-2 rounded-xl text-sm transition <?= $chemin === $prefixe ? 'bg-brand-orange text-white shadow-sm' : 'text-white/70 hover:bg-white/10 hover:text-white' ?>">
            <i class="fa-solid fa-gauge-high text-sm w-5 text-center"></i>
            <span>Dashboard</span>
        </a>

        <?= $rendreTitre('Gestion du Restaurant') ?>
        <div class="space-y-0.5">
            <?php foreach ($liensGestion as $lien): ?>
                <?= $rendreLien($lien, $estActif($lien['url'])) ?>
            <?php endforeach; ?>
        </div>

        <?php if (!empty($liensAdministration)): ?>
            <?= $rendreTitre('Administration') ?>
            <div class="space-y-0.5">
                <?php foreach ($liensAdministration as $lien): ?>
                    <?= $rendreLien($lien, $estActif($lien['url'])) ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </nav>

    <!-- Déconnexion -->
    <div class="px-4 py-3">
        <a href="<?= $base ?>/deconnexion"
           class="flex items-center gap-2 px-4 py-2 rounded-xl text-sm bg-white/5 text-white/70 hover:bg-white/10 hover:text-white transition">
            <i class="fa-solid fa-right-from-bracket"></i> Déconnexion
        </a>
    </div>
</aside>

<!-- Contenu principal -->
<div class="flex-1 ml-0 md:ml-64">

    <!-- Header -->
    <header class="bg-white border-b border-gray-200 px-4 md:px-8 py-3 flex items-center justify-between gap-4 sticky top-0 z-10">
        <h1 class="font-serif text-lg md:text-xl font-bold truncate"><?= e($pageTitle ?? 'Dashboard') ?></h1>
        <div class="flex items-center gap-4 md:gap-6 shrink-0">
            <button type="button" aria-label="Rechercher" class="hidden sm:block text-gray-400 hover:text-brand-orange transition">
                <i class="fa-solid fa-magnifying-glass text-lg"></i>
            </button>
            <button type="button" aria-label="Notifications" class="relative text-gray-400 hover:text-brand-orange transition">
                <i class="fa-regular fa-bell text-lg"></i>
                <span class="absolute -top-0.5 -right-0.5 w-2 h-2 rounded-full bg-red-500"></span>
            </button>
            <div class="relative pl-6 border-l border-gray-200" id="profil-bouton">
                <button type="button" onclick="toggleProfilMenu()" class="flex items-center gap-3 cursor-pointer group">
                    <div class="w-9 h-9 rounded-full bg-brand-orange/10 flex items-center justify-center text-brand-orange font-bold text-sm overflow-hidden shrink-0">
                        <?php if ($lienPhoto !== null): ?>
                            <img src="<?= e($lienPhoto) ?>" alt="Photo de profil" class="w-full h-full object-cover">
                        <?php else: ?>
                            <?= e($initiale) ?>
                        <?php endif; ?>
                    </div>
                    <div class="leading-tight hidden sm:block text-left">
                        <p class="text-sm font-semibold"><?= e($nomComplet) ?></p>
                        <p class="text-xs text-gray-500"><?= e($role) ?></p>
                    </div>
                    <i class="fa-solid fa-chevron-down text-xs text-gray-400 hidden sm:block group-hover:text-brand-orange transition"></i>
                </button>

                <div id="profil-menu" class="hidden absolute right-0 top-full mt-3 w-64 bg-white rounded-xl shadow-xl border border-gray-100 overflow-hidden z-30">
                    <div class="px-4 py-3 bg-gray-50/80 border-b border-gray-100 flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-brand-orange/10 flex items-center justify-center text-brand-orange font-bold text-sm overflow-hidden shrink-0">
                            <?php if ($lienPhoto !== null): ?>
                                <img src="<?= e($lienPhoto) ?>" alt="Photo de profil" class="w-full h-full object-cover">
                            <?php else: ?>
                                <?= e($initiale) ?>
                            <?php endif; ?>
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-semibold truncate"><?= e($nomComplet) ?></p>
                            <p class="text-xs text-gray-500 truncate"><?= e($email) ?></p>
                        </div>
                    </div>
                    <a href="<?= $base ?>/profil"
                       class="flex items-center gap-3 px-4 py-3 text-sm text-gray-700 hover:bg-brand-orange/5 hover:text-brand-orange transition">
                        <i class="fa-solid fa-user-pen w-4 text-center"></i> Modifier mes informations
                    </a>
                    <a href="<?= $base ?>/deconnexion"
                       class="flex items-center gap-3 px-4 py-3 text-sm text-red-600 hover:bg-red-50 transition border-t border-gray-100">
                        <i class="fa-solid fa-right-from-bracket w-4 text-center"></i> Déconnexion
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Contenu -->
    <main class="p-6 pb-28 md:p-8">
        <?php if ($message): ?>
            <div class="mb-6 px-4 py-3 rounded-xl text-sm
                <?= $message['type'] === 'error' ? 'bg-red-50 text-red-700 border border-red-200' : 'bg-green-50 text-green-700 border border-green-200' ?>">
                <?= e($message['message']) ?>
            </div>
        <?php endif; ?>

        <?= $content ?>
    </main>

</div>

<script>
    function openModal(id) {
        const el = document.getElementById(id);
        if (el) el.classList.remove('hidden');
    }
    function closeModal(id) {
        const el = document.getElementById(id);
        if (el) el.classList.add('hidden');
    }

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