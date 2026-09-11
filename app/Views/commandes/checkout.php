<?php $client = client(); ?>

<div class="mb-8">
    <h1 class="font-serif text-3xl font-bold mb-2">Finaliser la commande</h1>
    <span class="block w-16 h-1 bg-brand-orange"></span>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <div class="lg:col-span-2 space-y-4">
        <?php foreach ($lignes as $ligne): ?>
            <?php $produit = $ligne['produit']; ?>
            <div class="bg-white rounded-2xl shadow-sm border border-brand-brown/10 p-4 flex items-center gap-4">
                <?php if ($produit->image): ?>
                    <img src="<?= e($produit->image) ?>" alt="<?= e($produit->libelle) ?>" class="w-16 h-16 rounded-xl object-cover shrink-0">
                <?php else: ?>
                    <div class="w-16 h-16 rounded-xl bg-[#EBD9C9] flex items-center justify-center text-center shrink-0">
                        <span class="text-brand-brown/50 text-[10px] font-medium px-1"><?= e($produit->libelle) ?></span>
                    </div>
                <?php endif; ?>

                <div class="flex-1 min-w-0">
                    <a href="<?= $base ?>/produits/<?= (int) $produit->id ?>">
                        <h3 class="font-serif font-bold hover:text-brand-orange truncate"><?= e($produit->libelle) ?></h3>
                    </a>
                    <p class="text-sm text-brand-brown/50"><?= $produit->prixFormate() ?></p>
                </div>

                <div class="text-right shrink-0">
                    <p class="text-sm text-brand-brown/60">x <?= (int) $ligne['quantite'] ?></p>
                    <p class="font-semibold text-brand-orange"><?= number_format((float) $ligne['sousTotal'], 0, ',', ' ') ?> FCFA</p>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-brand-brown/10 p-6 h-fit">
        <h2 class="font-serif text-xl font-bold mb-4">Coordonnées</h2>
        <div class="text-sm text-brand-brown/70 mb-6 p-4 rounded-xl bg-brand-cream border border-brand-brown/10 space-y-1">
            <p class="font-semibold text-brand-brown"><?= e(nomComplet()) ?></p>
            <?php if (!empty($client['telephone'])): ?>
                <p><i class="fa-solid fa-phone text-xs mr-1"></i><?= e((string) $client['telephone']) ?></p>
            <?php endif; ?>
            <?php if (!empty($client['adresse'])): ?>
                <p><i class="fa-solid fa-location-dot text-xs mr-1"></i><?= e((string) $client['adresse']) ?></p>
            <?php endif; ?>
        </div>

        <h2 class="font-serif text-xl font-bold mb-3">Mode de paiement</h2>
        <form method="post" action="<?= $base ?>/commande">
            <input type="hidden" name="_token" value="<?= csrf() ?>">

            <div class="space-y-2 mb-6">
                <?php foreach ($modes as $mode): ?>
                    <?php $immediat = in_array($mode, $modesImmediats, true); ?>
                    <label class="flex items-center justify-between gap-3 border border-brand-brown/10 rounded-xl px-4 py-3 cursor-pointer hover:border-brand-orange/50 transition">
                        <span class="flex items-center gap-2 text-sm font-medium">
                            <input type="radio" name="mode_paiement" value="<?= e($mode) ?>"
                                   <?= $mode === 'Espèces' ? 'checked' : '' ?> class="accent-brand-orange">
                            <?= e($mode) ?>
                        </span>
                        <span class="text-[11px] <?= $immediat ? 'text-green-600 font-medium' : 'text-brand-brown/50' ?>">
                            <?= $immediat ? 'Paiement immédiat' : 'Paiement à la remise' ?>
                        </span>
                    </label>
                <?php endforeach; ?>
            </div>

            <dl class="space-y-3 text-sm mb-6">
                <div class="flex justify-between">
                    <dt class="text-brand-brown/60">Articles</dt>
                    <dd class="font-medium"><?= (int) $totalArticles ?></dd>
                </div>
                <div class="flex justify-between items-center border-t border-brand-brown/10 pt-3">
                    <dt class="text-brand-brown/60">Total à payer</dt>
                    <dd class="text-xl font-bold text-brand-orange"><?= number_format((float) $total, 0, ',', ' ') ?> FCFA</dd>
                </div>
            </dl>

            <button type="submit"
                    class="w-full bg-brand-orange text-white py-3 rounded-full font-medium hover:opacity-90 transition">
                Confirmer ma commande
            </button>
            <a href="<?= $base ?>/panier" class="block text-center mt-3 text-sm text-brand-brown/50 hover:text-brand-orange">
                Retour au panier
            </a>
        </form>
    </div>
</div>