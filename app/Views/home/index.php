<!-- Hero -->
<section class="bg-brand-cream">
    <div class="max-w-7xl mx-auto px-6 py-16 grid grid-cols-1 md:grid-cols-2 gap-10 items-center">
        <div>
            <span class="inline-block bg-white text-xs text-brand-brown/70 font-medium px-3 py-1 rounded-full mb-4">
                Authentique Gastronomie Sénégalaise
            </span>
            <h1 class="font-serif text-4xl md:text-5xl font-bold leading-tight mb-4">
                La saveur du Sénégal, directement dans votre assiette.
            </h1>
            <p class="text-brand-brown/70 mb-6 leading-relaxed">
                Découvrez nos plats préparés avec passion et commandez facilement en ligne.
                Des recettes traditionnelles cuisinées avec amour.
            </p>
            <div class="flex flex-wrap gap-3 mb-6">
                <a href="<?= $base ?>/menu" class="bg-brand-orange text-white px-6 py-3 rounded-full font-medium hover:opacity-90">
                    Commander maintenant
                </a>
                <a href="<?= $base ?>/menu" class="border border-brand-brown/20 px-6 py-3 rounded-full font-medium hover:border-brand-orange">
                    Découvrir le menu
                </a>
            </div>
            <div class="flex flex-wrap gap-3 text-sm text-brand-brown/70">
                <span class="inline-flex items-center gap-1 bg-white px-3 py-1.5 rounded-full">🥘 Plats frais</span>
                <span class="inline-flex items-center gap-1 bg-white px-3 py-1.5 rounded-full">⏱ Commande rapide</span>
                <span class="inline-flex items-center gap-1 bg-white px-3 py-1.5 rounded-full">✓ Service de qualité</span>
            </div>
        </div>

        <img src="<?= $base ?>/assets/images/hero.jpg" alt="Plat sénégalais"
             class="w-full h-96 rounded-2xl object-cover">
    </div>
</section>

<!-- Categories : fond blanc pour se distinguer du hero (creme) juste au-dessus -->
<section class="bg-white py-16">
    <div class="max-w-7xl mx-auto px-6">
        <div class="text-center mb-10">
            <h2 class="font-serif text-3xl font-bold mb-2">Découvrez notre menu</h2>
            <span class="inline-block w-16 h-1 bg-brand-orange"></span>
        </div>

        <?php if (empty($categories)): ?>
            <p class="text-center text-brand-brown/60">Aucune catégorie pour le moment.</p>
        <?php else: ?>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                <?php foreach ($categories as $cat): ?>
                    <a href="<?= $base ?>/menu?categorie=<?= (int) $cat->id ?>"
                       class="bg-brand-cream rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition block">
                        <div class="w-full h-32 bg-[#EBD9C9] flex items-center justify-center">
                            <span class="text-brand-brown/50 text-sm font-medium"><?= e($cat->nom) ?></span>
                        </div>
                        <div class="p-4">
                            <h3 class="font-serif font-bold mb-1"><?= e($cat->nom) ?></h3>
                            <p class="text-xs text-brand-brown/60"><?= e($cat->description ?? '') ?></p>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Plats populaires : fond creme pour alterner avec la section blanche au-dessus -->
<section class="bg-brand-cream py-16">
    <div class="max-w-7xl mx-auto px-6">
        <div class="text-center mb-10">
            <h2 class="font-serif text-3xl font-bold mb-2">Nos plats populaires</h2>
            <span class="inline-block w-16 h-1 bg-brand-orange"></span>
        </div>

        <?php if (empty($populaires)): ?>
            <p class="text-center text-brand-brown/60">Aucun plat disponible pour le moment.</p>
        <?php else: ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <?php foreach ($populaires as $p): ?>
                    <div class="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition">
                        <a href="<?= $base ?>/produits/<?= (int) $p->id ?>">
                            <?php if ($p->image): ?>
                                <img src="<?= e($p->image) ?>" alt="<?= e($p->libelle) ?>" class="w-full h-36 object-cover">
                            <?php else: ?>
                                <div class="w-full h-36 bg-[#EBD9C9] flex items-center justify-center">
                                    <span class="text-brand-brown/50 text-sm font-medium px-4 text-center"><?= e($p->libelle) ?></span>
                                </div>
                            <?php endif; ?>
                        </a>
                        <div class="p-4">
                            <a href="<?= $base ?>/produits/<?= (int) $p->id ?>">
                                <h3 class="font-serif font-bold hover:text-brand-orange"><?= e($p->libelle) ?></h3>
                            </a>
                            <div class="flex items-center justify-between mt-2">
                                <span class="font-semibold text-brand-orange"><?= $p->prixFormate() ?></span>
                                <form method="post" action="<?= $base ?>/panier/ajouter">
                                    <input type="hidden" name="_token" value="<?= csrf() ?>">
                                    <input type="hidden" name="produit_id" value="<?= (int) $p->id ?>">
                                    <input type="hidden" name="quantite" value="1">
                                    <button type="submit" class="bg-brand-orange text-white text-xs px-3 py-1.5 rounded-full hover:opacity-90">
                                        + Ajouter
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Pourquoi nous choisir : fond blanc, alterne avec la section creme au-dessus -->
<section class="bg-white py-16">
    <div class="max-w-7xl mx-auto px-6">
        <div class="text-center mb-10">
            <h2 class="font-serif text-3xl font-bold mb-2">Pourquoi choisir Saveur 221 ?</h2>
            <span class="inline-block w-16 h-1 bg-brand-orange"></span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php
            $atouts = [
                ['🌿', 'Produits de qualité', 'Des ingrédients frais et sélectionnés avec soin pour des recettes authentiques.'],
                ['📱', 'Commande simple', 'Parcourez notre menu et commandez en quelques clics, sans complication.'],
                ['⏱', 'Préparation rapide', 'Nos chefs préparent votre commande avec efficacité pour un service rapide.'],
                ['🤝', 'Service fiable', 'Une équipe attentive du choix du plat jusqu\'à la remise de votre commande.'],
            ];
            ?>
            <?php foreach ($atouts as [$emoji, $titre, $texte]): ?>
                <div class="bg-brand-cream rounded-2xl p-6">
                    <span class="w-10 h-10 flex items-center justify-center rounded-full bg-brand-orange/10 text-lg mb-3"><?= $emoji ?></span>
                    <h3 class="font-serif font-bold mb-2"><?= $titre ?></h3>
                    <p class="text-sm text-brand-brown/60"><?= $texte ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Comment ca marche : fond creme, alterne avec le blanc au-dessus -->
<section class="bg-brand-cream py-16">
    <div class="max-w-7xl mx-auto px-6">
        <div class="text-center mb-10">
            <h2 class="font-serif text-3xl font-bold mb-2">Comment ça marche ?</h2>
            <span class="inline-block w-16 h-1 bg-brand-orange"></span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-10 text-center">
            <?php
            $etapes = [
                ['1', 'Choisissez votre plat', 'Parcourez notre menu et sélectionnez vos plats préférés.'],
                ['2', 'Passez votre commande', 'Validez votre panier, réglez en ligne et suivez votre commande.'],
                ['3', 'Recevez votre repas', 'Récupérez votre commande fraîchement préparée.'],
            ];
            ?>
            <?php foreach ($etapes as [$numero, $titre, $texte]): ?>
                <div>
                    <span class="w-10 h-10 mx-auto rounded-full bg-brand-orange text-white font-bold flex items-center justify-center mb-4">
                        <?= $numero ?>
                    </span>
                    <h3 class="font-serif font-bold mb-2"><?= $titre ?></h3>
                    <p class="text-sm text-brand-brown/60"><?= $texte ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- CTA finale : collee au footer (pas de marge en bas) -->
<section class="relative bg-brand-brown text-white">
    <img src="<?= $base ?>/assets/images/cta-bg.jpg" alt=""
         class="absolute inset-0 w-full h-full object-cover opacity-20">
    <div class="relative max-w-7xl mx-auto px-6 py-16 text-center">
        <h2 class="font-serif text-3xl font-bold mb-2">Une envie de bon plat ?</h2>
        <span class="inline-block w-16 h-1 bg-brand-orange mb-4"></span>
        <p class="text-brand-cream/70 mb-6">Commandez dès maintenant et savourez la cuisine sénégalaise en famille ou entre amis.</p>
        <a href="<?= $base ?>/menu" class="inline-block bg-brand-orange text-white px-8 py-3 rounded-full font-medium hover:opacity-90">
            Commander maintenant
        </a>
    </div>
</section>
