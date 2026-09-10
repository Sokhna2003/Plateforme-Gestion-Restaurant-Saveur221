<?php

declare(strict_types=1);

/**
 * @var \App\Models\Avis $avis
 * @var string $baseRoute (/admin)
 */

use App\Models\Avis;
use App\Models\Commande;

$lienAvis = $base . $baseRoute . '/avis';
$lienCommandes = $base . $baseRoute . '/commandes';

$etoiles = static function (int $nb) {
    $html = '<div class="flex items-center gap-0.5">';
    for ($i = 1; $i <= 5; $i++) {
        $html .= $i <= $nb
            ? '<i class="fa-solid fa-star text-amber-400 text-base"></i>'
            : '<i class="fa-regular fa-star text-gray-300 text-base"></i>';
    }
    $html .= '</div>';
    return $html;
};

$badgeStatut = static function (string $statut): string {
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
<a href="<?= $lienAvis ?>"
   class="inline-flex items-center gap-2 text-sm font-medium text-brand-orange hover:opacity-80 transition mb-6">
    <i class="fa-solid fa-arrow-left"></i> Retour aux avis
</a>

<!-- ============================== DETAIL AVIS ============================== -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <!-- Client -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <p class="text-xs text-gray-400 uppercase tracking-wide mb-4">Client</p>
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-full bg-brand-orange/10 flex items-center justify-center text-brand-orange font-bold text-lg shrink-0">
                <?= e($avis->initiales()) ?>
            </div>
            <div class="min-w-0">
                <h3 class="font-bold text-gray-900 truncate"><?= e($avis->clientNomComplet()) ?></h3>
                <p class="text-sm text-gray-500 truncate"><?= e($avis->clientEmail ?? '') ?></p>
            </div>
        </div>
        <dl class="mt-5 space-y-3 text-sm">
            <div class="flex items-start gap-3">
                <i class="fa-solid fa-phone text-gray-400 mt-0.5 w-4 text-center"></i>
                <dd class="text-gray-900"><?= e($avis->clientTelephone ?? '—') ?></dd>
            </div>
            <div class="flex items-start gap-3">
                <i class="fa-solid fa-location-dot text-gray-400 mt-0.5 w-4 text-center"></i>
                <dd class="text-gray-900"><?= e($avis->clientAdresse ?? '—') ?></dd>
            </div>
        </dl>
    </div>

    <!-- Avis -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 border-t-4 border-t-amber-400 p-6 lg:col-span-1">
        <div class="flex items-start justify-between gap-4">
            <p class="text-xs text-gray-400 uppercase tracking-wide">Avis n°<?= $avis->id ?></p>
            <p class="text-xs text-gray-400 whitespace-nowrap"><?= e($avis->dateAvisFormatee()) ?></p>
        </div>
        <div class="mt-4">
            <?= $etoiles($avis->note) ?>
            <p class="text-sm font-semibold text-gray-900 mt-1.5"><?= $avis->note ?>/5</p>
        </div>
        <div class="mt-4">
            <p class="text-xs text-gray-400 uppercase tracking-wide mb-2">Commentaire</p>
            <?php if ($avis->commentaire !== null && $avis->commentaire !== ''): ?>
                <p class="text-sm text-gray-700 whitespace-pre-line leading-relaxed"><?= e($avis->commentaire) ?></p>
            <?php else: ?>
                <p class="text-sm text-gray-400 italic">Aucun commentaire.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Commande liée -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 border-t-4 border-t-blue-500 p-6 lg:col-span-1">
        <p class="text-xs text-gray-400 uppercase tracking-wide mb-4">Commande associée</p>
        <p class="text-lg font-bold text-gray-900">Commande n°<?= $avis->commandeId ?></p>
        <div class="mt-3 flex items-center gap-2">
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium <?= $badgeStatut($avis->commandeStatut ?? '') ?> whitespace-nowrap">
                <i class="fa-solid fa-circle text-[6px]"></i>
                <?= e(Commande::LIBELLES[$avis->commandeStatut ?? ''] ?? ($avis->commandeStatut ?? '')) ?>
            </span>
        </div>
        <p class="text-xs text-gray-500 mt-3">Passée le <?= e($avis->commandeDateFormatee()) ?></p>
        <p class="text-sm font-semibold text-gray-900 mt-1"><?= number_format((float) ($avis->commandeMontantTotal ?? 0), 0, ',', ' ') ?> FCFA</p>
        <a href="<?= $lienCommandes ?>/<?= $avis->commandeId ?>"
           class="inline-flex items-center gap-2 mt-4 px-3 py-1.5 rounded-lg text-xs font-medium text-brand-orange bg-brand-orange/10 hover:bg-brand-orange hover:text-white transition">
            <i class="fa-solid fa-eye text-xs"></i> Voir la commande
        </a>
    </div>
</div>

<!-- Suppression -->
<div class="flex items-center justify-end bg-white border border-red-100 rounded-xl px-6 py-4">
    <div class="flex items-center gap-4">
        <p class="text-sm text-gray-500">Supprimer définitivement cet avis ?</p>
        <button type="button" onclick="openModal('suppr-modal-detail')"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-white bg-red-500 hover:bg-red-600 transition">
            <i class="fa-solid fa-trash text-xs"></i> Supprimer
        </button>
    </div>
</div>

<div id="suppr-modal-detail" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/50" onclick="closeModal('suppr-modal-detail')"></div>
    <div class="relative bg-white rounded-2xl shadow-xl max-w-md w-full p-6">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-red-50 flex items-center justify-center text-red-500 text-xl shrink-0">
                <i class="fa-solid fa-trash-can"></i>
            </div>
            <h3 class="font-bold text-gray-900">Supprimer cet avis ?</h3>
        </div>
        <p class="text-sm text-gray-500 mt-2">
            L'avis de « <?= e($avis->clientNomComplet()) ?> » (<?= $avis->note ?>/5) sera définitivement supprimé.
        </p>
        <div class="mt-6 flex items-center justify-end gap-3">
            <button type="button" onclick="closeModal('suppr-modal-detail')"
                    class="px-4 py-2 rounded-lg text-sm font-medium text-gray-600 border border-gray-200 hover:bg-gray-50 transition">
                Annuler
            </button>
            <form method="post" action="<?= $lienAvis ?>/<?= $avis->id ?>/supprimer">
                <input type="hidden" name="_token" value="<?= csrf() ?>">
                <button type="submit"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-white bg-red-500 hover:bg-red-600 transition">
                    <i class="fa-solid fa-trash text-xs"></i> Supprimer
                </button>
            </form>
        </div>
    </div>
</div>