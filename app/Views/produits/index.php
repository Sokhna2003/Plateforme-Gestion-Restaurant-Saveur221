<main class="max-w-7xl mx-auto px-6 py-10">
    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4 mb-2">
        <div>
            <h1 class="font-serif text-3xl font-bold mb-2">Découvrez nos plats</h1>
            <span class="block w-16 h-1 bg-brand-orange"></span>
        </div>

        <form method="get" action="<?= $base ?>/menu" class="flex gap-2">
            <?php if ($categorieId): ?>
                <input type="hidden" name="categorie" value="<?= (int) $categorieId ?>">
            <?php endif; ?>
            <input
                type="text"
                name="q"
                value="<?= e($motCle ?? '') ?>"
                placeholder="Rechercher un plat..."
                class="border border-brand-brown/20 rounded-full px-4 py-2 text-sm w-56 focus:outline-none focus:border-brand-orange"
            >
            <button type="submit" class="bg-brand-orange text-white text-sm px-5 py-2 rounded-full hover:opacity-90">
                Rechercher
            </button>
        </form>
    </div>

    <div class="flex flex-wrap gap-2 mb-8 mt-6">
        <a href="<?= $base ?>/menu"
           class="px-4 py-1.5 rounded-full text-sm border <?= !$categorieId ? 'bg-brand-orange text-white border-brand-orange' : 'border-brand-brown/20 hover:border-brand-orange' ?>">
            Tous
        </a>
        <?php foreach ($categories as $cat): ?>
            <a href="<?= $base ?>/menu?categorie=<?= (int) $cat->id ?>"
               class="px-4 py-1.5 rounded-full text-sm border <?= $categorieId === (int) $cat->id ? 'bg-brand-orange text-white border-brand-orange' : 'border-brand-brown/20 hover:border-brand-orange' ?>">
                <?= e($cat->nom) ?>
            </a>
        <?php endforeach; ?>
    </div>

    <?php if (empty($produits)): ?>
        <p class="text-brand-brown/60">Aucun plat ne correspond à ta recherche pour le moment.</p>
    <?php else: ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php foreach ($produits as $p): ?>
                <div class="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition">
                    <a href="<?= $base ?>/produits/<?= (int) $p->id ?>">
                        <div class="relative">
                            <?php if ($p->image): ?>
                                <img src="<?= e($p->image) ?>" alt="<?= e($p->libelle) ?>" class="w-full h-40 object-cover">
                            <?php else: ?>
                                <div class="w-full h-40 bg-[#EBD9C9] flex items-center justify-center">
                                    <span class="text-brand-brown/50 text-sm font-medium px-4 text-center"><?= e($p->libelle) ?></span>
                                </div>
                            <?php endif; ?>
                            <span class="absolute top-3 left-3 bg-green-100 text-green-700 text-xs px-2 py-1 rounded-full">
                                Disponible
                            </span>
                        </div>
                    </a>
                    <div class="p-4">
                        <a href="<?= $base ?>/produits/<?= (int) $p->id ?>">
                            <h3 class="font-serif font-bold text-lg mb-1 hover:text-brand-orange"><?= e($p->libelle) ?></h3>
                        </a>
                        <p class="text-sm text-brand-brown/60 mb-3 line-clamp-2"><?= e($p->description ?? '') ?></p>
                        <div class="flex items-center justify-between">
                            <span class="font-semibold text-brand-orange"><?= $p->prixFormate() ?></span>
                            <form method="post" action="<?= $base ?>/panier/ajouter">
                                <input type="hidden" name="_token" value="<?= csrf() ?>">
                                <input type="hidden" name="produit_id" value="<?= (int) $p->id ?>">
                                <input type="hidden" name="quantite" value="1">
                                <button type="submit" class="bg-brand-orange text-white text-sm px-4 py-1.5 rounded-full hover:opacity-90">
                                    + Ajouter
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if ($totalPages > 1):
            $lienPage = function (int $p) use ($motCle, $categorieId): string {
                $params = ['page' => $p];
                if ($motCle) $params['q'] = $motCle;
                if ($categorieId) $params['categorie'] = $categorieId;
                return $base . '/menu?' . http_build_query($params);
            };
        ?>
            <div class="flex justify-center items-center gap-2 mt-10">
                <a href="<?= $lienPage(max(1, $page - 1)) ?>"
                   class="px-4 py-2 rounded-full border border-brand-brown/20 text-sm hover:border-brand-orange">
                    Précédent
                </a>
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="<?= $lienPage($i) ?>"
                       class="w-10 h-10 flex items-center justify-center rounded-full text-sm <?= $i === $page ? 'bg-brand-orange text-white' : 'border border-brand-brown/20 hover:border-brand-orange' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
                <a href="<?= $lienPage(min($totalPages, $page + 1)) ?>"
                   class="px-4 py-2 rounded-full border border-brand-brown/20 text-sm hover:border-brand-orange">
                    Suivant
                </a>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</main>
