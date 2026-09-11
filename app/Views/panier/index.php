<main class="max-w-7xl mx-auto px-6 py-10 min-h-[60vh]">
    <div class="mb-8">
        <h1 class="font-serif text-3xl font-bold mb-2">Votre panier</h1>
        <span class="block w-16 h-1 bg-brand-orange"></span>
    </div>

    <?php if (empty($lignes)): ?>
        <div class="text-center py-20">
            <i class="fa-solid fa-basket-shopping text-5xl text-brand-brown/20 mb-5"></i>
            <h2 class="font-serif text-2xl font-bold mb-2">Votre panier est vide</h2>
            <p class="text-brand-brown/60 mb-8">Parcourez le menu et ajoutez vos plats préférés.</p>
            <a href="<?= $base ?>/menu"
               class="inline-block bg-brand-orange text-white px-6 py-3 rounded-full font-medium hover:opacity-90 transition">
                Découvrir le menu
            </a>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Liste des articles -->
            <div class="lg:col-span-2 space-y-4">
                <?php foreach ($lignes as $ligne): ?>
                    <?php $produit = $ligne['produit']; ?>
                    <div class="bg-white rounded-2xl shadow-sm border border-brand-brown/10 p-4 flex items-center gap-4">
                        <a href="<?= $base ?>/produits/<?= (int) $produit->id ?>" class="shrink-0">
                            <?php if ($produit->image): ?>
                                <img src="<?= e($produit->image) ?>" alt="<?= e($produit->libelle) ?>" class="w-20 h-20 rounded-xl object-cover">
                            <?php else: ?>
                                <div class="w-20 h-20 rounded-xl bg-[#EBD9C9] flex items-center justify-center text-center">
                                    <span class="text-brand-brown/50 text-[10px] font-medium px-2"><?= e($produit->libelle) ?></span>
                                </div>
                            <?php endif; ?>
                        </a>

                        <div class="flex-1 min-w-0">
                            <a href="<?= $base ?>/produits/<?= (int) $produit->id ?>">
                                <h3 class="font-serif font-bold hover:text-brand-orange truncate"><?= e($produit->libelle) ?></h3>
                            </a>
                            <p class="text-sm text-brand-brown/50"><?= $produit->prixFormate() ?></p>
                            <?php if (!$produit->estDisponible()): ?>
                                <span class="inline-block mt-1 text-xs bg-red-100 text-red-600 px-2 py-0.5 rounded-full">Indisponible</span>
                            <?php endif; ?>
                        </div>

                        <div class="flex items-center gap-3 shrink-0">
                            <?php if ($produit->estDisponible()): ?>
                                <div class="flex items-center border border-brand-brown/20 rounded-full overflow-hidden">
                                    <form method="post" action="<?= $base ?>/panier/<?= (int) $produit->id ?>/modifier">
                                        <input type="hidden" name="_token" value="<?= csrf() ?>">
                                        <input type="hidden" name="quantite" value="<?= max(1, $ligne['quantite'] - 1) ?>">
                                        <button type="submit" <?= $ligne['quantite'] <= 1 ? 'disabled' : '' ?>
                                                class="w-9 h-9 flex items-center justify-center hover:bg-brand-cream text-lg <?= $ligne['quantite'] <= 1 ? 'opacity-30 cursor-not-allowed' : '' ?>">−</button>
                                    </form>
                                    <span class="w-10 text-center font-semibold text-sm"><?= (int) $ligne['quantite'] ?></span>
                                    <form method="post" action="<?= $base ?>/panier/<?= (int) $produit->id ?>/modifier">
                                        <input type="hidden" name="_token" value="<?= csrf() ?>">
                                        <input type="hidden" name="quantite" value="<?= min(99, $ligne['quantite'] + 1) ?>">
                                        <button type="submit" class="w-9 h-9 flex items-center justify-center hover:bg-brand-cream text-lg">+</button>
                                    </form>
                                </div>
                            <?php else: ?>
                                <span class="text-sm font-semibold text-brand-brown/50"><?= (int) $ligne['quantite'] ?></span>
                            <?php endif; ?>
                        </div>

                        <div class="text-right shrink-0 w-28">
                            <p class="font-semibold text-brand-orange"><?= number_format((float) $ligne['sousTotal'], 0, ',', ' ') ?> FCFA</p>
                            <form method="post" action="<?= $base ?>/panier/<?= (int) $produit->id ?>/retirer" class="mt-1">
                                <input type="hidden" name="_token" value="<?= csrf() ?>">
                                <button type="submit" class="text-brand-brown/40 hover:text-red-500 text-xs" title="Retirer du panier">
                                    <i class="fa-solid fa-trash-can"></i> Retirer
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Récapitulatif -->
            <div class="bg-white rounded-2xl shadow-sm border border-brand-brown/10 p-6 h-fit">
                <h2 class="font-serif text-xl font-bold mb-4">Récapitulatif</h2>
                <dl class="space-y-3 text-sm mb-6">
                    <div class="flex justify-between">
                        <dt class="text-brand-brown/60">Articles</dt>
                        <dd class="font-medium"><?= (int) $totalArticles ?></dd>
                    </div>
                    <div class="flex justify-between items-center border-t border-brand-brown/10 pt-3">
                        <dt class="text-brand-brown/60">Total</dt>
                        <dd class="text-xl font-bold text-brand-orange"><?= number_format((float) $total, 0, ',', ' ') ?> FCFA</dd>
                    </div>
                </dl>

                <a href="<?= $base ?>/menu"
                   class="flex items-center justify-center bg-brand-orange text-white py-3 rounded-full font-medium hover:opacity-90 transition mb-3">
                    Continuer mes achats
                </a>
                <form method="post" action="<?= $base ?>/panier/vider"
                      onsubmit="return confirm('Vider le panier ?');">
                    <input type="hidden" name="_token" value="<?= csrf() ?>">
                    <button type="submit"
                            class="w-full border border-red-200 text-red-500 py-2.5 rounded-full text-sm hover:bg-red-50 transition">
                        Vider le panier
                    </button>
                </form>
            </div>
        </div>
    <?php endif; ?>
</main>