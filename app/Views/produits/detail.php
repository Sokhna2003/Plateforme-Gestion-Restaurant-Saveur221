<main class="max-w-7xl mx-auto px-6 py-10">
    <nav class="text-sm text-brand-brown/50 mb-6">
        <a href="<?= $base ?>/menu" class="hover:text-brand-orange">Menu</a>
        <span class="mx-1">→</span>
        <a href="<?= $base ?>/menu?categorie=<?= (int) $produit->categorieId ?>" class="hover:text-brand-orange">
            <?= e($produit->categorieNom) ?>
        </a>
        <span class="mx-1">→</span>
        <span class="text-brand-brown"><?= e($produit->libelle) ?></span>
    </nav>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-10 mb-16">
        <?php if ($produit->image): ?>
            <img src="<?= e($produit->image) ?>" alt="<?= e($produit->libelle) ?>" class="w-full h-96 rounded-2xl object-cover">
        <?php else: ?>
            <div class="w-full h-96 rounded-2xl bg-[#EBD9C9] flex items-center justify-center">
                <span class="text-brand-brown/50 font-medium text-lg"><?= e($produit->libelle) ?></span>
            </div>
        <?php endif; ?>

        <div>
            <div class="flex gap-2 mb-3">
                <span class="text-xs bg-brand-cream border border-brand-brown/10 px-2 py-1 rounded-full">
                    <?= e($produit->categorieNom) ?>
                </span>
                <span class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded-full">Disponible</span>
            </div>

            <h1 class="font-serif text-3xl font-bold mb-3"><?= e($produit->libelle) ?></h1>

            <p class="text-2xl font-semibold text-brand-orange mb-4">
                <?= $produit->prixFormate() ?>
            </p>

            <p class="text-brand-brown/70 leading-relaxed mb-6">
                <?= nl2br(e($produit->description ?? '')) ?>
            </p>

            <form method="post" action="<?= $base ?>/panier/ajouter" class="flex items-center gap-4">
                <input type="hidden" name="_token" value="<?= csrf() ?>">
                <input type="hidden" name="produit_id" value="<?= (int) $produit->id ?>">

                <div class="flex items-center border border-brand-brown/20 rounded-full overflow-hidden">
                    <button type="button" onclick="changerQuantite(-1)" class="w-11 h-11 flex items-center justify-center hover:bg-brand-cream text-lg">−</button>
                    <input type="number" name="quantite" id="quantite" value="1" min="1" readonly
                           class="w-12 text-center border-x border-brand-brown/20 py-2 focus:outline-none">
                    <button type="button" onclick="changerQuantite(1)" class="w-11 h-11 flex items-center justify-center hover:bg-brand-cream text-lg">+</button>
                </div>

                <button type="submit" class="flex-1 bg-brand-orange text-white py-3 rounded-full font-medium hover:opacity-90 flex items-center justify-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l3-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m-9-1a1 1 0 102 0 1 1 0 00-2 0zm9 0a1 1 0 102 0 1 1 0 00-2 0z" />
                    </svg>
                    Ajouter au panier
                </button>
            </form>
        </div>
    </div>

    <?php if (!empty($similaires)): ?>
        <h2 class="font-serif text-2xl font-bold mb-2">Vous pourriez aussi aimer</h2>
        <span class="block w-16 h-1 bg-brand-orange mb-6"></span>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($similaires as $s): ?>
                <a href="<?= $base ?>/produits/<?= (int) $s->id ?>" class="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition block">
                    <?php if ($s->image): ?>
                        <img src="<?= e($s->image) ?>" alt="<?= e($s->libelle) ?>" class="w-full h-40 object-cover">
                    <?php else: ?>
                        <div class="w-full h-40 bg-[#EBD9C9] flex items-center justify-center">
                            <span class="text-brand-brown/50 text-sm font-medium px-4 text-center"><?= e($s->libelle) ?></span>
                        </div>
                    <?php endif; ?>
                    <div class="p-4">
                        <h3 class="font-serif font-bold mb-1"><?= e($s->libelle) ?></h3>
                        <span class="text-brand-orange font-semibold"><?= $s->prixFormate() ?></span>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>

<script>
function changerQuantite(delta) {
    const input = document.getElementById('quantite');
    let valeur = parseInt(input.value, 10) + delta;
    if (valeur < 1) valeur = 1;
    input.value = valeur;
}
</script>
