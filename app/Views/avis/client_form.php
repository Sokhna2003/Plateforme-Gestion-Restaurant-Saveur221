<?php
$noteDefaut = 5;
$labelsNote = [
    1 => 'Très déçu',
    2 => 'Déçu',
    3 => 'Correct',
    4 => 'Bien',
    5 => 'Excellent !',
];
?>

<div class="mb-8">
    <h1 class="font-serif text-3xl font-bold mb-2">Laisser un avis</h1>
    <p class="text-brand-brown/60">Commande n°<?= (int) $commande->id ?> — passée le <?= e($commande->dateFormatee()) ?></p>
    <span class="block w-16 h-1 bg-brand-orange mt-2"></span>
</div>

<div class="max-w-xl">
    <div class="bg-white rounded-2xl shadow-sm border border-brand-brown/10 p-8">

        <form method="post" action="<?= $base ?>/client/commandes/<?= (int) $commande->id ?>/avis" class="space-y-6" novalidate>
            <input type="hidden" name="_token" value="<?= csrf() ?>">
            <input type="hidden" name="note" id="note-input" value="<?= $noteDefaut ?>">

            <div>
                <label class="block text-sm font-medium mb-3">Votre note <span class="text-red-500">*</span></label>
                <div id="star-row" class="flex items-center gap-2 cursor-pointer select-none text-4xl">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <span data-valeur="<?= $i ?>" class="transition-colors <?= $i <= $noteDefaut ? 'text-amber-400' : 'text-brand-brown/20 hover:text-amber-300' ?>">★</span>
                    <?php endfor; ?>
                </div>
                <p class="mt-2 text-sm text-brand-brown/60" id="note-label"><?= $labelsNote[$noteDefaut] ?></p>
            </div>

            <div>
                <label for="commentaire" class="block text-sm font-medium mb-1">Commentaire <span class="text-brand-brown/40">(optionnel)</span></label>
                <textarea id="commentaire" name="commentaire" rows="4"
                          placeholder="Décrivez votre expérience…"
                          maxlength="500"
                          class="w-full border border-brand-brown/20 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-brand-orange focus:ring-1 focus:ring-brand-orange"></textarea>
            </div>

            <div class="flex items-center gap-4">
                <button type="submit"
                        class="bg-brand-orange text-white px-8 py-3 rounded-full font-medium hover:opacity-90 transition">
                    Publier mon avis
                </button>
                <a href="<?= $base ?>/client/commandes/<?= (int) $commande->id ?>"
                   class="border border-brand-brown/20 text-brand-brown px-8 py-3 rounded-full font-medium hover:border-brand-orange hover:text-brand-orange transition">
                    Annuler
                </a>
            </div>
        </form>
    </div>
</div>

<script>
(function () {
    const NOTE_LABELS = {
        1: 'Très déçu',
        2: 'Déçu',
        3: 'Correct',
        4: 'Bien',
        5: 'Excellent !'
    };
    const row = document.getElementById('star-row');
    const input = document.getElementById('note-input');
    const label = document.getElementById('note-label');
    const stars = row.querySelectorAll('[data-valeur]');

    function appliquer(valeur) {
        stars.forEach(function (s) {
            s.classList.toggle('text-amber-400', parseInt(s.dataset.valeur, 10) <= valeur);
            s.classList.toggle('text-brand-brown/20', parseInt(s.dataset.valeur, 10) > valeur);
        });
        label.textContent = NOTE_LABELS[valeur] || '';
        input.value = valeur;
    }

    row.addEventListener('click', function (e) {
        const etoile = e.target.closest('[data-valeur]');
        if (!etoile) return;
        appliquer(parseInt(etoile.dataset.valeur, 10));
    });

    row.addEventListener('mousemove', function (e) {
        const etoile = e.target.closest('[data-valeur]');
        if (!etoile) return;
        const val = parseInt(etoile.dataset.valeur, 10);
        stars.forEach(function (s) {
            s.classList.toggle('text-amber-400', parseInt(s.dataset.valeur, 10) <= val);
            s.classList.toggle('text-brand-brown/20', parseInt(s.dataset.valeur, 10) > val);
        });
        label.textContent = NOTE_LABELS[val] || '';
    });

    row.addEventListener('mouseleave', function () {
        appliquer(parseInt(input.value, 10) || 5);
    });
})();
</script>
