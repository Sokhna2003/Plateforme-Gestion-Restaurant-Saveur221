<?php

declare(strict_types=1);

/**
 * @var \App\Models\Commande[] $commandes
 * @var int $page
 * @var int $totalPages
 * @var int $total
 * @var string $motCle
 * @var string $statut
 * @var string $vue (liste | carte)
 * @var array{enAttente: int, enPreparation: int, prete: int} $stats
 * @var string $baseRoute (/admin ou /gerant)
 */

use App\Models\Commande;

$lienBase = $base . $baseRoute . '/commandes';
$motCle = $motCle ?? '';
$statut = $statut ?? '';
$vue = $vue ?? 'liste';

$badgeClasses = static function (string $statut): string {
    return match ($statut) {
        Commande::STATUT_EN_ATTENTE => 'bg-amber-100 text-amber-700',
        Commande::STATUT_EN_PREPARATION => 'bg-brand-orange/10 text-brand-orange',
        Commande::STATUT_PRETE => 'bg-blue-100 text-blue-700',
        Commande::STATUT_RETIREE => 'bg-green-100 text-green-700',
        default => 'bg-gray-100 text-gray-500',
    };
};

$formatPrix = static function (float $prix): string {
    return number_format($prix, 0, ',', ' ') . ' FCFA';
};

$renderKebab = static function (Commande $c) use ($lienBase): string {
    $html = '<div class="relative">'
        . '<button type="button" data-kebab-btn onclick="toggleKebab(\'kebab-' . $c->id . '\')"'
        . ' class="w-9 h-9 flex items-center justify-center rounded-lg text-gray-400 hover:bg-gray-100 hover:text-gray-700 transition" aria-label="Actions">'
        . '<i class="fa-solid fa-ellipsis-vertical"></i></button>'
        . '<div id="kebab-' . $c->id . '" class="hidden fixed z-50 w-56 bg-white rounded-xl shadow-xl border border-gray-200 py-2" data-kebab-menu style="left:0;top:0;">'
        . '<a href="' . $lienBase . '/' . $c->id . '" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition">'
        . '<i class="fa-solid fa-eye w-4 text-center text-gray-400"></i> Voir détails</a>';

    if ($c->peutChangerStatut()) {
        $html .= '<button type="button" onclick="openModal(\'statut-modal-' . $c->id . '\')"'
            . ' class="flex items-center gap-3 w-full px-4 py-2.5 text-sm text-left text-gray-700 hover:bg-gray-50 transition">'
            . '<i class="fa-solid fa-arrow-right-arrow-left w-4 text-center text-gray-400"></i> Modifier statut</button>';
    }

    if ($c->peutAnnuler()) {
        $html .= '<button type="button" onclick="openModal(\'annuler-modal-' . $c->id . '\')"'
            . ' class="flex items-center gap-3 w-full px-4 py-2.5 text-sm text-left text-red-600 hover:bg-red-50 transition">'
            . '<i class="fa-solid fa-ban w-4 text-center"></i> Annuler</button>';
    }

    if ($c->peutGenererRecu()) {
        $html .= '<a href="' . $lienBase . '/' . $c->id . '/recu" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition">'
            . '<i class="fa-solid fa-receipt w-4 text-center text-gray-400"></i> Générer le reçu</a>';
    }

    return $html . '</div></div>';
};

$querySansPage = $_GET;
unset($querySansPage['page']);
$qs = http_build_query($querySansPage);
$urlVue = static function (string $v) use ($lienBase, $page, $qs): string {
    $params = $qs !== '' ? $qs . '&' : '';
    return $lienBase . '?' . $params . 'page=' . $page . '&vue=' . $v;
};
?>

<!-- En-tête -->
<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-900">Suivi et Traitement des Commandes</h2>
    <p class="text-sm text-gray-500 mt-1">Gérez les demandes de livraison, de retrait et de service sur table en temps réel.</p>
</div>

<!-- Cartes de progression -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Commandes en attente</p>
                <p class="mt-3 text-3xl font-bold text-gray-900"><?= $stats['enAttente'] ?></p>
            </div>
            <span class="w-10 h-10 rounded-full bg-amber-100 flex items-center justify-center text-amber-600">
                <i class="fa-solid fa-hourglass-half"></i>
            </span>
        </div>
        <div class="mt-4 h-2 rounded-full bg-gray-100 overflow-hidden">
            <div class="h-2 rounded-full bg-amber-400 w-2/5"></div>
        </div>
        <p class="mt-2 text-xs font-medium text-amber-600">40 %</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Commandes en préparation</p>
                <p class="mt-3 text-3xl font-bold text-gray-900"><?= $stats['enPreparation'] ?></p>
            </div>
            <span class="w-10 h-10 rounded-full bg-brand-orange/10 flex items-center justify-center text-brand-orange">
                <i class="fa-solid fa-fire-burner"></i>
            </span>
        </div>
        <div class="mt-4 h-2 rounded-full bg-gray-100 overflow-hidden">
            <div class="h-2 rounded-full bg-brand-orange w-1/2"></div>
        </div>
        <p class="mt-2 text-xs font-medium text-brand-orange">50 %</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Commandes prêtes</p>
                <p class="mt-3 text-3xl font-bold text-gray-900"><?= $stats['prete'] ?></p>
            </div>
            <span class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center text-green-600">
                <i class="fa-solid fa-circle-check"></i>
            </span>
        </div>
        <div class="mt-4 h-2 rounded-full bg-gray-100 overflow-hidden">
            <div class="h-2 rounded-full bg-green-500 w-3/5"></div>
        </div>
        <p class="mt-2 text-xs font-medium text-green-600">60 %</p>
    </div>
</div>

<!-- Recherche + filtre + tableau -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100">
    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between gap-4 flex-wrap">
        <form method="get" action="<?= e($lienBase) ?>" class="flex items-center gap-2 flex-1 min-w-0 flex-wrap">
            <div class="relative flex-1 min-w-[220px]">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                <input type="text" name="q" value="<?= e($motCle) ?>" placeholder="Rechercher par client ou produit..."
                       class="w-full pl-9 pr-3 py-2 rounded-lg border border-gray-200 text-sm bg-gray-50 focus:outline-none focus:border-brand-orange">
            </div>
            <select name="statut"
                    class="text-sm px-3 py-2 rounded-lg border border-gray-200 bg-gray-50 text-gray-700 focus:outline-none focus:border-brand-orange cursor-pointer">
                <option value="">Tous les statuts</option>
                <?php foreach (Commande::LIBELLES as $cle => $libelle): ?>
                    <option value="<?= $cle ?>" <?= $statut === $cle ? 'selected' : '' ?>><?= e($libelle) ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-white bg-brand-orange hover:opacity-90 transition">
                <i class="fa-solid fa-magnifying-glass text-xs"></i> Rechercher
            </button>
        </form>
        <div class="flex items-center gap-2">
            <span class="text-xs text-gray-400 mr-1">Affichage :</span>
            <a href="<?= e($urlVue('liste')) ?>"
               class="w-8 h-8 flex items-center justify-center rounded-lg <?= $vue === 'liste' ? 'bg-brand-orange text-white' : 'text-gray-400 hover:bg-gray-100' ?>"
               title="Vue liste">
                <i class="fa-solid fa-list"></i>
            </a>
            <a href="<?= e($urlVue('carte')) ?>"
               class="w-8 h-8 flex items-center justify-center rounded-lg <?= $vue === 'carte' ? 'bg-brand-orange text-white' : 'text-gray-400 hover:bg-gray-100' ?>"
               title="Vue carte">
                <i class="fa-solid fa-table-cells-large"></i>
            </a>
        </div>
    </div>

    <?php if (!$commandes): ?>
        <div class="py-16 text-center text-sm text-gray-500">
            Aucune commande ne correspond à cette recherche.
        </div>
    <?php elseif ($vue === 'liste'): ?>
        <div class="w-full overflow-hidden">
            <table class="w-full text-[13px]">
                <thead>
                    <tr class="bg-gray-50 text-left text-xs uppercase tracking-wide text-gray-500 whitespace-nowrap">
                        <th class="px-3 py-3 font-medium">Client</th>
                        <th class="px-3 py-3 font-medium">Date</th>
                        <th class="px-3 py-3 font-medium">Produits</th>
                        <th class="px-3 py-3 font-medium">Quantité</th>
                        <th class="px-3 py-3 font-medium">Prix Unitaire</th>
                        <th class="px-3 py-3 font-medium">Montant</th>
                        <th class="px-3 py-3 font-medium">Statut</th>
                        <th class="px-3 py-3 font-medium text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php foreach ($commandes as $c): ?>
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="px-3 py-3">
                                <div class="flex items-center gap-3">
                                    <span class="w-8 h-8 rounded-full bg-brand-orange/10 flex items-center justify-center text-brand-orange text-sm font-bold shrink-0">
                                        <?= strtoupper(mb_substr($c->clientNomComplet(), 0, 1)) ?>
                                    </span>
                                    <p class="font-medium text-gray-900 whitespace-nowrap"><?= e($c->clientNomComplet()) ?></p>
                                </div>
                            </td>
                            <td class="px-3 py-3 text-gray-500 whitespace-nowrap"><?= e($c->dateFormatee()) ?></td>
                            <td class="px-3 py-3 max-w-[180px]">
                                <p class="text-gray-700 truncate" title="<?= e((string) $c->produitsLibelles) ?>">
                                    <?= e((string) $c->produitsLibelles) ?>
                                </p>
                            </td>
                            <td class="px-3 py-3 font-medium text-gray-900 text-center"><?= (int) $c->quantiteTotale ?></td>
                            <td class="px-3 py-3 text-gray-700 whitespace-nowrap"><?= e($formatPrix((float) $c->prixUnitaire)) ?></td>
                            <td class="px-3 py-3 font-bold text-gray-900 whitespace-nowrap"><?= e($c->montantFormate()) ?></td>
                            <td class="px-3 py-3">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium <?= $badgeClasses($c->statut) ?> whitespace-nowrap">
                                    <?= e($c->libelleStatut()) ?>
                                </span>
                            </td>
                            <td class="px-3 py-3">
                                <?= $renderKebab($c) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="p-6 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
            <?php foreach ($commandes as $c): ?>
                <div class="border border-gray-200 rounded-xl p-5 flex flex-col gap-4 transition hover:shadow-md">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <span class="w-10 h-10 rounded-full bg-brand-orange/10 flex items-center justify-center text-brand-orange font-bold shrink-0">
                                <?= strtoupper(mb_substr($c->clientNomComplet(), 0, 1)) ?>
                            </span>
                            <div>
                                <p class="font-medium text-gray-900 text-sm"><?= e($c->clientNomComplet()) ?></p>
                                <p class="text-xs text-gray-500"><?= e($c->dateFormatee()) ?></p>
                            </div>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium <?= $badgeClasses($c->statut) ?> whitespace-nowrap">
                                <?= e($c->libelleStatut()) ?>
                            </span>
                            <?= $renderKebab($c) ?>
                        </div>
                    </div>
                    <p class="text-sm text-gray-700 truncate" title="<?= e((string) $c->produitsLibelles) ?>">
                        <?= e((string) $c->produitsLibelles) ?>
                    </p>
                    <div class="mt-auto flex items-center justify-between border-t border-gray-100 pt-4">
                        <span class="text-lg font-bold text-gray-900"><?= e($c->montantFormate()) ?></span>
                        <span class="text-xs text-gray-500">
                            <?= (int) $c->quantiteTotale ?> article<?= $c->quantiteTotale > 1 ? 's' : '' ?>
                        </span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="px-6 pb-4">
        <?php include __DIR__ . '/../partials/pagination.php'; ?>
    </div>
</div>

<!-- ============================== MODALES ============================== -->
<?php foreach ($commandes as $c): ?>
    <?php include __DIR__ . '/partials/modals.php'; ?>
<?php endforeach; ?>

<script>
    function closeAllKebabs() {
        document.querySelectorAll('[data-kebab-menu]').forEach(function (menu) {
            menu.classList.add('hidden');
        });
    }

    function toggleKebab(id) {
        var menu = document.getElementById(id);
        if (!menu) return;
        var dejaOuvert = !menu.classList.contains('hidden');
        closeAllKebabs();
        if (dejaOuvert) return;

        var bouton = document.querySelector('[data-kebab-btn][onclick*="' + id + '"]');
        if (!bouton) return;
        var rect = bouton.getBoundingClientRect();

        menu.classList.remove('hidden');
        var largeur = menu.offsetWidth;
        var hauteur = menu.offsetHeight;
        var gauche = Math.max(8, rect.right - largeur);
        var haut = Math.max(8, rect.top - hauteur - 4);
        if (haut < 8) { haut = rect.bottom + 4; }

        menu.style.left = gauche + 'px';
        menu.style.top = haut + 'px';
    }

    document.addEventListener('click', function (event) {
        if (!event.target.closest('[data-kebab-btn]')) {
            closeAllKebabs();
        }
    });
</script>