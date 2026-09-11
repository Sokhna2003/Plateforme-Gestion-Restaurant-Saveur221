<?php

declare(strict_types=1);

/**
 * @var \App\Models\Client|\App\Models\Utilisateur|array<string, mixed> $profil
 * @var array<string, string> $old
 * @var array<string, string> $errors
 * @var bool $estClient
 * @var string $retour
 * @var string $base
 */

$old = $old ?? [];
$errors = $errors ?? [];
$profil = $profil ?? [];
$estClient = $estClient ?? false;
$retour = $retour ?? ($estClient ? '/client' : '');

$vPrenom = $old['prenom'] ?? ($profil['prenom'] ?? '');
$vNom = $old['nom'] ?? ($profil['nom'] ?? '');
$vEmail = $old['email'] ?? ($profil['email'] ?? '');
$vTelephone = $old['telephone'] ?? ($profil['telephone'] ?? '');
$vAdresse = $old['adresse'] ?? ($profil['adresse'] ?? '');
$photo = $profil['photo'] ?? null;
$nomComplet = trim(($profil['prenom'] ?? '') . ' ' . ($profil['nom'] ?? ''));
$initiales = mb_strtoupper(mb_substr((string) ($profil['prenom'] ?? ''), 0, 1))
    . mb_strtoupper(mb_substr((string) ($profil['nom'] ?? ''), 0, 1));

$lienPhoto = null;
if (is_string($photo) && $photo !== '') {
    $lienPhoto = str_starts_with($photo, 'http') ? $photo : $base . $photo;
}
?>

<div class="max-w-4xl mx-auto">
    <div class="flex items-center gap-4 mb-8">
        <div id="apercu-avatar" class="w-16 h-16 rounded-full bg-brand-orange/10 flex items-center justify-center text-brand-orange font-bold text-xl shrink-0 overflow-hidden">
            <?php if ($lienPhoto !== null): ?>
                <img src="<?= e($lienPhoto) ?>" alt="Photo de profil" class="w-full h-full object-cover">
            <?php else: ?>
                <?= e($initiales !== '' ? $initiales : '?') ?>
            <?php endif; ?>
        </div>
        <div>
            <h2 class="text-2xl font-bold text-gray-900"><?= e($nomComplet !== '' ? $nomComplet : 'Mon profil') ?></h2>
            <p class="text-sm text-gray-500"><?= e((string) $vEmail) ?></p>
        </div>
    </div>

    <form method="post" action="<?= $base ?>/profil" enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <input type="hidden" name="_token" value="<?= csrf() ?>">

        <!-- Photo de profil -->
        <div class="lg:col-span-1">
            <div class="bg-white border border-gray-200 rounded-xl p-6 sticky top-24">
                <label class="block text-sm font-medium text-gray-900 mb-4">Photo de profil</label>
                <div class="flex flex-col items-center gap-4">
                    <div id="apercu-photo" class="w-28 h-28 rounded-full bg-brand-orange/10 flex items-center justify-center text-brand-orange font-bold text-2xl overflow-hidden">
                        <?php if ($lienPhoto !== null): ?>
                            <img src="<?= e($lienPhoto) ?>" alt="Photo de profil" class="w-full h-full object-cover">
                        <?php else: ?>
                            <?= e($initiales !== '' ? $initiales : '?') ?>
                        <?php endif; ?>
                    </div>
                    <p class="text-xs text-gray-500 text-center">JPG, PNG, GIF ou WebP — 2 Mo maximum.</p>
                    <label for="photo"
                           class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-brand-orange border border-brand-orange/30 bg-brand-orange/5 hover:bg-brand-orange hover:text-white transition cursor-pointer">
                        <i class="fa-solid fa-camera text-sm"></i> Choisir une photo
                        <input type="file" id="photo" name="photo" accept="image/jpeg,image/png,image/gif,image/webp" class="hidden">
                    </label>
                    <?php if ($errors['photo'] ?? false): ?>
                        <p class="text-xs text-red-600 text-center"><?= e($errors['photo']) ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Informations -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white border border-gray-200 rounded-xl p-6">
                <h3 class="font-bold text-gray-900 mb-5">Mes informations</h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="p-prenom" class="block text-sm font-medium text-gray-900 mb-1">Prénom <span class="text-red-500">*</span></label>
                        <input type="text" id="p-prenom" name="prenom" value="<?= e((string) $vPrenom) ?>"
                               placeholder="Ex : Awa"
                               class="w-full border <?= isset($errors['prenom']) ? 'border-red-400 bg-red-50' : 'border-gray-200' ?> rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-brand-orange">
                        <?php if ($errors['prenom'] ?? false): ?>
                            <p class="mt-1 text-xs text-red-600"><?= e($errors['prenom']) ?></p>
                        <?php endif; ?>
                    </div>

                    <div>
                        <label for="p-nom" class="block text-sm font-medium text-gray-900 mb-1">Nom <span class="text-red-500">*</span></label>
                        <input type="text" id="p-nom" name="nom" value="<?= e((string) $vNom) ?>"
                               placeholder="Ex : Diop"
                               class="w-full border <?= isset($errors['nom']) ? 'border-red-400 bg-red-50' : 'border-gray-200' ?> rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-brand-orange">
                        <?php if ($errors['nom'] ?? false): ?>
                            <p class="mt-1 text-xs text-red-600"><?= e($errors['nom']) ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="mt-4">
                    <label for="p-email" class="block text-sm font-medium text-gray-900 mb-1">Adresse email <span class="text-red-500">*</span></label>
                    <input type="email" id="p-email" name="email" value="<?= e((string) $vEmail) ?>"
                           placeholder="exemple@saveur221.sn"
                           class="w-full border <?= isset($errors['email']) ? 'border-red-400 bg-red-50' : 'border-gray-200' ?> rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-brand-orange">
                    <?php if ($errors['email'] ?? false): ?>
                        <p class="mt-1 text-xs text-red-600"><?= e($errors['email']) ?></p>
                    <?php endif; ?>
                </div>

                <?php if ($estClient): ?>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                        <div>
                            <label for="p-telephone" class="block text-sm font-medium text-gray-900 mb-1">Téléphone <span class="text-red-500">*</span></label>
                            <input type="tel" id="p-telephone" name="telephone" value="<?= e((string) $vTelephone) ?>"
                                   placeholder="+221 77 000 00 00"
                                   class="w-full border <?= isset($errors['telephone']) ? 'border-red-400 bg-red-50' : 'border-gray-200' ?> rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-brand-orange">
                            <?php if ($errors['telephone'] ?? false): ?>
                                <p class="mt-1 text-xs text-red-600"><?= e($errors['telephone']) ?></p>
                            <?php endif; ?>
                        </div>

                        <div>
                            <label for="p-adresse" class="block text-sm font-medium text-gray-900 mb-1">Adresse <span class="text-red-500">*</span></label>
                            <input type="text" id="p-adresse" name="adresse" value="<?= e((string) $vAdresse) ?>"
                                   placeholder="Ex : Dakar, Sacré-Cœur"
                                   class="w-full border <?= isset($errors['adresse']) ? 'border-red-400 bg-red-50' : 'border-gray-200' ?> rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-brand-orange">
                            <?php if ($errors['adresse'] ?? false): ?>
                                <p class="mt-1 text-xs text-red-600"><?= e($errors['adresse']) ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl p-6">
                <h3 class="font-bold text-gray-900 mb-1">Changer le mot de passe</h3>
                <p class="text-xs text-gray-500 mb-5">Laissez vide pour conserver le mot de passe actuel.</p>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="p-mdp" class="block text-sm font-medium text-gray-900 mb-1">Nouveau mot de passe</label>
                        <input type="password" id="p-mdp" name="mot_de_passe" autocomplete="new-password"
                               placeholder="Au moins 6 caractères"
                               class="w-full border <?= isset($errors['mot_de_passe']) ? 'border-red-400 bg-red-50' : 'border-gray-200' ?> rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-brand-orange">
                        <?php if ($errors['mot_de_passe'] ?? false): ?>
                            <p class="mt-1 text-xs text-red-600"><?= e($errors['mot_de_passe']) ?></p>
                        <?php endif; ?>
                    </div>

                    <div>
                        <label for="p-mdp-conf" class="block text-sm font-medium text-gray-900 mb-1">Confirmation</label>
                        <input type="password" id="p-mdp-conf" name="mot_de_passe_confirmation" autocomplete="new-password"
                               placeholder="Confirmez le mot de passe"
                               class="w-full border <?= isset($errors['mot_de_passe_confirmation']) ? 'border-red-400 bg-red-50' : 'border-gray-200' ?> rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-brand-orange">
                        <?php if ($errors['mot_de_passe_confirmation'] ?? false): ?>
                            <p class="mt-1 text-xs text-red-600"><?= e($errors['mot_de_passe_confirmation']) ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3">
                <a href="<?= $base . e($retour) ?>"
                   class="px-5 py-2.5 rounded-lg text-sm font-medium text-gray-600 border border-gray-200 hover:bg-gray-50 transition">
                    Annuler
                </a>
                <button type="submit"
                        class="inline-flex items-center gap-2 bg-brand-orange text-white px-6 py-2.5 rounded-lg text-sm font-semibold hover:opacity-90 transition">
                    <i class="fa-solid fa-check"></i> Enregistrer les modifications
                </button>
            </div>
        </div>
    </form>
</div>

<script>
    const inputPhoto = document.getElementById('photo');
    if (inputPhoto) {
        inputPhoto.addEventListener('change', function () {
            const fichier = this.files && this.files[0];
            if (!fichier) return;
            const lecteur = new FileReader();
            lecteur.onload = function (evt) {
                const source = String(evt.target.result);
                ['apercu-photo', 'apercu-avatar'].forEach(function (id) {
                    const el = document.getElementById(id);
                    if (el) el.innerHTML = '<img src="' + source + '" alt="Photo de profil" class="w-full h-full object-cover">';
                });
            };
            lecteur.readAsDataURL(fichier);
        });
    }
</script>