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
    <h1 class="font-serif text-3xl font-bold mb-2">Connexion</h1>
    <p class="text-brand-brown/60">Accédez à votre espace personnel.</p>
</div>

<form method="post" action="<?= $base ?>/connexion" class="space-y-5">
    <input type="hidden" name="_token" value="<?= csrf() ?>">

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
        <label for="mot_de_passe" class="block text-sm font-medium mb-1">Mot de passe <span class="text-red-500">*</span></label>
        <input type="password" id="mot_de_passe" name="mot_de_passe" required
               placeholder="••••••••"
               class="w-full border <?= isset($errors['mot_de_passe']) ? 'border-red-400' : 'border-brand-brown/20' ?> rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-brand-orange focus:ring-1 focus:ring-brand-orange">
        <?php if (isset($errors['mot_de_passe']) && $errors['mot_de_passe'] !== ''): ?>
            <p class="mt-1 text-xs text-red-600"><?= e($errors['mot_de_passe']) ?></p>
        <?php endif; ?>
    </div>

    <button type="submit"
            class="w-full bg-brand-orange text-white py-3 rounded-xl font-medium hover:opacity-90 transition">
        Se connecter
    </button>
</form>

<p class="text-center text-sm text-brand-brown/60 mt-6">
    Pas encore de compte ?
    <a href="<?= $base ?>/inscription" class="text-brand-orange font-medium hover:underline">Créer un compte</a>
</p>
