<?php

declare(strict_types=1);

/**
 * @var \App\Models\Produit[] $produits
 * @var \App\Models\Categorie[] $categories
 * @var int $page
 * @var int $totalPages
 * @var int $total
 * @var string $motCle
 * @var int|null $categorieId
 * @var string|null $disponible (null | '1' | '0')
 * @var string $vue (liste | carte)
 * @var string $baseRoute (/admin ou /gerant)
 * @var string $mode ('' | creer | modifier)
 * @var \App\Models\Produit|null $produit
 * @var array<string, string> $old
 * @var array<string, string> $errors
 */

$lienBase = $base . $baseRoute . '/produits';
$mode = $mode ?? '';
$produit = $produit ?? null;
$old = $old ?? [];
$errors = $errors ?? [];

$vLibelle = $old['libelle'] ?? ($produit?->libelle ?? '');
$vDescription = $old['description'] ?? ($produit?->description ?? '');
$vPrix = $old['prix'] ?? ($produit !== null ? (string) $produit->prix : '');
$vStock = $old['quantite_stock'] ?? ($produit?->quantiteStock ?? 0);
$vSeuil = $old['seuil_alerte'] ?? ($produit?->seuilAlerte ?? 5);
$vCategorie = $old['categorie_id'] ?? ($produit?->categorieId ?? '');
$dispoInitiale = (isset($old['disponible']) && (string) $old['disponible'] === '1')
    || (!isset($old['disponible']) && $produit !== null && $produit->disponible);

$querySansPage = $_GET;
unset($querySansPage['page']);
$qs = http_build_query($querySansPage);

$urlVue = static function (string $v) use ($lienBase, $page, $qs): string {
    $params = $qs !== '' ? $qs . '&' : '';
    return $lienBase . '?' . $params . 'page=' . $page . '&vue=' . $v;
};
?>

<!-- ============================== EN-TETE ============================== -->
<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6 mb-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-900">Liste des Produits</h2>
        <p class="text-sm text-gray-500 mt-1">Gérez l'ensemble des plats et boissons proposés chez Saveur 221.</p>
    </div>
    <a href="<?= $lienBase ?>/creer"
       class="inline-flex items-center justify-center gap-2 bg-brand-orange text-white px-5 py-2.5 rounded-lg text-sm font-semibold shadow-sm hover:opacity-90 transition whitespace-nowrap">
        <i class="fa-solid fa-plus"></i> Ajouter un produit
    </a>
</div>

<!-- ============================== FILTRES ============================== -->
<form method="get" action="<?= $lienBase ?>"
      class="bg-white border border-gray-200 rounded-xl p-4 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-2 mb-6">
    <div class="relative lg:max-w-md w-full">
        <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
        <input type="text" name="q" value="<?= e($motCle) ?>"
               placeholder="Rechercher un produit..."
               class="w-full border border-gray-200 rounded-lg pl-9 pr-3 py-1.5 text-xs bg-white focus:outline-none focus:border-brand-orange">
    </div>

    <div class="flex flex-wrap items-center gap-2">
        <select name="categorie" class="border border-gray-200 rounded-lg px-2.5 py-1.5 text-xs bg-white focus:outline-none focus:border-brand-orange">
            <option value="">Toutes les catégories</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= (int) $cat->id ?>" <?= (int) $categorieId === (int) $cat->id ? 'selected' : '' ?>>
                    <?= e($cat->nom) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <select name="disponible" class="border border-gray-200 rounded-lg px-2.5 py-1.5 text-xs bg-white focus:outline-none focus:border-brand-orange">
            <option value="">Toutes disponibilités</option>
            <option value="1" <?= $disponible === '1' ? 'selected' : '' ?>>Disponibles</option>
            <option value="0" <?= $disponible === '0' ? 'selected' : '' ?>>Indisponibles</option>
        </select>

        <button type="submit"
                class="px-3 py-1.5 bg-brand-orange text-white text-xs font-medium rounded-lg hover:opacity-90 transition">
            <i class="fa-solid fa-filter mr-1"></i> Filtrer
        </button>
        <a href="<?= $lienBase ?>"
           class="px-3 py-1.5 text-xs font-medium text-gray-500 border border-gray-200 rounded-lg hover:bg-gray-50 transition">
            Réinitialiser
        </a>
    </div>
</form>

<?php if (empty($produits)): ?>
    <div class="bg-white border border-gray-200 rounded-xl p-12 text-center text-gray-500 text-sm">
        <i class="fa-solid fa-utensils mb-3 text-3xl text-gray-300 block"></i>
        <?= ($motCle !== '' || $categorieId !== null || $disponible !== null) ? 'Aucun produit ne correspond à vos critères.' : 'Aucun produit pour le moment.' ?>
    </div>
<?php else: ?>

<?php if ($vue === 'liste'): ?>
<!-- ============================== VUE LISTE ============================== -->
<div class="mt-4 bg-white border border-gray-200 rounded-xl overflow-hidden">
    <div class="flex items-center justify-between px-4 md:px-6 py-3 border-b border-gray-100">
        <span class="text-xs text-gray-400"><?= (string) $total ?> produit<?= $total > 1 ? 's' : '' ?></span>
        <div class="flex items-center gap-2">
            <span class="text-xs text-gray-400 mr-1">Affichage :</span>
            <a href="<?= $urlVue('liste') ?>"
               class="w-8 h-8 flex items-center justify-center rounded-lg <?= $vue === 'liste' ? 'bg-brand-orange text-white' : 'text-gray-400 hover:bg-gray-100' ?>"
               title="Vue liste">
                <i class="fa-solid fa-list"></i>
            </a>
            <a href="<?= $urlVue('carte') ?>"
               class="w-8 h-8 flex items-center justify-center rounded-lg <?= $vue === 'carte' ? 'bg-brand-orange text-white' : 'text-gray-400 hover:bg-gray-100' ?>"
               title="Vue carte">
                <i class="fa-solid fa-table-cells-large"></i>
            </a>
        </div>
    </div>

    <!-- Mobile : cartes empilees -->
    <div class="md:hidden p-4 space-y-3">
        <?php foreach ($produits as $p): ?>
        <div class="border border-gray-200 rounded-xl p-4">
            <div class="flex items-center gap-3">
                <?php if ($p->image): ?>
                    <img src="<?= e(str_contains($p->image, 'res.cloudinary.com') ? str_replace('/image/upload/', '/image/upload/w_96,h_96,c_fill,q_auto/', $p->image) : $p->image) ?>"
                         alt="<?= e($p->libelle) ?>" class="w-12 h-12 rounded-lg object-cover shrink-0">
                <?php else: ?>
                    <div class="w-12 h-12 rounded-lg bg-brand-orange/10 flex items-center justify-center text-brand-orange shrink-0">
                        <i class="fa-solid fa-utensils"></i>
                    </div>
                <?php endif; ?>
                <div class="min-w-0 flex-1">
                    <h3 class="font-semibold text-gray-900 text-sm truncate"><?= e($p->libelle) ?></h3>
                    <p class="text-xs text-brand-orange font-medium truncate"><?= e($p->categorieNom ?? '') ?></p>
                    <p class="text-xs text-gray-400 truncate"><?= $p->dateAjout ? date('d/m/Y', strtotime($p->dateAjout)) : '' ?></p>
                </div>
                <?php if ($p->disponible): ?>
                    <span class="px-2 py-1 rounded-full text-[10px] font-medium bg-green-100 text-green-700 whitespace-nowrap">
                        <i class="fa-solid fa-circle text-[6px]"></i> Disponible
                    </span>
                <?php else: ?>
                    <span class="px-2 py-1 rounded-full text-[10px] font-medium bg-gray-100 text-gray-500 whitespace-nowrap">
                        <i class="fa-solid fa-circle text-[6px]"></i> Indisponible
                    </span>
                <?php endif; ?>
            </div>

            <div class="mt-3 flex items-center justify-between text-sm">
                <span class="font-bold text-gray-900"><?= $p->prixFormate() ?></span>
                <span class="text-xs text-gray-500">Stock : <strong class="<?= $p->estEnRupture() ? 'text-red-500' : 'text-gray-700' ?>"><?= (int) $p->quantiteStock ?></strong></span>
            </div>

            <div class="mt-3 flex items-center gap-2">
                <a href="<?= $lienBase ?>/<?= $p->id ?>/modifier"
                   class="flex-1 inline-flex items-center justify-center gap-2 border border-brand-orange text-brand-orange text-sm font-medium px-3 py-2 rounded-lg hover:bg-brand-orange hover:text-white transition">
                    <i class="fa-solid fa-pen text-xs"></i> Modifier
                </a>
                <a href="<?= $base ?>/produits/<?= $p->id ?>" target="_blank"
                   class="w-9 h-9 inline-flex items-center justify-center rounded-lg bg-sky-50 text-sky-600 hover:bg-sky-500 hover:text-white transition"
                   title="Voir les détails">
                    <i class="fa-solid fa-eye text-xs"></i>
                </a>
                <button type="button" onclick="openModal('suppr-modal-<?= $p->id ?>')"
                        class="w-9 h-9 inline-flex items-center justify-center rounded-lg bg-red-50 text-red-500 hover:bg-red-500 hover:text-white transition"
                        title="Supprimer">
                    <i class="fa-solid fa-trash text-xs"></i>
                </button>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Desktop / tablette : tableau -->
    <div class="hidden md:block overflow-x-auto">
    <table class="w-full text-sm">
        <thead>
            <tr class="bg-gray-50 text-left text-xs uppercase tracking-wide text-gray-500">
                <th class="px-4 py-3 font-medium">Image</th>
                <th class="px-4 py-3 font-medium">Produit</th>
                <th class="px-4 py-3 font-medium">Description</th>
                <th class="px-4 py-3 font-medium">Catégorie</th>
                <th class="px-4 py-3 font-medium">Prix</th>
                <th class="px-4 py-3 font-medium">Date d'ajout</th>
                <th class="px-4 py-3 font-medium">Statut</th>
                <th class="px-4 py-3 font-medium text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            <?php foreach ($produits as $p): ?>
            <tr class="hover:bg-gray-50/50 transition">
                <td class="px-4 py-4">
                    <?php if ($p->image): ?>
                        <img src="<?= e(str_contains($p->image, 'res.cloudinary.com') ? str_replace('/image/upload/', '/image/upload/w_64,h_64,c_fill,q_auto/', $p->image) : $p->image) ?>"
                             alt="<?= e($p->libelle) ?>" class="w-10 h-10 rounded-lg object-cover">
                    <?php else: ?>
                        <div class="w-10 h-10 rounded-lg bg-brand-orange/10 flex items-center justify-center text-brand-orange">
                            <i class="fa-solid fa-utensils"></i>
                        </div>
                    <?php endif; ?>
                </td>
                <td class="px-4 py-4 font-medium text-gray-900"><?= e($p->libelle) ?></td>
                <td class="px-4 py-4 text-gray-500 text-xs max-w-[14rem] break-words"><?= e($p->description ?? '—') ?></td>
                <td class="px-4 py-4">
                    <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-brand-orange/10 text-brand-orange whitespace-nowrap">
                        <?= e($p->categorieNom ?? '') ?>
                    </span>
                </td>
                <td class="px-4 py-4 font-semibold text-gray-900 whitespace-nowrap"><?= $p->prixFormate() ?></td>
                <td class="px-4 py-4 text-gray-500 text-xs whitespace-nowrap">
                    <?= $p->dateAjout ? date('d/m/Y', strtotime($p->dateAjout)) : '—' ?>
                </td>
                <td class="px-4 py-4">
                    <?php if ($p->disponible): ?>
                        <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700 whitespace-nowrap">
                            <i class="fa-solid fa-circle text-[6px]"></i> Disponible
                        </span>
                    <?php else: ?>
                        <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-500 whitespace-nowrap">
                            <i class="fa-solid fa-circle text-[6px]"></i> Indisponible
                        </span>
                    <?php endif; ?>
                </td>
                <td class="px-4 py-4">
                    <div class="flex items-center justify-end gap-1.5">
                        <a href="<?= $base ?>/produits/<?= $p->id ?>" target="_blank"
                           class="w-7 h-7 flex items-center justify-center rounded-lg bg-sky-50 text-sky-600 hover:bg-sky-500 hover:text-white transition"
                           title="Voir les détails">
                            <i class="fa-solid fa-eye text-xs"></i>
                        </a>
                        <a href="<?= $lienBase ?>/<?= $p->id ?>/modifier"
                           class="w-7 h-7 flex items-center justify-center rounded-lg bg-brand-orange/10 text-brand-orange hover:bg-brand-orange hover:text-white transition"
                           title="Modifier">
                            <i class="fa-solid fa-pen text-xs"></i>
                        </a>
                        <button type="button" onclick="openModal('suppr-modal-<?= $p->id ?>')"
                                class="w-7 h-7 flex items-center justify-center rounded-lg bg-red-50 text-red-500 hover:bg-red-500 hover:text-white transition"
                                title="Supprimer">
                            <i class="fa-solid fa-trash text-xs"></i>
                        </button>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    </div>
</div>

<?php else: ?>
<!-- ============================== VUE CARTE ============================== -->
<div class="mt-4 flex items-center justify-between">
    <span class="text-xs text-gray-400"><?= (string) $total ?> produit<?= $total > 1 ? 's' : '' ?></span>
    <div class="flex items-center gap-2">
        <span class="text-xs text-gray-400 mr-1">Affichage :</span>
        <a href="<?= $urlVue('liste') ?>"
           class="w-8 h-8 flex items-center justify-center rounded-lg <?= $vue === 'liste' ? 'bg-brand-orange text-white' : 'text-gray-400 hover:bg-gray-100' ?>"
           title="Vue liste">
            <i class="fa-solid fa-list"></i>
        </a>
        <a href="<?= $urlVue('carte') ?>"
           class="w-8 h-8 flex items-center justify-center rounded-lg <?= $vue === 'carte' ? 'bg-brand-orange text-white' : 'text-gray-400 hover:bg-gray-100' ?>"
           title="Vue carte">
            <i class="fa-solid fa-table-cells-large"></i>
        </a>
    </div>
</div>

<div class="mt-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <?php foreach ($produits as $p): ?>
    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden hover:shadow-md transition flex flex-col">
        <?php if ($p->image): ?>
            <img src="<?= e(str_contains($p->image, 'res.cloudinary.com') ? str_replace('/image/upload/', '/image/upload/w_400,h_200,c_fill,q_auto/', $p->image) : $p->image) ?>"
                 alt="<?= e($p->libelle) ?>" class="w-full h-40 object-cover">
        <?php else: ?>
            <div class="w-full h-40 bg-brand-orange/10 flex items-center justify-center text-brand-orange text-4xl">
                <i class="fa-solid fa-utensils"></i>
            </div>
        <?php endif; ?>

        <div class="p-5 flex-1 flex flex-col">
            <div class="flex items-start justify-between gap-2 mb-1">
                <h3 class="font-semibold text-gray-900"><?= e($p->libelle) ?></h3>
                <?php if ($p->disponible): ?>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-medium bg-green-100 text-green-700 whitespace-nowrap">Disponible</span>
                <?php else: ?>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-medium bg-gray-100 text-gray-500 whitespace-nowrap">Indisponible</span>
                <?php endif; ?>
            </div>
            <p class="text-xs text-brand-orange font-medium mb-1"><?= e($p->categorieNom ?? '') ?></p>
            <p class="text-sm text-gray-500 mb-3 line-clamp-2"><?= e($p->description ?? 'Aucune description') ?></p>

            <div class="flex items-center justify-between mt-auto pt-2 border-t border-gray-100">
                <span class="font-bold text-gray-900"><?= $p->prixFormate() ?></span>
                <span class="text-xs text-gray-400">Stock : <strong class="<?= $p->estEnRupture() ? 'text-red-500' : 'text-gray-700' ?>"><?= (int) $p->quantiteStock ?></strong></span>
            </div>

            <div class="flex items-center gap-2 mt-4">
                <a href="<?= $lienBase ?>/<?= $p->id ?>/modifier"
                   class="flex-1 inline-flex items-center justify-center gap-2 border border-brand-orange text-brand-orange text-sm font-medium px-3 py-2 rounded-lg hover:bg-brand-orange hover:text-white transition">
                    <i class="fa-solid fa-pen text-xs"></i> Modifier
                </a>
                <a href="<?= $base ?>/produits/<?= $p->id ?>" target="_blank"
                   class="w-9 h-9 inline-flex items-center justify-center rounded-lg bg-sky-50 text-sky-600 hover:bg-sky-500 hover:text-white transition"
                   title="Voir les détails">
                    <i class="fa-solid fa-eye text-xs"></i>
                </a>
                <button type="button" onclick="openModal('suppr-modal-<?= $p->id ?>')"
                        class="w-9 h-9 inline-flex items-center justify-center rounded-lg bg-red-50 text-red-500 hover:bg-red-500 hover:text-white transition"
                        title="Supprimer">
                    <i class="fa-solid fa-trash text-xs"></i>
                </button>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<?php endif; ?>

<?php
    $lienBasePagination = $lienBase;
    require __DIR__ . '/../partials/pagination.php';
?>

<?php endif; ?>

<!-- ============================== MODALES SUPPRESSION ============================== -->
<?php foreach ($produits as $p): ?>
<div id="suppr-modal-<?= $p->id ?>" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/50" onclick="closeModal('suppr-modal-<?= $p->id ?>')"></div>
    <div class="relative bg-white rounded-2xl shadow-xl max-w-md w-full p-6">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-red-50 flex items-center justify-center text-red-500 text-xl shrink-0">
                <i class="fa-solid fa-trash-can"></i>
            </div>
            <div>
                <h3 class="font-bold text-gray-900">Supprimer le produit ?</h3>
                <p class="text-sm text-gray-500 mt-1">
                    « <?= e($p->libelle) ?> » sera déplacé dans la corbeille.
                    Vous pourrez le restaurer plus tard.
                </p>
            </div>
        </div>
        <div class="mt-6 flex items-center justify-end gap-3">
            <button type="button" onclick="closeModal('suppr-modal-<?= $p->id ?>')"
                    class="px-4 py-2 rounded-lg text-sm font-medium text-gray-600 border border-gray-200 hover:bg-gray-50 transition">
                Annuler
            </button>
            <form method="post" action="<?= $lienBase ?>/<?= $p->id ?>/supprimer">
                <input type="hidden" name="_token" value="<?= csrf() ?>">
                <button type="submit"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-white bg-red-500 hover:bg-red-600 transition">
                    <i class="fa-solid fa-trash text-xs"></i> Supprimer
                </button>
            </form>
        </div>
    </div>
</div>
<?php endforeach; ?>

<!-- ============================== DRAWER AJOUT / MODIFICATION ============================== -->
<div id="produit-drawer" class="fixed inset-0 z-50 <?= $mode === '' ? 'hidden' : '' ?>">
    <div class="absolute inset-0 bg-black/50" onclick="fermerDrawer()"></div>
    <div class="absolute right-0 top-0 h-full w-full max-w-md bg-white shadow-xl flex flex-col">
        <header class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h3 class="font-bold text-gray-900"><?= $mode === 'modifier' ? 'Modifier le produit' : 'Ajouter un produit' ?></h3>
            <button type="button" onclick="fermerDrawer()"
                    class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:bg-gray-100 transition">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </header>

        <form method="post"
              action="<?= $mode === 'modifier' ? $lienBase . '/' . $produit->id . '/modifier' : $lienBase . '/creer' ?>"
              enctype="multipart/form-data"
              class="flex-1 overflow-y-auto px-6 py-5 space-y-5">
            <input type="hidden" name="_token" value="<?= csrf() ?>">
            <?php if ($mode === 'modifier'): ?>
                <input type="hidden" name="image_actuelle" value="<?= e($produit->image ?? '') ?>">
            <?php endif; ?>

            <div>
                <label for="p-libelle" class="block text-sm font-medium text-gray-900 mb-1">Nom du produit <span class="text-red-500">*</span></label>
                <input type="text" id="p-libelle" name="libelle" value="<?= e($vLibelle) ?>"
                       placeholder="Ex : Thieboudienne, Jus de bissap..."
                       class="w-full border <?= isset($errors['libelle']) ? 'border-red-400 bg-red-50' : 'border-gray-200' ?> rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-brand-orange">
                <?php if ($errors['libelle'] ?? false): ?>
                    <p class="mt-1 text-xs text-red-600"><?= e($errors['libelle']) ?></p>
                <?php endif; ?>
            </div>

            <div>
                <label for="p-categorie" class="block text-sm font-medium text-gray-900 mb-1">Catégorie <span class="text-red-500">*</span></label>
                <select id="p-categorie" name="categorie_id"
                        class="w-full border <?= isset($errors['categorie_id']) ? 'border-red-400 bg-red-50' : 'border-gray-200' ?> rounded-lg px-4 py-2.5 text-sm bg-white focus:outline-none focus:border-brand-orange">
                    <option value="">Choisir une catégorie...</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= (int) $cat->id ?>" <?= (string) $vCategorie === (string) $cat->id ? 'selected' : '' ?>>
                            <?= e($cat->nom) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if ($errors['categorie_id'] ?? false): ?>
                    <p class="mt-1 text-xs text-red-600"><?= e($errors['categorie_id']) ?></p>
                <?php endif; ?>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="p-prix" class="block text-sm font-medium text-gray-900 mb-1">Prix (FCFA) <span class="text-red-500">*</span></label>
                    <input type="number" id="p-prix" name="prix" min="0" step="any" value="<?= e($vPrix) ?>"
                           placeholder="1500"
                           class="w-full border <?= isset($errors['prix']) ? 'border-red-400 bg-red-50' : 'border-gray-200' ?> rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-brand-orange">
                    <?php if ($errors['prix'] ?? false): ?>
                        <p class="mt-1 text-xs text-red-600"><?= e($errors['prix']) ?></p>
                    <?php endif; ?>
                </div>

                <div>
                    <label for="p-stock" class="block text-sm font-medium text-gray-900 mb-1">Stock</label>
                    <input type="number" id="p-stock" name="quantite_stock" min="0" step="1" value="<?= e((string) $vStock) ?>"
                           class="w-full border <?= isset($errors['quantite_stock']) ? 'border-red-400 bg-red-50' : 'border-gray-200' ?> rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-brand-orange">
                    <?php if ($errors['quantite_stock'] ?? false): ?>
                        <p class="mt-1 text-xs text-red-600"><?= e($errors['quantite_stock']) ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <div>
                <label for="p-seuil" class="block text-sm font-medium text-gray-900 mb-1">Seuil d'alerte</label>
                <input type="number" id="p-seuil" name="seuil_alerte" min="0" step="1" value="<?= e((string) $vSeuil) ?>"
                       class="w-full border <?= isset($errors['seuil_alerte']) ? 'border-red-400 bg-red-50' : 'border-gray-200' ?> rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-brand-orange">
                <?php if ($errors['seuil_alerte'] ?? false): ?>
                    <p class="mt-1 text-xs text-red-600"><?= e($errors['seuil_alerte']) ?></p>
                <?php endif; ?>
                <p class="mt-1 text-xs text-gray-400">En dessous de ce stock, le produit est signalé.</p>
            </div>

            <div class="flex items-center justify-between bg-gray-50 border border-gray-100 rounded-lg px-4 py-3">
                <div>
                    <span class="block text-sm font-medium text-gray-900">Disponibilité</span>
                    <span id="dispo-label" class="text-xs <?= $dispoInitiale ? 'text-green-600 font-medium' : 'text-gray-400' ?>">
                        <?= $dispoInitiale ? 'Disponible' : 'Non disponible' ?>
                    </span>
                </div>
                <span id="dispo-switch" onclick="basculerDispo()"
                      class="cursor-pointer w-12 h-6 rounded-full transition flex items-center px-0.5 <?= $dispoInitiale ? 'bg-green-500 justify-end' : 'bg-gray-300 justify-start' ?>">
                    <span class="w-5 h-5 bg-white rounded-full shadow-sm"></span>
                </span>
                <input type="hidden" name="disponible" id="dispo-value" value="<?= $dispoInitiale ? '1' : '0' ?>">
            </div>

            <div>
                <label for="p-description" class="block text-sm font-medium text-gray-900 mb-1">Description</label>
                <textarea id="p-description" name="description" rows="3"
                          placeholder="Décrivez le produit..."
                          class="w-full border <?= isset($errors['description']) ? 'border-red-400 bg-red-50' : 'border-gray-200' ?> rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-brand-orange resize-none"><?= e($vDescription) ?></textarea>
                <?php if ($errors['description'] ?? false): ?>
                    <p class="mt-1 text-xs text-red-600"><?= e($errors['description']) ?></p>
                <?php endif; ?>
            </div>

            <div>
                <label for="p-image" class="block text-sm font-medium text-gray-900 mb-1">Image</label>
                <div id="apercu-produit" class="<?= ($mode === 'modifier' && $produit?->image) ? 'mb-3' : 'hidden mb-3' ?>">
                    <img id="apercu-produit-src"
                         src="<?= $mode === 'modifier' && $produit?->image ? e($produit->image) : '' ?>"
                         alt="Aperçu de l'image" class="w-24 h-24 rounded-lg object-cover border border-gray-200 shadow-sm">
                    <p class="mt-1 text-xs text-gray-500" id="produit-image-nom">
                        <?= $mode === 'modifier' && $produit?->image ? 'Image actuelle du produit.' : '' ?>
                    </p>
                </div>
                <label for="p-image"
                       class="flex flex-col items-center justify-center w-full border-2 border-dashed <?= isset($errors['image']) ? 'border-red-400 bg-red-50' : 'border-gray-200' ?> rounded-lg px-4 py-6 cursor-pointer hover:border-brand-orange hover:bg-brand-orange/5 transition text-center">
                    <i class="fa-solid fa-cloud-arrow-up text-xl text-gray-300"></i>
                    <span class="mt-2 text-sm text-gray-500">Cliquez pour choisir une image</span>
                    <span class="mt-1 text-xs text-gray-400">JPG, PNG, GIF ou WebP — 2 Mo maximum.</span>
                </label>
                <input type="file" id="p-image" name="image" accept="image/*" class="hidden">
                <?php if ($errors['image'] ?? false): ?>
                    <p class="mt-1 text-xs text-red-600"><?= e($errors['image']) ?></p>
                <?php endif; ?>
            </div>

            <div class="flex items-center gap-3 pt-2 pb-4">
                <button type="submit"
                        class="flex-1 inline-flex items-center justify-center gap-2 bg-brand-orange text-white px-4 py-2.5 rounded-lg text-sm font-semibold hover:opacity-90 transition">
                    <i class="fa-solid fa-check"></i> <?= $mode === 'modifier' ? 'Enregistrer les modifications' : 'Ajouter le produit' ?>
                </button>
                <button type="button" onclick="fermerDrawer()"
                        class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg text-sm font-medium text-gray-600 border border-gray-200 hover:bg-gray-50 transition">
                    Annuler
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function ouvrirDrawer() {
        const el = document.getElementById('produit-drawer');
        if (el) {
            el.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }
    }
    function fermerDrawer() {
        const el = document.getElementById('produit-drawer');
        if (el) {
            el.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }
    }
    function basculerDispo() {
        const sw = document.getElementById('dispo-switch');
        const val = document.getElementById('dispo-value');
        const lbl = document.getElementById('dispo-label');
        if (!sw || !val || !lbl) return;
        const on = val.value === '1';
        val.value = on ? '0' : '1';
        sw.classList.toggle('bg-green-500', !on);
        sw.classList.toggle('bg-gray-300', on);
        sw.classList.toggle('justify-end', !on);
        sw.classList.toggle('justify-start', on);
        lbl.textContent = on ? 'Non disponible' : 'Disponible';
        lbl.className = on ? 'text-xs text-gray-400' : 'text-xs text-green-600 font-medium';
    }
    const imageProduit = document.getElementById('p-image');
    if (imageProduit) {
        imageProduit.addEventListener('change', function () {
            const fichier = this.files && this.files[0];
            const boite = document.getElementById('apercu-produit');
            const src = document.getElementById('apercu-produit-src');
            const nom = document.getElementById('produit-image-nom');
            if (!fichier) return;
            if (boite) boite.classList.remove('hidden');
            if (nom) nom.textContent = fichier.name;
            const lecteur = new FileReader();
            lecteur.onload = function (e) { if (src) src.src = e.target.result; };
            lecteur.readAsDataURL(fichier);
        });
    }
    if (document.getElementById('produit-drawer') && !document.getElementById('produit-drawer').classList.contains('hidden')) {
        document.body.classList.add('overflow-hidden');
    }
</script>