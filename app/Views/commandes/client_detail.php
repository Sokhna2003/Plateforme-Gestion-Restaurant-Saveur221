<?php
$statutBadge = static function (string $statut): string {
    return match ($statut) {
        'EN_ATTENTE' => 'bg-amber-100 text-amber-700',
        'EN_PREPARATION' => 'bg-brand-orange/10 text-brand-orange',
        'PRETE' => 'bg-blue-100 text-blue-700',
        'RETIREE' => 'bg-green-100 text-green-700',
        'ANNULEE' => 'bg-red-100 text-red-600',
        default => 'bg-gray-100 text-gray-500',
    };
};
?>

<div class="mb-8">
    <div class="flex items-center justify-between">
        <h1 class="font-serif text-3xl font-bold mb-2">Commande n°<?= (int) $commande->id ?></h1>
        <span class="inline-block px-3 py-1 rounded-full text-xs font-medium <?= $statutBadge($commande->statut) ?>">
            <?= e($commande->libelleStatut()) ?>
        </span>
    </div>
    <p class="text-brand-brown/60 mb-2">Passée le <?= e($commande->dateFormatee()) ?></p>
    <span class="block w-16 h-1 bg-brand-orange"></span>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <div class="bg-white rounded-2xl shadow-sm border border-brand-brown/10 p-6 lg:col-span-2">
        <h2 class="font-serif text-xl font-bold mb-4">Articles</h2>
        <div class="divide-y divide-brand-brown/10">
            <?php foreach ($lignes as $ligne): ?>
                <div class="flex items-center justify-between py-3">
                    <div class="min-w-0">
                        <p class="font-medium truncate"><?= e($ligne->produitLibelle) ?></p>
                        <p class="text-sm text-brand-brown/50"><?= (int) $ligne->quantite ?> × <?= $ligne->prixFormate() ?></p>
                    </div>
                    <p class="font-semibold text-brand-orange shrink-0"><?= $ligne->sousTotalFormate() ?></p>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="flex justify-between items-center border-t border-brand-brown/10 pt-4 mt-2">
            <span class="font-semibold">Total</span>
            <span class="text-xl font-bold text-brand-orange"><?= $commande->montantFormate() ?></span>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-brand-brown/10 p-6 h-fit">
        <h2 class="font-serif text-xl font-bold mb-4">Paiement</h2>
        <dl class="text-sm space-y-3">
            <div class="flex justify-between">
                <dt class="text-brand-brown/60">Mode</dt>
                <dd class="font-medium"><?= $paiement !== null ? e((string) $paiement['mode_paiement']) : 'À la remise' ?></dd>
            </div>
            <div class="flex justify-between items-center border-t border-brand-brown/10 pt-3">
                <dt class="text-brand-brown/60">Montant réglé</dt>
                <dd class="font-bold <?= $paiement !== null ? 'text-green-600' : 'text-brand-brown/60' ?>">
                    <?= $paiement !== null ? number_format((float) $paiement['montant'], 0, ',', ' ') . ' FCFA' : '0 FCFA' ?>
                </dd>
            </div>
        </dl>
        <p class="mt-4 text-xs text-brand-brown/50">
            <?= $paiement !== null
                ? 'Cette commande a été réglée en ligne. Merci !'
                : 'Le paiement sera encaissé lors du retrait de votre commande.' ?>
        </p>
    </div>
</div>

<div class="mt-8">
    <a href="<?= $base ?>/client/commandes"
       class="inline-block border border-brand-brown/20 text-brand-brown px-6 py-3 rounded-full font-medium hover:border-brand-orange hover:text-brand-orange transition">
        ← Retour à mes commandes
    </a>
</div>