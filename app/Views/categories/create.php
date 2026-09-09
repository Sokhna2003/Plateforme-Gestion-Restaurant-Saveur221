<?php

declare(strict_types=1);

/**
 * @var string $baseRoute
 */

$errors = $errors ?? [];
$old = $old ?? [];
?>
<div class="max-w-2xl mx-auto">
    <div class="mb-6 text-center">
        <a href="<?= $base . $baseRoute ?>/categories" class="text-sm text-gray-500 hover:text-brand-orange transition inline-flex items-center gap-2">
            <i class="fa-solid fa-arrow-left text-xs"></i> Retour aux catégories
        </a>
        <h2 class="text-2xl font-bold text-gray-900 mt-3">Ajouter une catégorie</h2>
        <p class="text-sm text-gray-500 mt-1">Ajoutez une nouvelle catégorie à votre menu avec son image.</p>
    </div>

    <form method="post" action="<?= $base . $baseRoute ?>/categories/creer"
          enctype="multipart/form-data"
          class="bg-white border border-gray-200 rounded-xl p-6 space-y-5">
        <input type="hidden" name="_token" value="<?= csrf() ?>">

        <div>
            <label for="nom" class="block text-sm font-medium text-gray-900 mb-1">Nom de la catégorie <span class="text-red-500">*</span></label>
            <input type="text" id="nom" name="nom"
                   value="<?= e($old['nom'] ?? '') ?>"
                   placeholder="Ex : Plats, Boissons, Desserts..."
                   class="w-full border <?= isset($errors['nom']) ? 'border-red-400 bg-red-50' : 'border-gray-200' ?> rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-brand-orange">
            <?php if ($errors['nom'] ?? false): ?>
                <p class="mt-1 text-xs text-red-600"><i class="fa-solid fa-circle-exclamation text-[10px] mr-1"></i><?= e($errors['nom']) ?></p>
            <?php endif; ?>
        </div>

        <div>
            <label for="image" class="block text-sm font-medium text-gray-900 mb-1">Image de la catégorie <span class="text-red-500">*</span></label>
            <div id="apercu-image" class="hidden mb-3">
                <img id="apercu-image-src" src="" alt="Aperçu de l'image choisie"
                     class="w-32 h-32 rounded-lg object-cover border border-gray-200 shadow-sm">
                <p class="mt-1 text-xs text-gray-500" id="image-nom"></p>
            </div>
            <label for="image"
                   class="flex flex-col items-center justify-center w-full border-2 border-dashed <?= isset($errors['image']) ? 'border-red-400 bg-red-50' : 'border-gray-200' ?> rounded-lg px-4 py-8 cursor-pointer hover:border-brand-orange hover:bg-brand-orange/5 transition text-center">
                <i class="fa-solid fa-cloud-arrow-up text-2xl text-gray-300"></i>
                <span class="mt-2 text-sm text-gray-500">Cliquez pour choisir une image</span>
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
                      class="w-full border <?= isset($errors['description']) ? 'border-red-400 bg-red-50' : 'border-gray-200' ?> rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-brand-orange resize-none"><?= e($old['description'] ?? '') ?></textarea>
            <?php if ($errors['description'] ?? false): ?>
                <p class="mt-1 text-xs text-red-600"><i class="fa-solid fa-circle-exclamation text-[10px] mr-1"></i><?= e($errors['description']) ?></p>
            <?php endif; ?>
        </div>

        <div class="flex items-center justify-center gap-3 pt-2">
            <button type="submit"
                    class="inline-flex items-center gap-2 bg-[#D95F02] text-white px-8 py-2.5 rounded-lg text-sm font-medium hover:opacity-90 transition">
                <i class="fa-solid fa-check"></i> Enregistrer
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
    const apercu = document.getElementById('apercu-image');
    const apercuSrc = document.getElementById('apercu-image-src');
    const imageNom = document.getElementById('image-nom');

    imageInput.addEventListener('change', function () {
        const fichier = this.files && this.files[0];
        if (!fichier) {
            apercu.classList.add('hidden');
            imageNom.textContent = '';
            return;
        }
        imageNom.textContent = fichier.name;
        const lecteur = new FileReader();
        lecteur.onload = function (e) {
            apercuSrc.src = e.target.result;
            apercu.classList.remove('hidden');
        };
        lecteur.readAsDataURL(fichier);
    });
</script>