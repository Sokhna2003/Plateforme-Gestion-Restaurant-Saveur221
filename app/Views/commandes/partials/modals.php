<?php

declare(strict_types=1);

/**
 * Modales de commande : changement de statut + annulation.
 * Nécessite : $c (App\Models\Commande), $lienBase, et en option $produitsRecap.
 *
 * @var \App\Models\Commande $c
 * @var string $lienBase
 * @var string|null $produitsRecap
 */

use App\Models\Commande;

$produitsRecap = $produitsRecap ?? $c->produitsLibelles ?? '—';

$badgeClasses = static function (string $statut): string {
    return match ($statut) {
        Commande::STATUT_EN_ATTENTE => 'bg-amber-100 text-amber-700',
        Commande::STATUT_EN_PREPARATION => 'bg-brand-orange/10 text-brand-orange',
        Commande::STATUT_PRETE => 'bg-blue-100 text-blue-700',
        Commande::STATUT_RETIREE => 'bg-green-100 text-green-700',
        default => 'bg-gray-100 text-gray-500',
    };
};

if ($c->peutChangerStatut()): ?>

<!-- ============================== MODAL CHANGEMENT DE STATUT ============================== -->
<div id="statut-modal-<?= $c->id ?>" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/50" onclick="closeModal('statut-modal-<?= $c->id ?>')"></div>
    <div class="relative bg-white rounded-2xl shadow-xl max-w-md w-full p-6">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-brand-orange/10 flex items-center justify-center text-brand-orange text-xl shrink-0">
                <i class="fa-solid fa-arrow-right-arrow-left"></i>
            </div>
            <div>
                <h3 class="font-bold text-gray-900">Modifier le statut</h3>
                <p class="text-sm text-gray-500 mt-1">Commande n°<?= $c->id ?> — <?= e($c->clientNomComplet()) ?></p>
            </div>
        </div>

        <div class="mt-5 grid grid-cols-2 gap-4 text-sm">
            <div>
                <p class="text-xs text-gray-500 mb-1">Date</p>
                <p class="font-medium text-gray-900"><?= e($c->dateFormatee()) ?></p>
            </div>
            <div>
                <p class="text-xs text-gray-500 mb-1">Montant</p>
                <p class="font-medium text-gray-900"><?= e($c->montantFormate()) ?></p>
            </div>
            <div class="col-span-2">
                <p class="text-xs text-gray-500 mb-1">Articles commandés</p>
                <p class="font-medium text-gray-900"><?= e($produitsRecap) ?></p>
            </div>
            <div class="col-span-2">
                <p class="text-xs text-gray-500 mb-1">Statut actuel</p>
                <span class="inline-flex <?= $badgeClasses($c->statut) ?> px-2.5 py-1 rounded-full text-xs font-medium">
                    <?= e($c->libelleStatut()) ?>
                </span>
            </div>
        </div>

        <form method="post" action="<?= $lienBase ?>/<?= $c->id ?>/statut" class="mt-6 flex items-center justify-end gap-3">
            <input type="hidden" name="_token" value="<?= csrf() ?>">
            <input type="hidden" name="statut" value="<?= e((string) $c->statutSuivant()) ?>">
            <button type="button" onclick="closeModal('statut-modal-<?= $c->id ?>')"
                    class="px-4 py-2 rounded-lg text-sm font-medium text-gray-600 border border-gray-200 hover:bg-gray-50 transition">
                Annuler
            </button>
            <button type="submit"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-white bg-brand-orange hover:opacity-90 transition">
                <i class="fa-solid fa-check text-xs"></i> Marquer comme <?= e((string) $c->libelleStatutSuivant()) ?>
            </button>
        </form>
    </div>
</div>
<?php endif; ?>

<?php if ($c->peutAnnuler()): ?><!-- ============================== MODAL ANNULATION ============================== -->
<div id="annuler-modal-<?= $c->id ?>" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/50" onclick="closeModal('annuler-modal-<?= $c->id ?>')"></div>
    <div class="relative bg-white rounded-2xl shadow-xl max-w-md w-full p-6">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-red-50 flex items-center justify-center text-red-500 text-xl shrink-0">
                <i class="fa-solid fa-ban"></i>
            </div>
            <div>
                <h3 class="font-bold text-gray-900">Annuler la commande ?</h3>
                <p class="text-sm text-gray-500 mt-1">
                    La commande n°<?= $c->id ?> de « <?= e($c->clientNomComplet()) ?> »
                    sera annulée et ne pourra plus être traitée.
                </p>
            </div>
        </div>
        <div class="mt-6 flex items-center justify-end gap-3">
            <button type="button" onclick="closeModal('annuler-modal-<?= $c->id ?>')"
                    class="px-4 py-2 rounded-lg text-sm font-medium text-gray-600 border border-gray-200 hover:bg-gray-50 transition">
                Fermer
            </button>
            <form method="post" action="<?= $lienBase ?>/<?= $c->id ?>/annuler">
                <input type="hidden" name="_token" value="<?= csrf() ?>">
                <button type="submit"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-white bg-red-500 hover:bg-red-600 transition">
                    <i class="fa-solid fa-ban text-xs"></i> Confirmer l'annulation
                </button>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>