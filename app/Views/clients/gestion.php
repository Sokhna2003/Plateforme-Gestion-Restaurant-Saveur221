<?php

declare(strict_types=1);

/**
 * @var \App\Models\Client[] $clients
 * @var array{totalClients: int, avecCommandes: int, commandes: int, chiffreAffaires: float} $stats
 * @var int $page
 * @var int $totalPages
 * @var int $total
 * @var string $motCle
 * @var string $avecCommandes ('' | 'avec' | 'sans')
 * @var string $baseRoute (/admin)
 */

$lienBase = $base . $baseRoute . '/clients';
?>

<!-- ============================== EN-TETE ============================== -->
<div class="flex flex-col md:flex-row md:items-end md:justify-between gap-6 mb-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-900">Clients</h2>
        <p class="text-sm text-gray-500 mt-1">Consultez la liste des clients inscrits sur la plateforme.</p>
    </div>
    <span class="inline-flex items-center gap-2 px-4 py-2 bg-brand-orange/10 text-brand-orange rounded-full text-sm font-semibold whitespace-nowrap">
        <i class="fa-solid fa-users"></i> <?= (int) $stats['totalClients'] ?> client<?= $stats['totalClients'] > 1 ? 's' : '' ?>
    </span>
</div>

<!-- ============================== CARTES STATISTIQUES ============================== -->
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 border-t-4 border-t-brand-orange p-5">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Total clients</p>
                <p class="mt-3 text-3xl font-bold text-gray-900"><?= (int) $stats['totalClients'] ?></p>
            </div>
            <span class="w-10 h-10 rounded-full bg-brand-orange/10 flex items-center justify-center text-brand-orange">
                <i class="fa-solid fa-users"></i>
            </span>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 border-t-4 border-t-blue-500 p-5">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Clients avec commandes</p>
                <p class="mt-3 text-3xl font-bold text-gray-900"><?= (int) $stats['avecCommandes'] ?></p>
            </div>
            <span class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600">
                <i class="fa-solid fa-basket-shopping"></i>
            </span>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 border-t-4 border-t-amber-400 p-5">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Commandes passées</p>
                <p class="mt-3 text-3xl font-bold text-gray-900"><?= (int) $stats['commandes'] ?></p>
            </div>
            <span class="w-10 h-10 rounded-full bg-amber-100 flex items-center justify-center text-amber-600">
                <i class="fa-solid fa-receipt"></i>
            </span>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 border-t-4 border-t-green-500 p-5">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Chiffre d'affaires</p>
                <p class="mt-3 text-2xl font-bold text-gray-900 truncate"><?= number_format((float) $stats['chiffreAffaires'], 0, ',', ' ') ?></p>
                <p class="text-xs text-gray-400 mt-0.5">FCFA</p>
            </div>
            <span class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center text-green-600">
                <i class="fa-solid fa-coins"></i>
            </span>
        </div>
    </div>
</div>

<!-- ============================== FILTRES ============================== -->
<form method="get" action="<?= $lienBase ?>"
      class="bg-white border border-gray-200 rounded-xl p-4 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-2 mb-6">
    <div class="relative lg:max-w-md w-full">
        <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
        <input type="text" name="q" value="<?= e($motCle) ?>"
               placeholder="Rechercher un client (nom, prénom, email, téléphone)..."
               class="w-full border border-gray-200 rounded-lg pl-9 pr-3 py-1.5 text-xs bg-white focus:outline-none focus:border-brand-orange">
    </div>

    <div class="flex flex-wrap items-center gap-2">
        <select name="commandes" class="border border-gray-200 rounded-lg px-2.5 py-1.5 text-xs bg-white focus:outline-none focus:border-brand-orange">
            <option value="">Tous les clients</option>
            <option value="avec" <?= $avecCommandes === 'avec' ? 'selected' : '' ?>>Avec commandes</option>
            <option value="sans" <?= $avecCommandes === 'sans' ? 'selected' : '' ?>>Sans commande</option>
        </select>

        <button type="submit"
                class="px-3 py-1.5 bg-brand-orange text-white text-xs font-medium rounded-lg hover:opacity-90 transition">
            <i class="fa-solid fa-filter mr-1"></i> Filtrer
        </button>
        <a href="<?= $lienBase ?>"
           class="px-3 py-1.5 text-xs font-medium text-gray-500 border border-gray-200 rounded-lg hover:bg-gray-50 transition">
            Réinitialiser
        </a>
    </div>
</form>

<?php if (empty($clients)): ?>
    <div class="bg-white border border-gray-200 rounded-xl p-12 text-center text-gray-500 text-sm">
        <i class="fa-solid fa-user mb-3 text-3xl text-gray-300 block"></i>
        <?= ($motCle !== '' || $avecCommandes !== '') ? 'Aucun client ne correspond à vos critères.' : 'Aucun client pour le moment.' ?>
    </div>
<?php else: ?>

<div class="mt-4 bg-white border border-gray-200 rounded-xl overflow-hidden">
    <div class="flex items-center justify-between px-6 py-3 border-b border-gray-100">
        <span class="text-xs text-gray-400"><?= (string) $total ?> client<?= $total > 1 ? 's' : '' ?></span>
    </div>

    <div class="overflow-x-auto">
    <table class="w-full text-sm">
        <thead>
            <tr class="bg-gray-50 text-left text-xs uppercase tracking-wide text-gray-500 whitespace-nowrap">
                <th class="px-4 py-3 font-medium">Client</th>
                <th class="px-4 py-3 font-medium">Contact</th>
                <th class="px-4 py-3 font-medium">Commandes</th>
                <th class="px-4 py-3 font-medium">Total dépensé</th>
                <th class="px-4 py-3 font-medium">Inscrit le</th>
                <th class="px-4 py-3 font-medium text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            <?php foreach ($clients as $c): ?>
            <tr class="hover:bg-gray-50/50 transition">
                <td class="px-4 py-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-brand-orange/10 flex items-center justify-center text-brand-orange font-bold text-sm shrink-0">
                            <?= e($c->initiales()) ?>
                        </div>
                        <div class="min-w-0">
                            <p class="font-medium text-gray-900 truncate"><?= e($c->nomComplet()) ?></p>
                            <p class="text-xs text-gray-500 truncate"><?= e($c->email) ?></p>
                        </div>
                    </div>
                </td>
                <td class="px-4 py-4">
                    <p class="text-gray-900"><?= e($c->telephone ?? '—') ?></p>
                    <p class="text-xs text-gray-500 truncate max-w-[14rem]"><?= e($c->adresse ?? '') ?></p>
                </td>
                <td class="px-4 py-4">
                    <?php if (($c->nbCommandes ?? 0) > 0): ?>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 whitespace-nowrap">
                            <i class="fa-solid fa-receipt text-[10px]"></i> <?= (int) $c->nbCommandes ?>
                        </span>
                    <?php else: ?>
                        <span class="text-xs text-gray-400">Aucune</span>
                    <?php endif; ?>
                </td>
                <td class="px-4 py-4 text-gray-900 text-xs font-medium whitespace-nowrap"><?= e($c->totalDepenseFormate()) ?></td>
                <td class="px-4 py-4 text-gray-500 text-xs whitespace-nowrap"><?= e($c->dateInscriptionFormatee()) ?></td>
                <td class="px-4 py-4">
                    <div class="flex items-center justify-end gap-1.5">
                        <a href="<?= $lienBase ?>/<?= $c->id ?>"
                           class="w-7 h-7 flex items-center justify-center rounded-lg bg-brand-orange/10 text-brand-orange hover:bg-brand-orange hover:text-white transition"
                           title="Voir le profil">
                            <i class="fa-solid fa-eye text-xs"></i>
                        </a>
                        <button type="button" onclick="openModal('suppr-modal-<?= $c->id ?>')"
                                class="w-7 h-7 flex items-center justify-center rounded-lg bg-red-50 text-red-500 hover:bg-red-500 hover:text-white transition"
                                title="Supprimer">
                            <i class="fa-solid fa-trash text-xs"></i>
                        </button>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    </div>
</div>

<?php
    $lienBasePagination = $lienBase;
    require __DIR__ . '/../partials/pagination.php';
?>

<?php endif; ?>

<!-- ============================== MODALES SUPPRESSION ============================== -->
<?php foreach ($clients as $c): ?>
    <div id="suppr-modal-<?= $c->id ?>" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50" onclick="closeModal('suppr-modal-<?= $c->id ?>')"></div>
        <div class="relative bg-white rounded-2xl shadow-xl max-w-md w-full p-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-full <?= ($c->nbCommandes ?? 0) > 0 ? 'bg-amber-50 text-amber-600' : 'bg-red-50 text-red-500' ?> flex items-center justify-center text-xl shrink-0">
                    <i class="fa-solid <?= ($c->nbCommandes ?? 0) > 0 ? 'fa-triangle-exclamation' : 'fa-trash-can' ?>"></i>
                </div>
                <div>
                    <h3 class="font-bold text-gray-900">Supprimer ce client ?</h3>
                    <p class="text-sm text-gray-500 mt-1">
                        <?php if (($c->nbCommandes ?? 0) > 0): ?>
                            « <?= e($c->nomComplet()) ?> » possède des commandes, sa suppression est impossible.
                        <?php else: ?>
                            Le compte « <?= e($c->nomComplet()) ?> » sera définitivement supprimé.
                        <?php endif; ?>
                    </p>
                </div>
            </div>
            <div class="mt-6 flex items-center justify-end gap-3">
                <button type="button" onclick="closeModal('suppr-modal-<?= $c->id ?>')"
                        class="px-4 py-2 rounded-lg text-sm font-medium text-gray-600 border border-gray-200 hover:bg-gray-50 transition">
                    Fermer
                </button>
                <?php if (($c->nbCommandes ?? 0) === 0): ?>
                    <form method="post" action="<?= $lienBase ?>/<?= $c->id ?>/supprimer">
                        <input type="hidden" name="_token" value="<?= csrf() ?>">
                        <button type="submit"
                                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-white bg-red-500 hover:bg-red-600 transition">
                            <i class="fa-solid fa-trash text-xs"></i> Supprimer
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php endforeach; ?>