<?php

declare(strict_types=1);

/**
 * Reçu d'une commande, affiché dans l'espace admin/gérant.
 *
 * @var \App\Models\Commande $commande
 * @var \App\Models\LigneCommande[] $lignes
 * @var array<string, mixed>|null $paiement
 * @var string $baseRoute (/admin ou /gerant)
 */

$lienBase = $base . $baseRoute . '/commandes';
$urlPdf = $lienBase . '/' . $commande->id . '/recu/pdf';
?>

<div class="mb-6 flex items-center justify-between">
    <div>
        <h2 class="text-2xl font-bold text-gray-900">Reçu n°<?= $commande->id ?></h2>
        <p class="text-sm text-gray-500 mt-1">Consultez et téléchargez le reçu de la commande n°<?= $commande->id ?></p>
    </div>
    <a href="<?= e($lienBase . '/' . $commande->id) ?>"
       class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-gray-600 border border-gray-200 hover:bg-gray-50 transition">
        <i class="fa-solid fa-arrow-left text-xs"></i> Retour à la commande
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 max-w-2xl">
    <div class="flex items-start justify-between border-b border-gray-100 pb-6">
        <div>
            <div class="flex items-center gap-2">
                <span class="w-8 h-8 rounded-full bg-brand-orange flex items-center justify-center text-white font-serif font-bold text-sm">S</span>
                <span class="font-serif font-bold text-2xl">SAVEUR 221</span>
            </div>
            <p class="text-sm text-gray-500 mt-1">Restaurant traditionnel sénégalais</p>
        </div>
        <div class="text-right">
            <h1 class="font-bold text-lg">Reçu</h1>
            <p class="text-sm text-gray-500">N° <?= $commande->id ?></p>
            <p class="text-sm text-gray-500"><?= e($commande->dateFormatee()) ?></p>
        </div>
    </div>

    <div class="py-6 grid grid-cols-1 sm:grid-cols-2 gap-6 text-sm">
        <div>
            <p class="text-xs uppercase tracking-wide text-gray-500 mb-1">Client</p>
            <p class="font-medium"><?= e($commande->clientNomComplet()) ?></p>
            <?php if ($commande->clientEmail): ?><p class="text-gray-500"><?= e($commande->clientEmail) ?></p><?php endif; ?>
            <?php if ($commande->clientTelephone): ?><p class="text-gray-500"><?= e($commande->clientTelephone) ?></p><?php endif; ?>
        </div>
        <div class="text-right">
            <p class="text-xs uppercase tracking-wide text-gray-500 mb-1">Statut</p>
            <p class="font-medium"><?= e($commande->libelleStatut()) ?></p>
            <p class="text-xs uppercase tracking-wide text-gray-500 mt-3 mb-1">Paiement</p>
            <p class="font-medium"><?= $paiement !== null ? ('Payé avec ' . e((string) $paiement['mode_paiement'])) : 'Non payé' ?></p>
        </div>
    </div>

    <table class="w-full text-sm border-t border-gray-100">
        <thead>
            <tr class="text-left text-xs uppercase tracking-wide text-gray-500">
                <th class="py-3 font-medium">Produit</th>
                <th class="py-3 font-medium text-center">Qté</th>
                <th class="py-3 font-medium text-right">Prix unitaire</th>
                <th class="py-3 font-medium text-right">Sous-total</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            <?php foreach ($lignes as $ligne): ?>
                <tr>
                    <td class="py-3 font-medium text-gray-900"><?= e($ligne->produitLibelle) ?></td>
                    <td class="py-3 text-center text-gray-700"><?= $ligne->quantite ?></td>
                    <td class="py-3 text-right text-gray-700"><?= e($ligne->prixFormate()) ?></td>
                    <td class="py-3 text-right font-medium text-gray-900"><?= e($ligne->sousTotalFormate()) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="mt-5 flex items-center justify-between border-t border-gray-100 pt-4">
        <span class="text-gray-500 text-sm">Montant total</span>
        <span class="text-xl font-bold text-brand-orange"><?= e($commande->montantFormate()) ?></span>
    </div>

    <p class="mt-8 text-center text-sm text-gray-500">Merci de votre confiance et bon appétit !</p>
</div>

<div class="mt-6 flex items-center gap-3">
    <a href="<?= e($urlPdf) ?>"
       class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg text-sm font-medium text-white bg-brand-orange hover:opacity-90 transition">
        <i class="fa-solid fa-file-pdf"></i> Télécharger le reçu (PDF)
    </a>
    <button onclick="window.print()"
            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg text-sm font-medium text-gray-600 border border-gray-200 hover:bg-gray-50 transition">
        <i class="fa-solid fa-print"></i> Imprimer
    </button>
</div>