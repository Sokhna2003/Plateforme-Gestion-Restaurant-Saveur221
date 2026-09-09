<?php

declare(strict_types=1);

$estAdmin = $estAdmin ?? false;
$estGerant = $estGerant ?? false;
$estClient = $estClient ?? false;

$nomComplet = nomComplet();
?>

<?php if ($estClient): ?>

<div class="max-w-2xl mx-auto text-center py-12">
    <div class="w-20 h-20 rounded-full bg-brand-orange/10 flex items-center justify-center mx-auto mb-6">
        <span class="text-brand-orange font-serif font-bold text-2xl"><?= strtoupper(substr($nomComplet, 0, 1)) ?></span>
    </div>
    <h1 class="font-serif text-3xl font-bold mb-2">Bienvenue, <?= e($nomComplet) ?> !</h1>
    <p class="text-brand-brown/60 mb-8">Consultez vos commandes et gérer votre compte.</p>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <a href="<?= $base ?>/menu" class="bg-white border border-brand-brown/10 rounded-xl p-6 hover:border-brand-orange transition">
            <div class="text-brand-orange text-2xl mb-2"><i class="fa-solid fa-utensils"></i></div>
            <h3 class="font-serif font-bold mb-1">Voir le menu</h3>
            <p class="text-sm text-brand-brown/50">Découvrez nos plats</p>
        </a>
        <a href="<?= $base ?>/client" class="bg-white border border-brand-brown/10 rounded-xl p-6 hover:border-brand-orange transition">
            <div class="text-brand-orange text-2xl mb-2"><i class="fa-solid fa-receipt"></i></div>
            <h3 class="font-serif font-bold mb-1">Mes commandes</h3>
            <p class="text-sm text-brand-brown/50">Historique et suivi</p>
        </a>
    </div>
</div>

<?php elseif ($estGerant): ?>

<div class="max-w-4xl mx-auto py-8">
    <h1 class="font-serif text-2xl font-bold mb-1">Tableau de bord</h1>
    <p class="text-brand-brown/60 mb-6">Bienvenue, <?= e($nomComplet) ?>.</p>

    <?php if ($estAdmin): ?>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
        <div class="bg-white border border-brand-brown/10 rounded-xl p-6">
            <p class="text-sm text-brand-brown/50 mb-1">Commandes du jour</p>
            <p class="font-serif text-2xl font-bold text-brand-orange">—</p>
        </div>
        <div class="bg-white border border-brand-brown/10 rounded-xl p-6">
            <p class="text-sm text-brand-brown/50 mb-1">Chiffre d'affaires</p>
            <p class="font-serif text-2xl font-bold text-brand-orange">—</p>
        </div>
        <div class="bg-white border border-brand-brown/10 rounded-xl p-6">
            <p class="text-sm text-brand-brown/50 mb-1">Clients</p>
            <p class="font-serif text-2xl font-bold text-brand-orange">—</p>
        </div>
    </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <?php if ($estAdmin): ?>
        <a href="<?= $base ?>/admin/utilisateurs" class="bg-white border border-brand-brown/10 rounded-xl p-6 hover:border-brand-orange transition">
            <h3 class="font-serif font-bold mb-1">Gestion utilisateurs</h3>
            <p class="text-sm text-brand-brown/50">Gérer les comptes</p>
        </a>
        <?php endif; ?>
        <a href="<?= $base ?>/admin/categories" class="bg-white border border-brand-brown/10 rounded-xl p-6 hover:border-brand-orange transition">
            <h3 class="font-serif font-bold mb-1">Catégories</h3>
            <p class="text-sm text-brand-brown/50">Gérer les catégories</p>
        </a>
        <a href="<?= $base ?>/admin/produits" class="bg-white border border-brand-brown/10 rounded-xl p-6 hover:border-brand-orange transition">
            <h3 class="font-serif font-bold mb-1">Produits</h3>
            <p class="text-sm text-brand-brown/50">Gérer le catalogue</p>
        </a>
        <a href="<?= $base ?>/admin/commandes" class="bg-white border border-brand-brown/10 rounded-xl p-6 hover:border-brand-orange transition">
            <h3 class="font-serif font-bold mb-1">Commandes</h3>
            <p class="text-sm text-brand-brown/50">Gérer les commandes</p>
        </a>
    </div>
</div>

<?php endif; ?>
