<?php

declare(strict_types=1);

/**
 * @var \App\Models\Categorie[] $categories
 * @var int $page
 * @var int $totalPages
 * @var int $total
 * @var string $motCle
 * @var string $vue (liste | carte)
 * @var string $baseRoute (/admin ou /gerant)
 */

$lienBase = $base . $baseRoute . '/categories';
?>

<!-- Section titre + recherche + bouton ajouter -->
<div class="flex flex-col md:flex-row md:items-end md:justify-between gap-6 mb-8">
    <div>
        <h2 class="text-2xl font-bold text-gray-900">Catégories de Menu</h2>
        <p class="text-sm text-gray-500 mt-1">Organisez vos plats sénégalais par catégories d'aliments.</p>
    </div>

    <div class="flex flex-col sm:flex-row sm:items-center gap-3">
        <form method="get" action="<?= $lienBase ?>" class="flex items-center">
            <div class="relative">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                <input type="text" name="q" value="<?= e($motCle) ?>"
                       placeholder="Rechercher une catégorie..."
                       class="w-64 border border-gray-200 rounded-lg pl-9 pr-3 py-2 text-sm bg-white focus:outline-none focus:border-brand-orange">
            </div>
            <button type="submit" class="ml-2 px-4 py-2 bg-[#D95F02] text-white text-sm font-medium rounded-lg hover:opacity-90 transition">
                Rechercher
            </button>
        </form>

        <a href="<?= $base . $baseRoute ?>/categories/creer"
           class="inline-flex items-center justify-center gap-2 bg-[#D95F02] text-white px-4 py-2 rounded-lg text-sm font-medium hover:opacity-90 transition">
            <i class="fa-solid fa-plus"></i> Ajouter une catégorie
        </a>
    </div>
</div>

<?php if (empty($categories)): ?>
    <div class="bg-white border border-gray-200 rounded-xl p-12 text-center text-gray-500 text-sm">
        <?= $motCle !== '' ? 'Aucune catégorie ne correspond à votre recherche.' : 'Aucune catégorie pour le moment.' ?>
    </div>
<?php else: ?>

<?php if ($vue === 'liste'): ?>
<!-- ============================== VUE LISTE ============================== -->
<div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
    <div class="flex items-center justify-between px-4 md:px-6 py-3 border-b border-gray-100">
        <span class="text-xs text-gray-400"><?= e((string) $total) ?> catégorie<?= $total > 1 ? 's' : '' ?></span>
        <div class="flex items-center gap-2">
            <span class="text-xs text-gray-400 mr-1">Affichage :</span>
            <a href="<?= $lienBase ?>?q=<?= e($motCle) ?>&page=<?= e((string) $page) ?>&vue=liste"
               class="w-8 h-8 flex items-center justify-center rounded-lg <?= $vue === 'liste' ? 'bg-brand-orange text-white' : 'text-gray-400 hover:bg-gray-100' ?>"
               title="Vue liste">
                <i class="fa-solid fa-list"></i>
            </a>
            <a href="<?= $lienBase ?>?q=<?= e($motCle) ?>&page=<?= e((string) $page) ?>&vue=carte"
               class="w-8 h-8 flex items-center justify-center rounded-lg <?= $vue === 'carte' ? 'bg-brand-orange text-white' : 'text-gray-400 hover:bg-gray-100' ?>"
               title="Vue carte">
                <i class="fa-solid fa-table-cells-large"></i>
            </a>
        </div>
    </div>

    <!-- Mobile : cartes empilees -->
    <div class="md:hidden p-4 space-y-3">
        <?php foreach ($categories as $cat): ?>
        <div class="border border-gray-200 rounded-xl p-4">
            <div class="flex items-center gap-3">
                <?php if ($cat->image): ?>
                    <img src="<?= e(str_contains($cat->image, 'res.cloudinary.com') ? str_replace('/image/upload/', '/image/upload/w_96,h_96,c_fill,q_auto/', $cat->image) : $cat->image) ?>"
                         alt="<?= e($cat->nom) ?>" class="w-12 h-12 rounded-lg object-cover shrink-0">
                <?php else: ?>
                    <div class="w-12 h-12 rounded-lg bg-brand-orange/10 flex items-center justify-center text-brand-orange shrink-0">
                        <i class="fa-solid fa-folder-open"></i>
                    </div>
                <?php endif; ?>
                <div class="min-w-0 flex-1">
                    <h3 class="font-semibold text-gray-900 text-sm truncate"><?= e($cat->nom) ?></h3>
                    <p class="text-xs text-gray-400"><?= date('d/m/Y', strtotime($cat->dateAjout)) ?></p>
                </div>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-medium <?= $cat->nombreDeProduits > 0 ? 'bg-brand-orange/10 text-brand-orange' : 'bg-gray-100 text-gray-500' ?> whitespace-nowrap">
                    <?= $cat->nombreDeProduits ?> produit<?= $cat->nombreDeProduits > 1 ? 's' : '' ?>
                </span>
            </div>
            <p class="mt-2 text-xs text-gray-500 truncate"><?= e($cat->description ?? '—') ?></p>
            <div class="mt-3 flex items-center gap-2">
                <a href="<?= $base . $baseRoute ?>/categories/<?= $cat->id ?>/modifier"
                   class="flex-1 inline-flex items-center justify-center gap-2 border border-brand-orange text-brand-orange text-sm font-medium px-3 py-2 rounded-lg hover:bg-brand-orange hover:text-white transition">
                    <i class="fa-solid fa-pen text-xs"></i> Modifier
                </a>
                <button type="button" onclick="openModal('suppr-modal-<?= $cat->id ?>')"
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
                <th class="px-6 py-3 font-medium">Image</th>
                <th class="px-6 py-3 font-medium">Nom de la catégorie</th>
                <th class="px-6 py-3 font-medium">Nombre de produits</th>
                <th class="px-6 py-3 font-medium">Description</th>
                <th class="px-6 py-3 font-medium">Date d'ajout</th>
                <th class="px-6 py-3 font-medium text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            <?php foreach ($categories as $cat): ?>
            <tr class="hover:bg-gray-50/50 transition">
                <td class="px-6 py-4">
                    <?php if ($cat->image): ?>
                        <img src="<?= e(str_contains($cat->image, 'res.cloudinary.com') ? str_replace('/image/upload/', '/image/upload/w_80,h_80,c_fill,q_auto/', $cat->image) : $cat->image) ?>"
                             alt="<?= e($cat->nom) ?>"
                             class="w-12 h-12 rounded-lg object-cover">
                    <?php else: ?>
                        <div class="w-12 h-12 rounded-lg bg-brand-orange/10 flex items-center justify-center text-brand-orange">
                            <i class="fa-solid fa-folder-open"></i>
                        </div>
                    <?php endif; ?>
                </td>
                <td class="px-6 py-4 font-medium text-gray-900"><?= e($cat->nom) ?></td>
                <td class="px-6 py-4">
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-medium <?= $cat->nombreDeProduits > 0 ? 'bg-brand-orange/10 text-brand-orange' : 'bg-gray-100 text-gray-500' ?>">
                        <?= $cat->nombreDeProduits ?> produit<?= $cat->nombreDeProduits > 1 ? 's' : '' ?>
                    </span>
                </td>
                <td class="px-6 py-4 text-gray-500 max-w-[16rem] truncate"><?= e($cat->description ?? '—') ?></td>
                <td class="px-6 py-4 text-gray-500"><?= date('d/m/Y', strtotime($cat->dateAjout)) ?></td>
                <td class="px-6 py-4">
                    <div class="flex items-center justify-end gap-2">
                        <a href="<?= $base . $baseRoute ?>/categories/<?= $cat->id ?>/modifier"
                           class="w-8 h-8 flex items-center justify-center rounded-lg bg-brand-orange/10 text-brand-orange hover:bg-brand-orange hover:text-white transition"
                           title="Modifier">
                            <i class="fa-solid fa-pen text-xs"></i>
                        </a>
                        <button type="button" onclick="openModal('suppr-modal-<?= $cat->id ?>')"
                                class="w-8 h-8 flex items-center justify-center rounded-lg bg-red-50 text-red-500 hover:bg-red-500 hover:text-white transition"
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

<?php
    $lienBasePagination = $lienBase;
    require __DIR__ . '/../partials/pagination.php';
?>

<?php else: ?>
<!-- ============================== VUE CARTE ============================== -->
<div class="mb-4 flex items-center justify-between">
    <span class="text-xs text-gray-400"><?= e((string) $total) ?> catégorie<?= $total > 1 ? 's' : '' ?></span>
    <div class="flex items-center gap-2">
        <span class="text-xs text-gray-400 mr-1">Affichage :</span>
        <a href="<?= $lienBase ?>?q=<?= e($motCle) ?>&page=<?= e((string) $page) ?>&vue=liste"
           class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:bg-gray-100"
           title="Vue liste">
            <i class="fa-solid fa-list"></i>
        </a>
        <a href="<?= $lienBase ?>?q=<?= e($motCle) ?>&page=<?= e((string) $page) ?>&vue=carte"
           class="w-8 h-8 flex items-center justify-center rounded-lg bg-brand-orange text-white"
           title="Vue carte">
            <i class="fa-solid fa-table-cells-large"></i>
        </a>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <?php foreach ($categories as $cat): ?>
    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden hover:shadow-md transition">
        <?php if ($cat->image): ?>
            <img src="<?= e(str_contains($cat->image, 'res.cloudinary.com') ? str_replace('/image/upload/', '/image/upload/w_400,h_200,c_fill,q_auto/', $cat->image) : $cat->image) ?>"
                 alt="<?= e($cat->nom) ?>" class="w-full h-36 object-cover">
        <?php else: ?>
            <div class="w-full h-36 bg-brand-orange/10 flex items-center justify-center text-brand-orange text-4xl">
                <i class="fa-solid fa-folder-open"></i>
            </div>
        <?php endif; ?>

        <div class="p-5">
            <div class="flex items-start justify-between gap-2 mb-2">
                <h3 class="font-semibold text-gray-900"><?= e($cat->nom) ?></h3>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-brand-orange/10 text-brand-orange whitespace-nowrap">
                    <?= $cat->nombreDeProduits ?> produit<?= $cat->nombreDeProduits > 1 ? 's' : '' ?>
                </span>
            </div>
            <p class="text-sm text-gray-500 mb-3 line-clamp-2"><?= e($cat->description ?? 'Aucune description') ?></p>
            <p class="text-xs text-gray-400 mb-4">Ajoutée le <?= date('d/m/Y', strtotime($cat->dateAjout)) ?></p>

            <div class="flex items-center gap-2">
                <a href="<?= $base . $baseRoute ?>/categories/<?= $cat->id ?>/modifier"
                   class="flex-1 inline-flex items-center justify-center gap-2 border border-brand-orange text-brand-orange text-sm font-medium px-3 py-2 rounded-lg hover:bg-brand-orange hover:text-white transition">
                    <i class="fa-solid fa-pen text-xs"></i> Modifier
                </a>
                <button type="button" onclick="openModal('suppr-modal-<?= $cat->id ?>')"
                        class="w-9 h-9 flex items-center justify-center rounded-lg bg-red-50 text-red-500 hover:bg-red-500 hover:text-white transition"
                        title="Supprimer">
                    <i class="fa-solid fa-trash text-xs"></i>
                </button>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<?php
    $lienBasePagination = $lienBase;
    require __DIR__ . '/../partials/pagination.php';
?>

<?php endif; ?>

<?php endif; ?>

<!-- ============================== MODALES SUPPRESSION ============================== -->
<?php foreach ($categories as $cat): ?>
<div id="suppr-modal-<?= $cat->id ?>" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/50" onclick="closeModal('suppr-modal-<?= $cat->id ?>')"></div>
    <div class="relative bg-white rounded-2xl shadow-xl max-w-md w-full p-6">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-red-50 flex items-center justify-center text-red-500 text-xl shrink-0">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <div>
                <h3 class="font-bold text-gray-900">Supprimer la catégorie ?</h3>
                <p class="text-sm text-gray-500 mt-1">
                    « <?= e($cat->nom) ?> » sera déplacée dans la corbeille.
                    Vous pourrez la restaurer plus tard.
                </p>
            </div>
        </div>
        <?php if ($cat->nombreDeProduits > 0): ?>
            <p class="mt-4 text-sm bg-red-50 border border-red-100 text-red-700 rounded-lg px-3 py-2">
                <i class="fa-solid fa-circle-exclamation text-xs mr-1"></i>
                Suppression impossible : <?= $cat->nombreDeProduits ?> produit(s) sont encore liés à cette catégorie.
            </p>
        <?php endif; ?>
        <div class="mt-6 flex items-center justify-end gap-3">
            <button type="button" onclick="closeModal('suppr-modal-<?= $cat->id ?>')"
                    class="px-4 py-2 rounded-lg text-sm font-medium text-gray-600 border border-gray-200 hover:bg-gray-50 transition">
                Annuler
            </button>
            <form method="post" action="<?= $base . $baseRoute ?>/categories/<?= $cat->id ?>/supprimer">
                <input type="hidden" name="_token" value="<?= csrf() ?>">
                <button type="submit" <?= $cat->nombreDeProduits > 0 ? 'disabled' : '' ?>
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-white bg-red-500 hover:bg-red-600 transition <?= $cat->nombreDeProduits > 0 ? 'opacity-50 cursor-not-allowed' : '' ?>">
                    <i class="fa-solid fa-trash text-xs"></i> Supprimer
                </button>
            </form>
        </div>
    </div>
</div>
<?php endforeach; ?>