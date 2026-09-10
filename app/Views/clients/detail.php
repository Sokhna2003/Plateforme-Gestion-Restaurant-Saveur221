<?php

declare(strict_types=1);

/**
 * @var \App\Models\Client $client
 * @var \App\Models\Commande[] $commandes
 * @var string $baseRoute (/admin)
 */

use App\Models\Commande;

$lienClients = $base . $baseRoute . '/clients';
$lienCommandes = $base . $baseRoute . '/commandes';
$nbChiffre = 0;
foreach ($commandes as $cmd) {
    $nbChiffre += $cmd->montantTotal;
}

$badgeClasses = static function (string $statut): string {
    return match ($statut) {
        Commande::STATUT_EN_ATTENTE => 'bg-amber-100 text-amber-700',
        Commande::STATUT_EN_PREPARATION => 'bg-brand-orange/10 text-brand-orange',
        Commande::STATUT_PRETE => 'bg-blue-100 text-blue-700',
        Commande::STATUT_RETIREE => 'bg-green-100 text-green-700',
        default => 'bg-gray-100 text-gray-500',
    };
};
?>

<!-- Retour -->
<a href="<?= $lienClients ?>"
   class="inline-flex items-center gap-2 text-sm font-medium text-brand-orange hover:opacity-80 transition mb-6">
    <i class="fa-solid fa-arrow-left"></i> Retour aux clients
</a>

<!-- ============================== FICHE CLIENT ============================== -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <!-- Carte identité -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 lg:col-span-1">
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 rounded-full bg-brand-orange/10 flex items-center justify-center text-brand-orange font-bold text-xl shrink-0">
                <?= e($client->initiales()) ?>
            </div>
            <div class="min-w-0">
                <h3 class="text-lg font-bold text-gray-900 truncate"><?= e($client->nomComplet()) ?></h3>
                <p class="text-sm text-gray-500 truncate">Client depuis le <?= e($client->dateInscriptionFormatee()) ?></p>
            </div>
        </div>

        <dl class="mt-6 space-y-4 text-sm">
            <div class="flex items-start gap-3">
                <i class="fa-solid fa-envelope text-gray-400 mt-0.5 w-4 text-center"></i>
                <div>
                    <dt class="text-xs text-gray-400 uppercase tracking-wide">Email</dt>
                    <dd class="text-gray-900"><?= e($client->email) ?></dd>
                </div>
            </div>
            <div class="flex items-start gap-3">
                <i class="fa-solid fa-phone text-gray-400 mt-0.5 w-4 text-center"></i>
                <div>
                    <dt class="text-xs text-gray-400 uppercase tracking-wide">Téléphone</dt>
                    <dd class="text-gray-900"><?= e($client->telephone ?? '—') ?></dd>
                </div>
            </div>
            <div class="flex items-start gap-3">
                <i class="fa-solid fa-location-dot text-gray-400 mt-0.5 w-4 text-center"></i>
                <div>
                    <dt class="text-xs text-gray-400 uppercase tracking-wide">Adresse</dt>
                    <dd class="text-gray-900"><?= e($client->adresse ?? '—') ?></dd>
                </div>
            </div>
        </dl>
    </div>

    <!-- Statistiques -->
    <div class="lg:col-span-2 grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 border-t-4 border-t-brand-orange p-4">
            <p class="text-sm font-medium text-gray-500">Commandes</p>
            <p class="mt-2 text-2xl font-bold text-gray-900"><?= count($commandes) ?></p>
            <span class="inline-flex items-center gap-1.5 text-xs font-medium <?= $commandes ? 'text-brand-orange' : 'text-gray-400' ?> mt-1">
                <i class="fa-solid <?= $commandes ? 'fa-arrow-trend-up' : 'fa-minus' ?>"></i>
                <?= $commandes ? 'Client actif' : 'Aucune commande' ?>
            </span>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 border-t-4 border-t-green-500 p-4">
            <p class="text-sm font-medium text-gray-500">Total dépensé</p>
            <p class="mt-2 text-xl font-bold text-gray-900 truncate"><?= number_format($nbChiffre, 0, ',', ' ') ?></p>
            <p class="text-xs text-gray-400 mt-0.5">FCFA</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 border-t-4 border-t-amber-400 p-4">
            <p class="text-sm font-medium text-gray-500">Dernière commande</p>
            <?php if ($commandes): ?>
                <p class="mt-2 text-sm font-semibold text-gray-900">n°<?= $commandes[0]->id ?></p>
                <p class="text-xs text-gray-400 mt-1"><?= e($commandes[0]->dateFormatee()) ?></p>
            <?php else: ?>
                <p class="mt-2 text-2xl font-bold text-gray-300">—</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- ============================== HISTORIQUE DES COMMANDES ============================== -->
<div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
    <div class="flex items-center justify-between px-6 py-3 border-b border-gray-100">
        <span class="text-xs text-gray-400"><?= count($commandes) ?> commande<?= count($commandes) > 1 ? 's' : '' ?></span>
    </div>

    <?php if ($commandes): ?>
    <div class="overflow-x-auto">
    <table class="w-full text-sm">
        <thead>
            <tr class="bg-gray-50 text-left text-xs uppercase tracking-wide text-gray-500 whitespace-nowrap">
                <th class="px-4 py-3 font-medium">Commande</th>
                <th class="px-4 py-3 font-medium">Date</th>
                <th class="px-4 py-3 font-medium">Statut</th>
                <th class="px-4 py-3 font-medium">Montant</th>
                <th class="px-4 py-3 font-medium text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            <?php foreach ($commandes as $cmd): ?>
            <tr class="hover:bg-gray-50/50 transition">
                <td class="px-4 py-4 font-medium text-gray-900 whitespace-nowrap">Commande n°<?= $cmd->id ?></td>
                <td class="px-4 py-4 text-gray-500 text-xs whitespace-nowrap"><?= e($cmd->dateFormatee()) ?></td>
                <td class="px-4 py-4">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium <?= $badgeClasses($cmd->statut) ?> whitespace-nowrap">
                        <i class="fa-solid fa-circle text-[6px]"></i> <?= e($cmd->libelleStatut()) ?>
                    </span>
                </td>
                <td class="px-4 py-4 text-gray-900 text-xs font-medium whitespace-nowrap"><?= e($cmd->montantFormate()) ?></td>
                <td class="px-4 py-4">
                    <div class="flex items-center justify-end">
                        <a href="<?= $lienCommandes ?>/<?= $cmd->id ?>"
                           class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-medium text-brand-orange bg-brand-orange/10 hover:bg-brand-orange hover:text-white transition"
                           title="Voir la commande">
                            <i class="fa-solid fa-eye text-xs"></i> Voir
                        </a>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    </div>
    <?php else: ?>
    <div class="p-12 text-center text-gray-500 text-sm">
        <i class="fa-solid fa-receipt mb-3 text-3xl text-gray-300 block"></i>
        Ce client n'a encore passé aucune commande.
    </div>
    <?php endif; ?>
</div>