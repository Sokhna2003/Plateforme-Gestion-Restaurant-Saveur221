<?php

declare(strict_types=1);

/**
 * @var \App\Models\Produit[] $produits
 * @var int $page
 * @var int $totalPages
 * @var int $total
 * @var string $etat ('' | normal | faible | rupture)
 * @var array{total: int, faible: int, rupture: int} $stats
 * @var string $baseRoute (/admin ou /gerant)
 */

use App\Models\Produit;

$lienBase = $base . $baseRoute . '/stock';
$etatCourant = $etat ?? '';

$etatStock = static function (Produit $produit): string {
    if ($produit->quantiteStock === 0) {
        return 'rupture';
    }
    if ($produit->quantiteStock <= $produit->seuilAlerte) {
        return 'faible';
    }
    return 'normal';
};
?>

<!-- En-tête de la page -->
<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-900">État des stocks en cuisine</h2>
    <p class="text-sm text-gray-500 mt-1">Suivez et approvisionnez les ingrédients et plats traditionnels de Saveur 221.</p>
</div>

<!-- Cartes statistiques -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 border-t-4 border-t-green-500 p-5">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Total produits</p>
                <p class="mt-3 text-3xl font-bold text-gray-900"><?= $stats['total'] ?></p>
            </div>
            <span class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center text-green-600">
                <i class="fa-solid fa-boxes-stacked"></i>
            </span>
        </div>
        <p class="mt-3 text-sm font-medium text-green-600">
            <i class="fa-solid fa-circle-check text-xs mr-1"></i>Tous enregistrés
        </p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 border-t-4 border-t-brand-orange p-5">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Stock faible</p>
                <p class="mt-3 text-3xl font-bold text-gray-900"><?= $stats['faible'] ?></p>
            </div>
            <span class="w-10 h-10 rounded-full bg-brand-orange/10 flex items-center justify-center text-brand-orange">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </span>
        </div>
        <p class="mt-3 text-sm font-medium text-brand-orange">
            <i class="fa-solid fa-circle-exclamation text-xs mr-1"></i>Action requise
        </p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 border-t-4 border-t-red-500 p-5">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Produits en rupture</p>
                <p class="mt-3 text-3xl font-bold text-gray-900"><?= $stats['rupture'] ?></p>
            </div>
            <span class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center text-red-500">
                <i class="fa-solid fa-circle-exclamation"></i>
            </span>
        </div>
        <p class="mt-3 text-sm font-medium text-red-500">
            <i class="fa-solid fa-triangle-exclamation text-xs mr-1"></i>Rupture critique
        </p>
    </div>
</div>

<!-- Inventaire -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100">
    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between gap-4 flex-wrap">
        <h3 class="font-semibold text-gray-900">Inventaire des Plats & Ingrédients</h3>
        <form method="get" action="<?= e($lienBase) ?>" class="flex items-center gap-2">
            <select name="etat" onchange="this.form.submit()"
                    class="text-xs px-3 py-1.5 rounded-lg border border-gray-200 bg-gray-50 text-gray-700 focus:outline-none focus:border-brand-orange cursor-pointer">
                <option value="">Tous les états</option>
                <option value="normal" <?= $etatCourant === 'normal' ? 'selected' : '' ?>>Stock normal</option>
                <option value="faible" <?= $etatCourant === 'faible' ? 'selected' : '' ?>>Stock faible</option>
                <option value="rupture" <?= $etatCourant === 'rupture' ? 'selected' : '' ?>>Rupture</option>
            </select>
        </form>
    </div>

    <?php if (!$produits): ?>
        <div class="py-16 text-center text-sm text-gray-500">
            Aucun produit ne correspond à ce filtre.
        </div>
    <?php else: ?>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 text-left text-xs uppercase tracking-wide text-gray-500">
                    <th class="px-6 py-3 font-medium">Produit</th>
                    <th class="px-6 py-3 font-medium">Stock actuel</th>
                    <th class="px-6 py-3 font-medium">Seuil d'alerte</th>
                    <th class="px-6 py-3 font-medium">État</th>
                    <th class="px-6 py-3 font-medium text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php foreach ($produits as $p): ?>
                    <?php $etat = $etatStock($p); ?>
                    <tr class="hover:bg-gray-50/50 transition">
                        <td class="px-6 py-3">
                            <div class="flex items-center gap-3">
                                <?php if ($p->image): ?>
                                    <img src="<?= e(str_contains($p->image, 'res.cloudinary.com') ? str_replace('/image/upload/', '/image/upload/w_48,h_48,c_fill,q_auto/', $p->image) : $p->image) ?>"
                                         alt="<?= e($p->libelle) ?>" class="w-10 h-10 rounded-lg object-cover">
                                <?php else: ?>
                                    <span class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center text-gray-400 shrink-0">
                                        <i class="fa-solid fa-utensils text-xs"></i>
                                    </span>
                                <?php endif; ?>
                                <div>
                                    <p class="font-medium text-gray-900"><?= e($p->libelle) ?></p>
                                    <?php if ($p->categorieNom): ?>
                                        <p class="text-xs text-gray-500"><?= e($p->categorieNom) ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-3">
                            <span class="font-bold <?= $p->quantiteStock === 0 ? 'text-red-500' : 'text-gray-900' ?>">
                                <?= $p->quantiteStock ?>
                            </span>
                        </td>
                        <td class="px-6 py-3 text-gray-500"><?= $p->seuilAlerte ?></td>
                        <td class="px-6 py-3">
                            <?php if ($etat === 'normal'): ?>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700 whitespace-nowrap">
                                    <i class="fa-solid fa-circle-check text-[10px]"></i> Normal
                                </span>
                            <?php elseif ($etat === 'faible'): ?>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-brand-orange/10 text-brand-orange whitespace-nowrap">
                                    <i class="fa-solid fa-triangle-exclamation text-[10px]"></i> Stock faible
                                </span>
                            <?php else: ?>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-600 whitespace-nowrap">
                                    <i class="fa-solid fa-circle-exclamation text-[10px]"></i> Rupture
                                </span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-3">
                            <div class="flex items-center justify-end gap-2">
                                <button type="button" onclick="openModal('restock-modal-<?= $p->id ?>')"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium text-green-700 bg-green-50 border border-green-200 hover:bg-green-100 transition whitespace-nowrap">
                                    <i class="fa-solid fa-plus text-[10px]"></i> Réapprovisionner
                                </button>
                                <button type="button" onclick="openModal('seuil-modal-<?= $p->id ?>')"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium text-brand-orange bg-brand-orange/10 border border-brand-orange/20 hover:bg-brand-orange/20 transition whitespace-nowrap">
                                    <i class="fa-solid fa-sliders text-[10px]"></i> Modifier le seuil
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="px-6 pb-4">
        <?php include __DIR__ . '/../partials/pagination.php'; ?>
    </div>
    <?php endif; ?>
</div>

<!-- ============================== MODALES ============================== -->
<?php foreach ($produits as $p): ?>
<!-- Réapprovisionner -->
<div id="restock-modal-<?= $p->id ?>" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/50" onclick="closeModal('restock-modal-<?= $p->id ?>')"></div>
    <div class="relative bg-white rounded-2xl shadow-xl max-w-md w-full p-6">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-green-50 flex items-center justify-center text-green-600 text-xl shrink-0">
                <i class="fa-solid fa-boxes-stacked"></i>
            </div>
            <div>
                <h3 class="font-bold text-gray-900">Réapprovisionner</h3>
                <p class="text-sm text-gray-500 mt-1"><?= e($p->libelle) ?></p>
            </div>
        </div>

        <form method="post" action="<?= e($lienBase) ?>/<?= $p->id ?>/reapprovisionner" class="mt-5 space-y-4">
            <input type="hidden" name="_token" value="<?= csrf() ?>">
            <div>
                <p class="text-sm text-gray-500 mb-1">Stock actuel</p>
                <p class="text-xl font-bold text-gray-900"><?= $p->quantiteStock ?></p>
            </div>
            <div>
                <label for="restock-qty-<?= $p->id ?>" class="block text-sm font-medium text-gray-900 mb-1">Quantité à ajouter</label>
                <input type="number" id="restock-qty-<?= $p->id ?>" name="quantite" min="1" step="1" value="1"
                       data-produit-id="<?= $p->id ?>" data-restock-current="<?= $p->quantiteStock ?>"
                       class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:border-brand-orange">
            </div>
            <div class="rounded-lg bg-gray-50 border border-gray-100 px-4 py-3 flex items-center justify-between">
                <span class="text-sm text-gray-500">Nouveau stock calculé</span>
                <span class="text-lg font-bold text-green-600" id="restock-result-<?= $p->id ?>"><?= $p->quantiteStock + 1 ?></span>
            </div>
            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" onclick="closeModal('restock-modal-<?= $p->id ?>')"
                        class="px-4 py-2 rounded-lg text-sm font-medium text-gray-600 border border-gray-200 hover:bg-gray-50 transition">
                    Annuler
                </button>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-white bg-green-500 hover:bg-green-600 transition">
                    <i class="fa-solid fa-check text-xs"></i> Confirmer
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modifier le seuil -->
<div id="seuil-modal-<?= $p->id ?>" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/50" onclick="closeModal('seuil-modal-<?= $p->id ?>')"></div>
    <div class="relative bg-white rounded-2xl shadow-xl max-w-md w-full p-6">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-brand-orange/10 flex items-center justify-center text-brand-orange text-xl shrink-0">
                <i class="fa-solid fa-sliders"></i>
            </div>
            <div>
                <h3 class="font-bold text-gray-900">Modifier le seuil d'alerte</h3>
                <p class="text-sm text-gray-500 mt-1"><?= e($p->libelle) ?></p>
            </div>
        </div>

        <form method="post" action="<?= e($lienBase) ?>/<?= $p->id ?>/seuil" class="mt-5 space-y-4">
            <input type="hidden" name="_token" value="<?= csrf() ?>">
            <div>
                <p class="text-sm text-gray-500 mb-1">Seuil d'alerte actuel</p>
                <p class="text-xl font-bold text-gray-900"><?= $p->seuilAlerte ?></p>
            </div>
            <div>
                <label for="seuil-input-<?= $p->id ?>" class="block text-sm font-medium text-gray-900 mb-1">Nouveau Seuil d'alerte</label>
                <input type="number" id="seuil-input-<?= $p->id ?>" name="seuil" min="0" step="1" value="1"
                       data-produit-id="<?= $p->id ?>" data-seuil-actuel="<?= $p->seuilAlerte ?>"
                       class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:border-brand-orange">
            </div>
            <div class="rounded-lg bg-gray-50 border border-gray-100 px-4 py-3 flex items-center justify-between">
                <span class="text-sm text-gray-500">Le nouveau seuil sera de :</span>
                <span class="text-lg font-bold text-brand-orange" id="seuil-result-<?= $p->id ?>">1</span>
            </div>
            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" onclick="closeModal('seuil-modal-<?= $p->id ?>')"
                        class="px-4 py-2 rounded-lg text-sm font-medium text-gray-600 border border-gray-200 hover:bg-gray-50 transition">
                    Annuler
                </button>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-white bg-brand-orange hover:opacity-90 transition">
                    <i class="fa-solid fa-check text-xs"></i> Confirmer
                </button>
            </div>
        </form>
    </div>
</div>
<?php endforeach; ?>

<script>
(function () {
    document.querySelectorAll('[data-restock-current]').forEach(function (champ) {
        var maj = function () {
            var resultat = document.getElementById('restock-result-' + champ.dataset.produitId);
            if (!resultat) return;
            var valeur = parseInt(champ.value, 10);
            resultat.textContent = isNaN(valeur)
                ? parseInt(champ.dataset.restockCurrent, 10)
                : parseInt(champ.dataset.restockCurrent, 10) + valeur;
        };
        champ.addEventListener('input', maj);
    });

    document.querySelectorAll('[data-seuil-actuel]').forEach(function (champ) {
        var maj = function () {
            var resultat = document.getElementById('seuil-result-' + champ.dataset.produitId);
            if (!resultat) return;
            resultat.textContent = champ.value === ''
                ? champ.dataset.seuilActuel
                : champ.value;
        };
        champ.addEventListener('input', maj);
    });
})();
</script>