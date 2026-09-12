<?php

declare(strict_types=1);

/**
 * @var array<int, array<string, mixed>> $items
 * @var array<string, string> $entites
 * @var string $entiteActive ('tout' | 'categorie' | 'produit')
 * @var string $motCle
 * @var string $baseRoute (/admin ou /gerant)
 */

$lienBase = $base . $baseRoute . '/corbeille';
?>

<!-- Section titre + filtres -->
<div class="flex flex-col md:flex-row md:items-end md:justify-between gap-6 mb-8">
    <div>
        <h2 class="text-2xl font-bold text-gray-900">Corbeille</h2>
        <p class="text-sm text-gray-500 mt-1">Éléments supprimés : restaurez-les ou supprimez-les définitivement.</p>
    </div>

    <form method="get" action="<?= $lienBase ?>" class="flex flex-col sm:flex-row sm:items-center gap-3">
        <select name="entite"
                class="border border-gray-200 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:border-brand-orange">
            <option value="tout" <?= $entiteActive === 'tout' ? 'selected' : '' ?>>Toutes les entités</option>
            <?php foreach ($entites as $cle => $libelle): ?>
                <option value="<?= e($cle) ?>" <?= $entiteActive === $cle ? 'selected' : '' ?>><?= e($libelle) ?></option>
            <?php endforeach; ?>
        </select>

        <div class="relative">
            <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
            <input type="text" name="q" value="<?= e($motCle) ?>"
                   placeholder="Rechercher par nom..."
                   class="w-64 border border-gray-200 rounded-lg pl-9 pr-3 py-2 text-sm bg-white focus:outline-none focus:border-brand-orange">
        </div>

        <button type="submit" class="px-4 py-2 bg-[#D95F02] text-white text-sm font-medium rounded-lg hover:opacity-90 transition">
            Filtrer
        </button>
    </form>
</div>

<?php if (empty($items)): ?>
    <div class="bg-white border border-gray-200 rounded-xl p-12 text-center text-gray-500 text-sm">
        <i class="fa-solid fa-trash-can mb-3 text-3xl text-gray-300 block"></i>
        <?= $motCle !== '' ? 'Aucun élément ne correspond à votre recherche dans la corbeille.' : 'La corbeille est vide.' ?>
    </div>
<?php else: ?>

<div class="bg-white border border-gray-200 rounded-xl overflow-x-auto">
    <table class="w-full text-sm">
        <thead>
            <tr class="bg-gray-50 text-left text-xs uppercase tracking-wide text-gray-500">
                <th class="px-6 py-3 font-medium">Type</th>
                <th class="px-6 py-3 font-medium">Image</th>
                <th class="px-6 py-3 font-medium">Nom</th>
                <th class="px-6 py-3 font-medium">Détail</th>
                <th class="px-6 py-3 font-medium">Date de suppression</th>
                <th class="px-6 py-3 font-medium text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            <?php foreach ($items as $item): ?>
            <?php $isCategorie = $item['entite'] === 'categorie'; ?>
            <tr class="hover:bg-gray-50/50 transition">
                <td class="px-6 py-4">
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-medium <?= $isCategorie ? 'bg-brand-orange/10 text-brand-orange' : 'bg-sky-50 text-sky-600' ?>">
                        <i class="fa-solid <?= $isCategorie ? 'fa-folder-open' : 'fa-utensils' ?> text-[10px] mr-1"></i>
                        <?= e($item['type']) ?>
                    </span>
                </td>
                <td class="px-6 py-4">
                    <?php if ($item['image']): ?>
                        <img src="<?= e(str_contains($item['image'], 'res.cloudinary.com') ? str_replace('/image/upload/', '/image/upload/w_80,h_80,c_fill,q_auto/', $item['image']) : $item['image']) ?>"
                             alt="<?= e($item['nom']) ?>"
                             class="w-12 h-12 rounded-lg object-cover grayscale">
                    <?php else: ?>
                        <div class="w-12 h-12 rounded-lg bg-gray-100 flex items-center justify-center text-gray-400 grayscale">
                            <i class="fa-solid <?= $isCategorie ? 'fa-folder-open' : 'fa-utensils' ?>"></i>
                        </div>
                    <?php endif; ?>
                </td>
                <td class="px-6 py-4 font-medium text-gray-900"><?= e($item['nom']) ?></td>
                <td class="px-6 py-4 text-gray-500 max-w-[16rem] truncate">
                    <span class="<?= $isCategorie ? '' : 'text-sky-600' ?>"><?= e($item['detail']) ?></span>
                    <span class="text-gray-400 text-xs block mt-0.5"><?= e($item['infos']) ?></span>
                </td>
                <td class="px-6 py-4 text-gray-500"><?= date('d/m/Y H:i', strtotime($item['dateSuppression'])) ?></td>
                <td class="px-6 py-4">
                    <div class="flex items-center justify-end gap-2">
                        <form method="post" action="<?= $lienBase ?>/<?= e($item['entite']) ?>/<?= $item['id'] ?>/restaurer">
                            <input type="hidden" name="_token" value="<?= csrf() ?>">
                            <button type="submit"
                                    class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg bg-green-50 text-green-600 text-xs font-medium hover:bg-green-500 hover:text-white transition"
                                    title="Restaurer">
                                <i class="fa-solid fa-rotate-left text-xs"></i> Restaurer
                            </button>
                        </form>
                        <button type="button" onclick="openModal('def-modal-<?= e($item['entite']) ?>-<?= $item['id'] ?>')"
                                class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg bg-red-50 text-red-500 text-xs font-medium hover:bg-red-500 hover:text-white transition"
                                title="Supprimer définitivement">
                            <i class="fa-solid fa-trash-can text-xs"></i> Définitif
                        </button>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php endif; ?>

<!-- ============================== MODALES SUPPRESSION DÉFINITIVE ============================== -->
<?php foreach ($items as $item): ?>
<div id="def-modal-<?= e($item['entite']) ?>-<?= $item['id'] ?>" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/50" onclick="closeModal('def-modal-<?= e($item['entite']) ?>-<?= $item['id'] ?>')"></div>
    <div class="relative bg-white rounded-2xl shadow-xl max-w-md w-full p-6">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-red-50 flex items-center justify-center text-red-500 text-xl shrink-0">
                <i class="fa-solid fa-trash-can"></i>
            </div>
            <div>
                <h3 class="font-bold text-gray-900">Suppression définitive ?</h3>
                <p class="text-sm text-gray-500 mt-1">
                    « <?= e($item['nom']) ?> » sera supprimée <strong>définitivement</strong>. Cette action est irréversible.
                </p>
            </div>
        </div>
        <div class="mt-6 flex items-center justify-end gap-3">
            <button type="button" onclick="closeModal('def-modal-<?= e($item['entite']) ?>-<?= $item['id'] ?>')"
                    class="px-4 py-2 rounded-lg text-sm font-medium text-gray-600 border border-gray-200 hover:bg-gray-50 transition">
                Annuler
            </button>
            <form method="post" action="<?= $lienBase ?>/<?= e($item['entite']) ?>/<?= $item['id'] ?>/supprimer-definitif">
                <input type="hidden" name="_token" value="<?= csrf() ?>">
                <button type="submit"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-white bg-red-500 hover:bg-red-600 transition">
                    <i class="fa-solid fa-trash-can text-xs"></i> Supprimer définitivement
                </button>
            </form>
        </div>
    </div>
</div>
<?php endforeach; ?>