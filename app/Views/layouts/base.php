<?php

declare(strict_types=1);

$message = flash_messages();

// Determiner le role depuis la session
$role = $_SESSION['user']['role'] ?? ($_SESSION['client'] ?? null !== null ? 'CLIENT' : '');
$isAdmin = ($role === 'ADMIN');
$isGerant = ($role === 'GERANT' || $isAdmin);
$nomComplet = nomComplet();
$currentPage = basename($_SERVER['REQUEST_URI'] ?? '');

// Liens sidebar selon le role
$liensAdmin = [
    ['label' => 'Dashboard', 'icon' => 'fa-solid fa-gauge-high', 'url' => '/admin'],
    ['label' => 'Utilisateurs', 'icon' => 'fa-solid fa-users', 'url' => '/admin/utilisateurs'],
    ['label' => 'Clients', 'icon' => 'fa-solid fa-user', 'url' => '/admin/clients'],
    ['label' => 'Catégories', 'icon' => 'fa-solid fa-folder-open', 'url' => '/admin/categories'],
    ['label' => 'Produits', 'icon' => 'fa-solid fa-utensils', 'url' => '/admin/produits'],
    ['label' => 'Stock', 'icon' => 'fa-solid fa-boxes-stacked', 'url' => '/admin/stock'],
    ['label' => 'Commandes', 'icon' => 'fa-solid fa-receipt', 'url' => '/admin/commandes'],
    ['label' => 'Paiements', 'icon' => 'fa-solid fa-credit-card', 'url' => '/admin/paiements'],
    ['label' => 'Avis', 'icon' => 'fa-solid fa-star', 'url' => '/admin/avis'],
    ['label' => 'Corbeille', 'icon' => 'fa-solid fa-trash-can', 'url' => '/admin/corbeille'],
    ['label' => 'Statistiques', 'icon' => 'fa-solid fa-chart-line', 'url' => '/admin/stats'],
];

$liensGerant = [
    ['label' => 'Dashboard', 'icon' => 'fa-solid fa-gauge-high', 'url' => '/gerant'],
    ['label' => 'Catégories', 'icon' => 'fa-solid fa-folder-open', 'url' => '/gerant/categories'],
    ['label' => 'Produits', 'icon' => 'fa-solid fa-utensils', 'url' => '/gerant/produits'],
    ['label' => 'Stock', 'icon' => 'fa-solid fa-boxes-stacked', 'url' => '/gerant/stock'],
    ['label' => 'Commandes', 'icon' => 'fa-solid fa-receipt', 'url' => '/gerant/commandes'],
    ['label' => 'Paiements', 'icon' => 'fa-solid fa-credit-card', 'url' => '/gerant/paiements'],
    ['label' => 'Corbeille', 'icon' => 'fa-solid fa-trash-can', 'url' => '/gerant/corbeille'],
    ['label' => 'Statistiques', 'icon' => 'fa-solid fa-chart-line', 'url' => '/gerant/stats'],
];

$liens = $isAdmin ? $liensAdmin : $liensGerant;
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
<aside class="w-64 bg-brand-brown text-white min-h-screen flex flex-col fixed left-0 top-0">
    <!-- Logo -->
    <div class="p-6 border-b border-white/10">
        <div class="flex items-center gap-2">
            <span class="w-8 h-8 rounded-full bg-brand-orange flex items-center justify-center font-serif font-bold text-sm">S</span>
            <span class="font-serif font-bold">SAVEUR 221</span>
        </div>
        <p class="text-white/50 text-xs mt-1"><?= e($role) ?></p>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 p-4 space-y-1">
        <?php foreach ($liens as $lien): ?>
            <a href="<?= $base . $lien['url'] ?>"
               class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm transition
                      <?= ($currentPage === ltrim($lien['url'], '/') || ($lien['url'] !== '/'.$role && $currentPage === ltrim($lien['url'], '/'))) ? 'bg-brand-orange text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' ?>">
                <i class="<?= $lien['icon'] ?> text-sm w-5 text-center"></i>
                <span><?= $lien['label'] ?></span>
            </a>
        <?php endforeach; ?>
    </nav>

    <!-- Profil + logout -->
    <div class="p-4 border-t border-white/10">
        <div class="flex items-center gap-3 mb-3">
            <div class="w-9 h-9 rounded-full bg-brand-orange/20 flex items-center justify-center text-brand-orange font-bold text-sm">
                <?= strtoupper(substr($nomComplet, 0, 1)) ?>
            </div>
            <div class="text-sm">
                <p class="font-medium text-white"><?= e($nomComplet) ?></p>
                <p class="text-white/50 text-xs"><?= e($role) ?></p>
            </div>
        </div>
        <a href="<?= $base ?>/deconnexion"
           class="flex items-center gap-2 px-4 py-2 rounded-xl text-sm text-white/60 hover:bg-white/10 hover:text-white transition">
            <i class="fa-solid fa-right-from-bracket"></i> Déconnexion
        </a>
    </div>
</aside>

<!-- Contenu principal -->
<div class="flex-1 ml-64">

    <!-- Header -->
    <header class="bg-white border-b border-gray-200 px-8 py-4 flex items-center justify-between sticky top-0 z-10">
        <h1 class="font-serif text-xl font-bold"><?= e($pageTitle ?? 'Dashboard') ?></h1>
        <div class="flex items-center gap-4">
            <span class="text-sm text-gray-500"><?= date('d/m/Y') ?></span>
            <div class="w-9 h-9 rounded-full bg-brand-orange/10 flex items-center justify-center text-brand-orange font-bold text-sm">
                <?= strtoupper(substr($nomComplet, 0, 1)) ?>
            </div>
        </div>
    </header>

    <!-- Contenu -->
    <main class="p-8">
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
</script>

</body>
</html>
