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

<form method="post" action="<?= $base ?>/connexion" class="space-y-5" id="loginForm" novalidate>
    <input type="hidden" name="_token" value="<?= csrf() ?>">

    <div>
        <label for="email" class="block text-sm font-medium mb-1">Email <span class="text-red-500">*</span></label>
        <input type="text" id="email" name="email"
               value="<?= e($old['email'] ?? '') ?>"
               placeholder="votre@email.com"
               class="w-full border border-brand-brown/20 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-brand-orange focus:ring-1 focus:ring-brand-orange">
        <p class="mt-1 text-xs text-red-600 hidden" id="email-error"></p>
        <?php if (isset($errors['email']) && $errors['email'] !== ''): ?>
            <p class="mt-1 text-xs text-red-600"><?= e($errors['email']) ?></p>
        <?php endif; ?>
    </div>

    <div>
        <label for="mot_de_passe" class="block text-sm font-medium mb-1">Mot de passe <span class="text-red-500">*</span></label>
        <input type="password" id="mot_de_passe" name="mot_de_passe"
               placeholder="••••••••"
               class="w-full border border-brand-brown/20 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-brand-orange focus:ring-1 focus:ring-brand-orange">
        <p class="mt-1 text-xs text-red-600 hidden" id="mot_de_passe-error"></p>
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

<script>
document.getElementById('loginForm').addEventListener('submit', function(e) {
    let valide = true;

    const email = document.getElementById('email');
    const emailErr = document.getElementById('email-error');
    const mdp = document.getElementById('mot_de_passe');
    const mdpErr = document.getElementById('mot_de_passe-error');

    // Reset
    [email, mdp].forEach(function(el) { el.classList.remove('border-red-400'); el.classList.add('border-brand-brown/20'); });
    [emailErr, mdpErr].forEach(function(el) { el.classList.add('hidden'); el.textContent = ''; });

    // Email vide
    if (email.value.trim() === '') {
        email.classList.remove('border-brand-brown/20');
        email.classList.add('border-red-400');
        emailErr.textContent = 'L\'adresse email est requise.';
        emailErr.classList.remove('hidden');
        valide = false;
    // Email format invalide
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value.trim())) {
        email.classList.remove('border-brand-brown/20');
        email.classList.add('border-red-400');
        emailErr.textContent = 'Le format de l\'email est invalide.';
        emailErr.classList.remove('hidden');
        valide = false;
    }

    // Mot de passe vide
    if (mdp.value === '') {
        mdp.classList.remove('border-brand-brown/20');
        mdp.classList.add('border-red-400');
        mdpErr.textContent = 'Le mot de passe est requis.';
        mdpErr.classList.remove('hidden');
        valide = false;
    }

    if (!e) e.preventDefault();
    if (!valide) e.preventDefault();
});
</script>
