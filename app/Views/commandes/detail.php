<?php

declare(strict_types=1);

/**
 * @var \App\Models\Commande $commande
 * @var \App\Models\LigneCommande[] $lignes
 * @var array<string, mixed>|null $paiement
 * @var string $baseRoute (/admin ou /gerant)
 */

use App\Models\Commande;

$lienBase = $base . $baseRoute . '/commandes';
$produitsRecap = implode(', ', array_map(
    static fn (\App\Models\LigneCommande $l): string => $l->produitLibelle,
    $lignes
));

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

<div class="mb-6 flex items-center justify-between">
    <div>
        <h2 class="text-2xl font-bold text-gray-900">Commande n°<?= $commande->id ?></h2>
        <p class="text-sm text-gray-500 mt-1"><?= e($commande->dateFormatee()) ?></p>
    </div>
    <a href="<?= e($lienBase) ?>"
       class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-gray-600 border border-gray-200 hover:bg-gray-50 transition">
        <i class="fa-solid fa-arrow-left text-xs"></i> Retour aux commandes
    </a>
</div>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

    <!-- Carte gauche : commande + articles -->
    <div class="xl:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between">
            <h3 class="font-bold text-gray-900">Commande</h3>
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium <?= $badgeClasses($commande->statut) ?> whitespace-nowrap">
                <?= e($commande->libelleStatut()) ?>
            </span>
        </div>
        <p class="text-sm text-gray-500 mt-1">Passée le <?= e($commande->dateFormatee()) ?></p>

        <h4 class="mt-6 font-semibold text-gray-900">Articles commandés</h4>
        <div class="mt-3 overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-left text-xs uppercase tracking-wide text-gray-500">
                        <th class="px-4 py-3 font-medium">Produit</th>
                        <th class="px-4 py-3 font-medium text-center">Qté</th>
                        <th class="px-4 py-3 font-medium text-right">Prix unitaire</th>
                        <th class="px-4 py-3 font-medium text-right">Sous-total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php foreach ($lignes as $ligne): ?>
                        <tr>
                            <td class="px-4 py-3 font-medium text-gray-900"><?= e($ligne->produitLibelle) ?></td>
                            <td class="px-4 py-3 text-center text-gray-700"><?= $ligne->quantite ?></td>
                            <td class="px-4 py-3 text-right text-gray-700"><?= e($ligne->prixFormate()) ?></td>
                            <td class="px-4 py-3 text-right font-medium text-gray-900"><?= e($ligne->sousTotalFormate()) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr class="border-t border-gray-200 bg-gray-50/50">
                        <td class="px-4 py-3 font-semibold text-gray-900" colspan="3">Total</td>
                        <td class="px-4 py-3 text-right font-bold text-brand-orange"><?= e($commande->montantFormate()) ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Carte droite : client + paiement & statut -->
    <div class="space-y-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-bold text-gray-900">Informations Client</h3>
            <div class="mt-4 space-y-3 text-sm">
                <div class="flex items-start gap-3">
                    <i class="fa-solid fa-user w-4 text-gray-400 mt-0.5"></i>
                    <div>
                        <p class="text-xs text-gray-500">Nom complet</p>
                        <p class="font-medium text-gray-900"><?= e($commande->clientNomComplet()) ?></p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <i class="fa-solid fa-envelope w-4 text-gray-400 mt-0.5"></i>
                    <div>
                        <p class="text-xs text-gray-500">Email</p>
                        <p class="font-medium text-gray-900"><?= e((string) $commande->clientEmail) ?></p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <i class="fa-solid fa-phone w-4 text-gray-400 mt-0.5"></i>
                    <div>
                        <p class="text-xs text-gray-500">Téléphone</p>
                        <p class="font-medium text-gray-900"><?= e((string) ($commande->clientTelephone ?? '—')) ?></p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <i class="fa-solid fa-location-dot w-4 text-gray-400 mt-0.5"></i>
                    <div>
                        <p class="text-xs text-gray-500">Adresse</p>
                        <p class="font-medium text-gray-900"><?= e((string) ($commande->clientAdresse ?? '—')) ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-5">
            <h3 class="font-bold text-gray-900">Paiement & Statut</h3>

            <div>
                <p class="text-xs uppercase tracking-wide text-gray-500 mb-2">Statut du paiement</p>
                <?php if ($paiement !== null): ?>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                        <i class="fa-solid fa-circle-check text-[10px]"></i> Payé avec <?= e($paiement['mode_paiement'] ?? '') ?>
                    </span>
                    <?php if (isset($paiement['montant'])): ?>
                        <p class="mt-2 text-sm text-gray-600">
                            <?= number_format((float) $paiement['montant'], 0, ',', ' ') ?> FCFA
                            <?php if (!empty($paiement['date_paiement'])): ?>
                                le <?= e((new \DateTimeImmutable($paiement['date_paiement']))->format('d/m/Y H:i')) ?>
                            <?php endif; ?>
                        </p>
                    <?php endif; ?>
                <?php else: ?>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-500">
                        <i class="fa-solid fa-hourglass-half text-[10px]"></i> Non payé
                    </span>
                <?php endif; ?>
            </div>

            <div class="border-t border-gray-100 pt-5">
                <p class="text-xs uppercase tracking-wide text-gray-500 mb-3">Modifier le statut de la commande</p>
                <?php if ($commande->peutChangerStatut()): ?>
                    <button type="button" onclick="openModal('statut-modal-<?= $commande->id ?>')"
                            class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 rounded-lg text-sm font-medium text-white bg-brand-orange hover:opacity-90 transition">
                        <i class="fa-solid fa-arrow-right-arrow-left text-xs"></i>
                        Marquer comme <?= e((string) $commande->libelleStatutSuivant()) ?>
                    </button>
                <?php else: ?>
                    <p class="text-sm text-gray-500">Aucune modification possible pour ce statut.</p>
                <?php endif; ?>

                <?php if ($commande->peutAnnuler()): ?>
                    <button type="button" onclick="openModal('annuler-modal-<?= $commande->id ?>')"
                            class="mt-3 w-full inline-flex items-center justify-center gap-2 px-4 py-3 rounded-lg text-sm font-medium text-red-600 border border-red-200 bg-red-50 hover:bg-red-100 transition">
                        <i class="fa-solid fa-ban text-xs"></i> Annuler la commande
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- ============================== MODALES ============================== -->
<?php $c = $commande; ?>
<?php include __DIR__ . '/partials/modals.php'; ?>