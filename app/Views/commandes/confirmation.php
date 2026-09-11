<div class="max-w-2xl mx-auto text-center">
    <div class="w-20 h-20 rounded-full bg-green-100 flex items-center justify-center mx-auto mb-6">
        <i class="fa-solid fa-check text-green-600 text-3xl"></i>
    </div>
    <h1 class="font-serif text-3xl font-bold mb-2">Commande enregistrée !</h1>
    <p class="text-brand-brown/60 mb-8">Merci pour votre confiance. Votre commande a bien été prise en compte.</p>

    <div class="bg-white rounded-2xl shadow-sm border border-brand-brown/10 p-8 text-left">
        <div class="text-center mb-6">
            <p class="text-sm text-brand-brown/50">Commande</p>
            <p class="font-serif text-3xl font-bold text-brand-orange">n°<?= (int) $commande->id ?></p>
            <p class="mt-2 text-sm text-brand-brown/50"><?= e($commande->dateFormatee()) ?></p>
        </div>

        <dl class="text-sm space-y-3 border-t border-brand-brown/10 pt-6">
            <div class="flex justify-between">
                <dt class="text-brand-brown/60">Articles</dt>
                <dd class="font-medium"><?= (int) ($commande->quantiteTotale ?? 0) ?></dd>
            </div>
            <div class="flex justify-between">
                <dt class="text-brand-brown/60">Statut</dt>
                <dd><span class="inline-block bg-amber-100 text-amber-700 px-2.5 py-0.5 rounded-full text-xs font-medium"><?= e($commande->libelleStatut()) ?></span></dd>
            </div>
            <div class="flex justify-between">
                <dt class="text-brand-brown/60">Mode de paiement</dt>
                <dd class="font-medium"><?= $paiement !== null ? e((string) $paiement['mode_paiement']) : 'À la remise' ?></dd>
            </div>
            <div class="flex justify-between">
                <dt class="text-brand-brown/60">Statut du paiement</dt>
                <dd class="font-medium <?= $paiement !== null ? 'text-green-600' : 'text-brand-brown/60' ?>">
                    <?= $paiement !== null ? 'Payé' : 'À régler à la remise' ?>
                </dd>
            </div>
            <div class="flex justify-between items-center border-t border-brand-brown/10 pt-3">
                <dt class="text-brand-brown/60">Total</dt>
                <dd class="text-xl font-bold text-brand-orange"><?= $commande->montantFormate() ?></dd>
            </div>
        </dl>
    </div>

    <div class="flex flex-col sm:flex-row gap-3 justify-center mt-8">
        <a href="<?= $base ?>/client/commandes"
           class="bg-brand-orange text-white px-6 py-3 rounded-full font-medium hover:opacity-90 transition">
            Mes commandes
        </a>
        <a href="<?= $base ?>/menu"
           class="border border-brand-brown/20 text-brand-brown px-6 py-3 rounded-full font-medium hover:border-brand-orange hover:text-brand-orange transition">
            Continuer mes achats
        </a>
    </div>
</div>