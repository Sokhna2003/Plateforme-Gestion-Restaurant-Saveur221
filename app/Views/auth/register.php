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

<form method="post" action="<?= $base ?>/inscription" class="space-y-4" id="registerForm" novalidate>
    <input type="hidden" name="_token" value="<?= csrf() ?>">

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label for="nom" class="block text-sm font-medium mb-1">Nom <span class="text-red-500">*</span></label>
            <input type="text" id="nom" name="nom"
                   value="<?= e($old['nom'] ?? '') ?>"
                   class="w-full border border-brand-brown/20 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-brand-orange focus:ring-1 focus:ring-brand-orange">
            <p class="mt-1 text-xs text-red-600 hidden" id="nom-error"></p>
        </div>
        <div>
            <label for="prenom" class="block text-sm font-medium mb-1">Prénom <span class="text-red-500">*</span></label>
            <input type="text" id="prenom" name="prenom"
                   value="<?= e($old['prenom'] ?? '') ?>"
                   class="w-full border border-brand-brown/20 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-brand-orange focus:ring-1 focus:ring-brand-orange">
            <p class="mt-1 text-xs text-red-600 hidden" id="prenom-error"></p>
        </div>
    </div>

    <div>
        <label for="email" class="block text-sm font-medium mb-1">Email <span class="text-red-500">*</span></label>
        <input type="text" id="email" name="email"
               value="<?= e($old['email'] ?? '') ?>"
               placeholder="votre@email.com"
               class="w-full border border-brand-brown/20 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-brand-orange focus:ring-1 focus:ring-brand-orange">
        <p class="mt-1 text-xs text-red-600 hidden" id="email-error"></p>
    </div>

    <div>
        <label for="telephone" class="block text-sm font-medium mb-1">Téléphone <span class="text-red-500">*</span></label>
        <input type="text" id="telephone" name="telephone"
               value="<?= e($old['telephone'] ?? '') ?>"
               placeholder="+221 77 000 00 00"
               class="w-full border border-brand-brown/20 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-brand-orange focus:ring-1 focus:ring-brand-orange">
        <p class="mt-1 text-xs text-red-600 hidden" id="telephone-error"></p>
    </div>

    <div>
        <label for="adresse" class="block text-sm font-medium mb-1">Adresse <span class="text-red-500">*</span></label>
        <input type="text" id="adresse" name="adresse"
               value="<?= e($old['adresse'] ?? '') ?>"
               placeholder="Votre adresse"
               class="w-full border border-brand-brown/20 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-brand-orange focus:ring-1 focus:ring-brand-orange">
        <p class="mt-1 text-xs text-red-600 hidden" id="adresse-error"></p>
    </div>

    <div>
        <label for="mot_de_passe" class="block text-sm font-medium mb-1">Mot de passe <span class="text-red-500">*</span></label>
        <input type="password" id="mot_de_passe" name="mot_de_passe"
               placeholder="Minimum 6 caractères"
               class="w-full border border-brand-brown/20 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-brand-orange focus:ring-1 focus:ring-brand-orange">
        <p class="mt-1 text-xs text-red-600 hidden" id="mot_de_passe-error"></p>
    </div>

    <div>
        <label for="mot_de_passe_confirmation" class="block text-sm font-medium mb-1">Confirmer le mot de passe <span class="text-red-500">*</span></label>
        <input type="password" id="mot_de_passe_confirmation" name="mot_de_passe_confirmation"
               placeholder="Retapez votre mot de passe"
               class="w-full border border-brand-brown/20 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-brand-orange focus:ring-1 focus:ring-brand-orange">
        <p class="mt-1 text-xs text-red-600 hidden" id="mot_de_passe_confirmation-error"></p>
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

<script>
document.getElementById('registerForm').addEventListener('submit', function(e) {
    let valide = true;

    const champs = [
        { id: 'nom', label: 'Nom' },
        { id: 'prenom', label: 'Prénom' },
        { id: 'email', label: 'Email' },
        { id: 'telephone', label: 'Téléphone' },
        { id: 'adresse', label: 'Adresse' },
        { id: 'mot_de_passe', label: 'Mot de passe' },
        { id: 'mot_de_passe_confirmation', label: 'Confirmation du mot de passe' }
    ];

    // Reset tous les styles
    champs.forEach(function(c) {
        const el = document.getElementById(c.id);
        const err = document.getElementById(c.id + '-error');
        el.classList.remove('border-red-400');
        el.classList.add('border-brand-brown/20');
        err.classList.add('hidden');
        err.textContent = '';
    });

    // Validation chaque champ
    champs.forEach(function(c) {
        const el = document.getElementById(c.id);
        const err = document.getElementById(c.id + '-error');
        const val = el.value.trim();

        if (val === '') {
            el.classList.remove('border-brand-brown/20');
            el.classList.add('border-red-400');
            err.textContent = 'Ce champ est obligatoire.';
            err.classList.remove('hidden');
            valide = false;
        } else if (c.id === 'email' && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val)) {
            el.classList.remove('border-brand-brown/20');
            el.classList.add('border-red-400');
            err.textContent = 'Le format de l\'email est invalide.';
            err.classList.remove('hidden');
            valide = false;
        }
    });

    // Mot de passe min 6 car
    const mdp = document.getElementById('mot_de_passe');
    const mdpErr = document.getElementById('mot_de_passe-error');
    if (mdp.value.trim() !== '' && mdp.value.trim().length < 6) {
        mdp.classList.remove('border-brand-brown/20');
        mdp.classList.add('border-red-400');
        mdpErr.textContent = 'Le mot de passe doit contenir au moins 6 caractères.';
        mdpErr.classList.remove('hidden');
        valide = false;
    }

    // Confirmation
    const mdpConf = document.getElementById('mot_de_passe_confirmation');
    const mdpConfErr = document.getElementById('mot_de_passe_confirmation-error');
    if (mdp.value.trim() !== '' && mdpConf.value.trim() !== mdp.value.trim()) {
        mdpConf.classList.remove('border-brand-brown/20');
        mdpConf.classList.add('border-red-400');
        mdpConfErr.textContent = 'Les mots de passe ne correspondent pas.';
        mdpConfErr.classList.remove('hidden');
        valide = false;
    }

    if (!valide) e.preventDefault();
});
</script>
