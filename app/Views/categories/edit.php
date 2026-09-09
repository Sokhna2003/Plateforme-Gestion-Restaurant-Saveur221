<?php

declare(strict_types=1);

/**
 * @var \App\Models\Categorie $categorie
 * @var string $baseRoute
 * @var array $old
 * @var array $errors
 */

$errors = $errors ?? [];
$old = $old ?? [];
$vNom = $old['nom'] ?? $categorie->nom;
$vDescription = $old['description'] ?? ($categorie->description ?? '');
?>
<div class="max-w-2xl mx-auto">
    <div class="mb-6 text-center">
        <a href="<?= $base . $baseRoute ?>/categories" class="text-sm text-gray-500 hover:text-brand-orange transition inline-flex items-center gap-2">
            <i class="fa-solid fa-arrow-left text-xs"></i> Retour aux catégories
        </a>
        <h2 class="text-2xl font-bold text-gray-900 mt-3">Modifier la catégorie</h2>
        <p class="text-sm text-gray-500 mt-1">Mettez à jour les informations de « <?= e($categorie->nom) ?> ».</p>
    </div>

    <form method="post" action="<?= $base . $baseRoute ?>/categories/<?= $categorie->id ?>/modifier"
          enctype="multipart/form-data"
          class="bg-white border border-gray-200 rounded-xl p-6 space-y-5">
        <input type="hidden" name="_token" value="<?= csrf() ?>">
        <input type="hidden" name="image_actuelle" value="<?= e($categorie->image ?? '') ?>">

        <div>
            <label for="nom" class="block text-sm font-medium text-gray-900 mb-1">Nom de la catégorie <span class="text-red-500">*</span></label>
            <input type="text" id="nom" name="nom"
                   value="<?= e($vNom) ?>"
                   placeholder="Ex : Plats, Boissons, Desserts..."
                   class="w-full border <?= isset($errors['nom']) ? 'border-red-400 bg-red-50' : 'border-gray-200' ?> rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-brand-orange">
            <?php if ($errors['nom'] ?? false): ?>
                <p class="mt-1 text-xs text-red-600"><i class="fa-solid fa-circle-exclamation text-[10px] mr-1"></i><?= e($errors['nom']) ?></p>
            <?php endif; ?>
        </div>

        <div>
            <label for="image" class="block text-sm font-medium text-gray-900 mb-1">Image de la catégorie</label>

            <?php if ($categorie->image): ?>
                <div class="mb-3 flex items-center gap-3">
                    <img id="apercu-image-src" src="<?= e($categorie->image) ?>" alt="<?= e($categorie->nom) ?>" class="w-20 h-20 rounded-lg object-cover border border-gray-200">
                    <div class="text-xs text-gray-500" id="image-etat">
                        <p>Image actuelle : <span class="font-mono break-all"><?= e(basename(parse_url($categorie->image, PHP_URL_PATH) ?? $categorie->image)) ?></span></p>
                        <p class="mt-1">Choisissez un nouveau fichier pour la remplacer (optionnel).</p>
                    </div>
                </div>
            <?php else: ?>
                <div id="apercu-image" class="hidden mb-3">
                    <img id="apercu-image-src" src="" alt="Aperçu de l'image choisie"
                         class="w-32 h-32 rounded-lg object-cover border border-gray-200 shadow-sm">
                    <p class="mt-1 text-xs text-gray-500" id="image-nom"></p>
                </div>
            <?php endif; ?>

            <label for="image"
                   class="flex flex-col items-center justify-center w-full border-2 border-dashed <?= isset($errors['image']) ? 'border-red-400 bg-red-50' : 'border-gray-200' ?> rounded-lg px-4 py-8 cursor-pointer hover:border-brand-orange hover:bg-brand-orange/5 transition text-center">
                <i class="fa-solid fa-cloud-arrow-up text-2xl text-gray-300"></i>
                <span class="mt-2 text-sm text-gray-500"><?= $categorie->image ? 'Cliquez pour changer d\'image' : 'Cliquez pour ajouter une image' ?></span>
                <span class="mt-1 text-xs text-gray-400">JPG, PNG, GIF ou WebP — 2 Mo maximum. Stockée sur Cloudinary.</span>
            </label>
            <input type="file" id="image" name="image" accept="image/*" class="hidden">
            <?php if ($errors['image'] ?? false): ?>
                <p class="mt-1 text-xs text-red-600"><i class="fa-solid fa-circle-exclamation text-[10px] mr-1"></i><?= e($errors['image']) ?></p>
            <?php endif; ?>
        </div>

        <div>
            <label for="description" class="block text-sm font-medium text-gray-900 mb-1">Description</label>
            <textarea id="description" name="description" rows="4"
                      placeholder="Décrivez brièvement cette catégorie..."
                      class="w-full border <?= isset($errors['description']) ? 'border-red-400 bg-red-50' : 'border-gray-200' ?> rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-brand-orange resize-none"><?= e($vDescription) ?></textarea>
            <?php if ($errors['description'] ?? false): ?>
                <p class="mt-1 text-xs text-red-600"><i class="fa-solid fa-circle-exclamation text-[10px] mr-1"></i><?= e($errors['description']) ?></p>
            <?php endif; ?>
        </div>

        <div class="flex items-center justify-center gap-3 pt-2">
            <button type="submit"
                    class="inline-flex items-center gap-2 bg-[#D95F02] text-white px-8 py-2.5 rounded-lg text-sm font-medium hover:opacity-90 transition">
                <i class="fa-solid fa-check"></i> Enregistrer les modifications
            </button>
            <a href="<?= $base . $baseRoute ?>/categories"
               class="inline-flex items-center gap-2 px-8 py-2.5 rounded-lg text-sm font-medium text-gray-500 border border-gray-200 hover:bg-gray-50 transition">
                Annuler
            </a>
        </div>
    </form>
</div>

<script>
    const imageInput = document.getElementById('image');
    const apercuSrc = document.getElementById('apercu-image-src');
    const apercuBoite = document.getElementById('apercu-image');
    const imageEtat = document.getElementById('image-etat');

    imageInput.addEventListener('change', function () {
        const fichier = this.files && this.files[0];
        if (!fichier) {
            if (apercuBoite) apercuBoite.classList.add('hidden');
            if (imageEtat) imageEtat.innerHTML = '<p class="mt-1">Choisissez un nouveau fichier pour la remplacer (optionnel).</p>';
            return;
        }
        if (apercuBoite) apercuBoite.classList.remove('hidden');
        if (imageEtat) imageEtat.innerHTML = '<p class="mt-1">Nouvelle image : <span class="font-mono break-all">' + fichier.name + '</span></p>';
        const lecteur = new FileReader();
        lecteur.onload = function (e) {
            apercuSrc.src = e.target.result;
        };
        lecteur.readAsDataURL(fichier);
    });
</script>