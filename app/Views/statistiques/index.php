<?php

declare(strict_types=1);

/**
 * @var string $periode (jour | semaine | mois)
 * @var string $baseRoute (/admin ou /gerant)
 */

$lienBase = $base . $baseRoute . '/stats';

$pills = [
    ['jour', 'Aujourd\'hui'],
    ['semaine', 'Cette semaine'],
    ['mois', 'Ce mois'],
];
?>

<!-- En-tête : titre + texte à gauche, filtre à droite -->
<div class="mb-6 flex flex-wrap items-center justify-between gap-4">
    <div>
        <h2 class="text-2xl font-bold text-gray-900">Performances &amp; Analyse</h2>
        <p class="text-sm text-gray-500 mt-1">Aperçu de la croissance et des produits populaires.</p>
    </div>
    <div class="flex flex-wrap gap-2">
        <?php foreach ($pills as [$valeur, $libelle]): ?>
            <a href="<?= e($lienBase) ?>?periode=<?= e($valeur) ?>"
               class="px-4 py-1.5 rounded-full text-sm border <?= $periode === $valeur ? 'bg-brand-orange text-white border-brand-orange' : 'border-brand-brown/20 hover:border-brand-orange' ?>">
                <?= e($libelle) ?>
            </a>
        <?php endforeach; ?>
    </div>
</div>

<!-- Cartes chiffre d'affaires (valeurs et pourcentages non dynamiques) -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 border-t-4 border-t-green-500 p-5">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Chiffre d'affaires du jour</p>
                <p class="mt-3 text-2xl font-bold text-gray-900">
                    486 500 <small class="text-xs font-medium text-gray-400">FCFA</small>
                </p>
            </div>
            <span class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center text-green-600">
                <i class="fa-solid fa-money-bill-trend-up"></i>
            </span>
        </div>
        <p class="mt-3 text-sm font-medium text-green-600">
            <i class="fa-solid fa-arrow-trend-up text-xs mr-1"></i>+15,2 %
        </p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 border-t-4 border-t-blue-500 p-5">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Chiffre d'affaires de la semaine</p>
                <p class="mt-3 text-2xl font-bold text-gray-900">
                    2 340 750 <small class="text-xs font-medium text-gray-400">FCFA</small>
                </p>
            </div>
            <span class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600">
                <i class="fa-solid fa-chart-line"></i>
            </span>
        </div>
        <p class="mt-3 text-sm font-medium text-blue-600">
            <i class="fa-solid fa-arrow-trend-up text-xs mr-1"></i>+8,4 %
        </p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 border-t-4 border-t-brand-orange p-5">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Chiffre d'affaires du mois</p>
                <p class="mt-3 text-2xl font-bold text-gray-900">
                    9 687 500 <small class="text-xs font-medium text-gray-400">FCFA</small>
                </p>
            </div>
            <span class="w-10 h-10 rounded-full bg-brand-orange/10 flex items-center justify-center text-brand-orange">
                <i class="fa-solid fa-calendar-days"></i>
            </span>
        </div>
        <p class="mt-3 text-sm font-medium text-brand-orange">
            <i class="fa-solid fa-arrow-trend-up text-xs mr-1"></i>+4,1 %
        </p>
    </div>
</div>

<!-- Graphiques : chiffre d'affaires, évolution des commandes, commandes par statut -->
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 mb-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-semibold text-gray-900 text-sm">Chiffre d'affaires</h3>
            <i class="fa-solid fa-chart-column text-brand-orange"></i>
        </div>
        <div class="p-4">
            <img src="<?= e($base . '/assets/images/chiffre-d%27affaires.png') ?>" alt="Chiffre d'affaires"
                 class="w-full h-auto rounded-lg">
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-semibold text-gray-900 text-sm">Évolution du nombre de commandes</h3>
            <i class="fa-solid fa-chart-line text-brand-orange"></i>
        </div>
        <div class="p-4">
            <img src="<?= e($base . '/assets/images/evolution-nombre%20-commandes.png') ?>" alt="Évolution du nombre de commandes"
                 class="w-full h-auto rounded-lg">
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-semibold text-gray-900 text-sm">Commandes par statut</h3>
            <i class="fa-solid fa-chart-pie text-brand-orange"></i>
        </div>
        <div class="p-4">
            <img src="<?= e($base . '/assets/images/Commandes-par-statut.png') ?>" alt="Commandes par statut"
                 class="w-full h-auto rounded-lg">
        </div>
    </div>
</div>

<!-- Graphiques : chiffre d'affaires, évolution des commandes, commandes par statut -->
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 mb-6">
    <!-- Graphique : produits les plus vendus -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-semibold text-gray-900 text-sm">Produits les plus vendus</h3>
            <i class="fa-solid fa-trophy text-brand-orange"></i>
        </div>
        <div class="p-4">
            <img src="<?= e($base . '/assets/images/produits-plus-vendus.png') ?>" alt="Produits les plus vendus"
                class="w-full h-auto rounded-lg">
        </div>
    </div>

    <div class=""></div>

    <div class=""></div>
</div>

