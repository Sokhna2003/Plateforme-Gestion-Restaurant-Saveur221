<?php

declare(strict_types=1);

/**
 * @var \App\Models\Avis[] $avis
 * @var array{total: int, noteMoyenne: float, cinqEtoiles: int, avecCommentaire: int} $stats
 * @var int $page
 * @var int $totalPages
 * @var int $total
 * @var string $motCle
 * @var int|null $note (null | 1..5)
 * @var string $baseRoute (/admin)
 */

use App\Models\Avis;

$lienBase = $base . $baseRoute . '/avis';

$etoiles = static function (int $nb) {
    $html = '<div class="flex items-center gap-0.5">';
    for ($i = 1; $i <= 5; $i++) {
        $html .= $i <= $nb
            ? '<i class="fa-solid fa-star text-amber-400 text-xs"></i>'
            : '<i class="fa-regular fa-star text-gray-300 text-xs"></i>';
    }
    $html .= '</div>';
    return $html;
};
?>

<!-- ============================== EN-TETE ============================== -->
<div class="flex flex-col md:flex-row md:items-end md:justify-between gap-6 mb-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-900">Avis clients</h2>
        <p class="text-sm text-gray-500 mt-1">Consultez et modérez les avis laissés par les clients après leurs commandes.</p>
    </div>
    <span class="inline-flex items-center gap-2 px-4 py-2 bg-brand-orange/10 text-brand-orange rounded-full text-sm font-semibold whitespace-nowrap">
        <i class="fa-solid fa-star"></i> <?= (int) $stats['total'] ?> avis
    </span>
</div>

<!-- ============================== CARTES STATISTIQUES ============================== -->
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 border-t-4 border-t-blue-500 p-5">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Total avis</p>
                <p class="mt-3 text-3xl font-bold text-gray-900"><?= (int) $stats['total'] ?></p>
            </div>
            <span class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600">
                <i class="fa-solid fa-star"></i>
            </span>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 border-t-4 border-t-brand-orange p-5">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Note moyenne</p>
                <?php if ($stats['total'] > 0): ?>
                    <p class="mt-3 text-2xl font-bold text-gray-900 truncate"><?= number_format((float) $stats['noteMoyenne'], 1, ',', ' ') ?></p>
                    <p class="text-xs text-gray-400 mt-0.5">sur 5</p>
                <?php else: ?>
                    <p class="mt-3 text-2xl font-bold text-gray-300">—</p>
                <?php endif; ?>
            </div>
            <span class="w-10 h-10 rounded-full bg-brand-orange/10 flex items-center justify-center text-brand-orange">
                <i class="fa-solid fa-star-half-stroke"></i>
            </span>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 border-t-4 border-t-amber-400 p-5">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">5 étoiles</p>
                <p class="mt-3 text-3xl font-bold text-gray-900"><?= (int) $stats['cinqEtoiles'] ?></p>
            </div>
            <span class="w-10 h-10 rounded-full bg-amber-100 flex items-center justify-center text-amber-600">
                <i class="fa-solid fa-star"></i>
            </span>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 border-t-4 border-t-green-500 p-5">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Avis avec commentaire</p>
                <p class="mt-3 text-3xl font-bold text-gray-900"><?= (int) $stats['avecCommentaire'] ?></p>
            </div>
            <span class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center text-green-600">
                <i class="fa-solid fa-comment"></i>
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
               placeholder="Rechercher un client ou un commentaire..."
               class="w-full border border-gray-200 rounded-lg pl-9 pr-3 py-1.5 text-xs bg-white focus:outline-none focus:border-brand-orange">
    </div>

    <div class="flex flex-wrap items-center gap-2">
        <select name="note" class="border border-gray-200 rounded-lg px-2.5 py-1.5 text-xs bg-white focus:outline-none focus:border-brand-orange">
            <option value="">Toutes les notes</option>
            <?php for ($i = Avis::NOTE_MIN; $i <= Avis::NOTE_MAX; $i++): ?>
                <option value="<?= $i ?>" <?= $note === $i ? 'selected' : '' ?>><?= $i ?> étoile<?= $i > 1 ? 's' : '' ?></option>
            <?php endfor; ?>
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

<?php if (empty($avis)): ?>
    <div class="bg-white border border-gray-200 rounded-xl p-12 text-center text-gray-500 text-sm">
        <i class="fa-solid fa-star mb-3 text-3xl text-gray-300 block"></i>
        <?= ($motCle !== '' || $note !== null) ? 'Aucun avis ne correspond à vos critères.' : 'Aucun avis pour le moment.' ?>
    </div>
<?php else: ?>

<div class="mt-4 bg-white border border-gray-200 rounded-xl overflow-hidden">
    <div class="flex items-center justify-between px-6 py-3 border-b border-gray-100">
        <span class="text-xs text-gray-400"><?= (string) $total ?> avis</span>
    </div>

    <div class="overflow-x-auto">
    <table class="w-full text-sm">
        <thead>
            <tr class="bg-gray-50 text-left text-xs uppercase tracking-wide text-gray-500 whitespace-nowrap">
                <th class="px-4 py-3 font-medium">Client</th>
                <th class="px-4 py-3 font-medium">Note</th>
                <th class="px-4 py-3 font-medium">Commentaire</th>
                <th class="px-4 py-3 font-medium">Commande</th>
                <th class="px-4 py-3 font-medium">Date</th>
                <th class="px-4 py-3 font-medium text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            <?php foreach ($avis as $a): ?>
            <tr class="hover:bg-gray-50/50 transition">
                <td class="px-4 py-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-brand-orange/10 flex items-center justify-center text-brand-orange font-bold text-sm shrink-0">
                            <?= e($a->initiales()) ?>
                        </div>
                        <div class="min-w-0">
                            <p class="font-medium text-gray-900 truncate"><?= e($a->clientNomComplet()) ?></p>
                            <p class="text-xs text-gray-500 truncate"><?= e($a->clientEmail ?? '') ?></p>
                        </div>
                    </div>
                </td>
                <td class="px-4 py-4">
                    <?= $etoiles($a->note) ?>
                    <p class="text-xs text-gray-400 mt-1"><?= $a->note ?>/5</p>
                </td>
                <td class="px-4 py-4">
                    <?php if ($a->commentaire !== null && $a->commentaire !== ''): ?>
                        <p class="text-gray-700 max-w-[18rem] truncate"><?= e($a->commentaireCourt(70)) ?></p>
                    <?php else: ?>
                        <span class="text-xs text-gray-400">Aucun commentaire</span>
                    <?php endif; ?>
                </td>
                <td class="px-4 py-4">
                    <a href="<?= $base . $baseRoute ?>/commandes/<?= $a->commandeId ?>"
                       class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600 hover:bg-brand-orange/10 hover:text-brand-orange transition whitespace-nowrap">
                        n°<?= $a->commandeId ?>
                    </a>
                </td>
                <td class="px-4 py-4 text-gray-500 text-xs whitespace-nowrap"><?= e($a->dateAvisFormatee()) ?></td>
                <td class="px-4 py-4">
                    <div class="flex items-center justify-end gap-1.5">
                        <a href="<?= $lienBase ?>/<?= $a->id ?>"
                           class="w-7 h-7 flex items-center justify-center rounded-lg bg-brand-orange/10 text-brand-orange hover:bg-brand-orange hover:text-white transition"
                           title="Voir l'avis">
                            <i class="fa-solid fa-eye text-xs"></i>
                        </a>
                        <button type="button" onclick="openModal('suppr-modal-<?= $a->id ?>')"
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
<?php foreach ($avis as $a): ?>
    <div id="suppr-modal-<?= $a->id ?>" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50" onclick="closeModal('suppr-modal-<?= $a->id ?>')"></div>
        <div class="relative bg-white rounded-2xl shadow-xl max-w-md w-full p-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-red-50 flex items-center justify-center text-red-500 text-xl shrink-0">
                    <i class="fa-solid fa-trash-can"></i>
                </div>
                <div>
                    <h3 class="font-bold text-gray-900">Supprimer cet avis ?</h3>
                    <p class="text-sm text-gray-500 mt-1">
                        L'avis de « <?= e($a->clientNomComplet()) ?> » (<?= $a->note ?>/5) sera définitivement supprimé.
                    </p>
                </div>
            </div>
            <div class="mt-6 flex items-center justify-end gap-3">
                <button type="button" onclick="closeModal('suppr-modal-<?= $a->id ?>')"
                        class="px-4 py-2 rounded-lg text-sm font-medium text-gray-600 border border-gray-200 hover:bg-gray-50 transition">
                    Annuler
                </button>
                <form method="post" action="<?= $lienBase ?>/<?= $a->id ?>/supprimer">
                    <input type="hidden" name="_token" value="<?= csrf() ?>">
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-white bg-red-500 hover:bg-red-600 transition">
                        <i class="fa-solid fa-trash text-xs"></i> Supprimer
                    </button>
                </form>
            </div>
        </div>
    </div>
<?php endforeach; ?>