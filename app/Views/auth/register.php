<?php

declare(strict_types=1);

$errors = $errors ?? [];
$old = $old ?? [];
?>
<div class="mb-8">
    <a href="<?= $base ?>/" class="flex items-center gap-2 mb-6">
        <span class="w-8 h-8 rounded-full bg-brand-orange text-white flex items-center justify-center font-serif font-bold text-sm">S</span>
        <span class="font-serif font-bold text-lg">SAVEUR 221</span>
    </a>
    <h1 class="font-serif text-3xl font-bold mb-2">Inscription</h1>
    <p class="text-brand-brown/60">Créez votre compte pour commander.</p>
</div>

<form method="post" action="<?= $base ?>/inscription" class="space-y-4">
    <input type="hidden" name="_token" value="<?= csrf() ?>">

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label for="nom" class="block text-sm font-medium mb-1">Nom <span class="text-red-500">*</span></label>
            <input type="text" id="nom" name="nom" required
                   value="<?= e($old['nom'] ?? '') ?>"
                   class="w-full border <?= isset($errors['nom']) ? 'border-red-400' : 'border-brand-brown/20' ?> rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-brand-orange focus:ring-1 focus:ring-brand-orange">
            <?php if (isset($errors['nom']) && $errors['nom'] !== ''): ?>
                <p class="mt-1 text-xs text-red-600"><?= e($errors['nom']) ?></p>
            <?php endif; ?>
        </div>
        <div>
            <label for="prenom" class="block text-sm font-medium mb-1">Prénom <span class="text-red-500">*</span></label>
            <input type="text" id="prenom" name="prenom" required
                   value="<?= e($old['prenom'] ?? '') ?>"
                   class="w-full border <?= isset($errors['prenom']) ? 'border-red-400' : 'border-brand-brown/20' ?> rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-brand-orange focus:ring-1 focus:ring-brand-orange">
            <?php if (isset($errors['prenom']) && $errors['prenom'] !== ''): ?>
                <p class="mt-1 text-xs text-red-600"><?= e($errors['prenom']) ?></p>
            <?php endif; ?>
        </div>
    </div>

    <div>
        <label for="email" class="block text-sm font-medium mb-1">Email <span class="text-red-500">*</span></label>
        <input type="email" id="email" name="email" required
               value="<?= e($old['email'] ?? '') ?>"
               placeholder="votre@email.com"
               class="w-full border <?= isset($errors['email']) ? 'border-red-400' : 'border-brand-brown/20' ?> rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-brand-orange focus:ring-1 focus:ring-brand-orange">
        <?php if (isset($errors['email']) && $errors['email'] !== ''): ?>
            <p class="mt-1 text-xs text-red-600"><?= e($errors['email']) ?></p>
        <?php endif; ?>
    </div>

    <div>
        <label for="telephone" class="block text-sm font-medium mb-1">Téléphone <span class="text-red-500">*</span></label>
        <input type="text" id="telephone" name="telephone" required
               value="<?= e($old['telephone'] ?? '') ?>"
               placeholder="+221 77 000 00 00"
               class="w-full border <?= isset($errors['telephone']) ? 'border-red-400' : 'border-brand-brown/20' ?> rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-brand-orange focus:ring-1 focus:ring-brand-orange">
        <?php if (isset($errors['telephone']) && $errors['telephone'] !== ''): ?>
            <p class="mt-1 text-xs text-red-600"><?= e($errors['telephone']) ?></p>
        <?php endif; ?>
    </div>

    <div>
        <label for="adresse" class="block text-sm font-medium mb-1">Adresse <span class="text-red-500">*</span></label>
        <input type="text" id="adresse" name="adresse" required
               value="<?= e($old['adresse'] ?? '') ?>"
               placeholder="Votre adresse"
               class="w-full border <?= isset($errors['adresse']) ? 'border-red-400' : 'border-brand-brown/20' ?> rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-brand-orange focus:ring-1 focus:ring-brand-orange">
        <?php if (isset($errors['adresse']) && $errors['adresse'] !== ''): ?>
            <p class="mt-1 text-xs text-red-600"><?= e($errors['adresse']) ?></p>
        <?php endif; ?>
    </div>

    <div>
        <label for="mot_de_passe" class="block text-sm font-medium mb-1">Mot de passe <span class="text-red-500">*</span></label>
        <input type="password" id="mot_de_passe" name="mot_de_passe" required minlength="6"
               placeholder="Minimum 6 caractères"
               class="w-full border <?= isset($errors['mot_de_passe']) ? 'border-red-400' : 'border-brand-brown/20' ?> rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-brand-orange focus:ring-1 focus:ring-brand-orange">
        <?php if (isset($errors['mot_de_passe']) && $errors['mot_de_passe'] !== ''): ?>
            <p class="mt-1 text-xs text-red-600"><?= e($errors['mot_de_passe']) ?></p>
        <?php endif; ?>
    </div>

    <div>
        <label for="mot_de_passe_confirmation" class="block text-sm font-medium mb-1">Confirmer le mot de passe <span class="text-red-500">*</span></label>
        <input type="password" id="mot_de_passe_confirmation" name="mot_de_passe_confirmation" required
               placeholder="Retapez votre mot de passe"
               class="w-full border <?= isset($errors['mot_de_passe_confirmation']) ? 'border-red-400' : 'border-brand-brown/20' ?> rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-brand-orange focus:ring-1 focus:ring-brand-orange">
        <?php if (isset($errors['mot_de_passe_confirmation']) && $errors['mot_de_passe_confirmation'] !== ''): ?>
            <p class="mt-1 text-xs text-red-600"><?= e($errors['mot_de_passe_confirmation']) ?></p>
        <?php endif; ?>
    </div>

    <button type="submit"
            class="w-full bg-brand-orange text-white py-3 rounded-xl font-medium hover:opacity-90 transition mt-2">
        Créer mon compte
    </button>
</form>

<p class="text-center text-sm text-brand-brown/60 mt-6">
    Déjà un compte ?
    <a href="<?= $base ?>/connexion" class="text-brand-orange font-medium hover:underline">Se connecter</a>
</p>
