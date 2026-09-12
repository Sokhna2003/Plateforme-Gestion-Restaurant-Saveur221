<?php

declare(strict_types=1);

/**
 * @var \App\Models\PaiementCommande[] $commandes
 * @var int $page
 * @var int $totalPages
 * @var int $total
 * @var string $statut ('' | payee | impayee | partielle)
 * @var array{totalEncaise: float, impayees: int, partiellement: int} $stats
 * @var \App\Models\PaiementCommande[] $payables
 * @var string $baseRoute (/admin ou /gerant)
 */

use App\Models\PaiementCommande;

$lienBase = $base . $baseRoute . '/paiements';
$statutCourant = $statut ?? '';
$modes = \App\Services\PaiementService::MODES;
$totalEncaise = number_format($stats['totalEncaise'], 0, ',', ' ');

$badgeClasses = static function (string $statutPaiement): string {
    return match ($statutPaiement) {
        PaiementCommande::STATUT_PAYEE => 'bg-green-100 text-green-700',
        PaiementCommande::STATUT_PARTIEL => 'bg-brand-orange/10 text-brand-orange',
        default => 'bg-amber-100 text-amber-700',
    };
};
?>

<!-- En-tête de la page -->
<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-900">Paiements des commandes</h2>
    <p class="text-sm text-gray-500 mt-1">Enregistrez et suivez les paiements des commandes de Saveur 221.</p>
</div>

<!-- Cartes statistiques (border gauche + pourcentage non dynamique) -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 border-l-4 border-l-green-500 p-5">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Total encaissé</p>
                <p class="mt-3 text-3xl font-bold text-gray-900">
                    <?= $totalEncaise ?> <small class="text-sm font-medium text-gray-400">FCFA</small>
                </p>
            </div>
            <span class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center text-green-600">
                <i class="fa-solid fa-coins"></i>
            </span>
        </div>
        <p class="mt-3 text-sm font-medium text-green-600">
            <i class="fa-solid fa-arrow-trend-up text-xs mr-1"></i>+15,2 %
        </p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 border-l-4 border-l-amber-400 p-5">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Commandes impayées</p>
                <p class="mt-3 text-3xl font-bold text-gray-900"><?= $stats['impayees'] ?></p>
            </div>
            <span class="w-10 h-10 rounded-full bg-amber-100 flex items-center justify-center text-amber-600">
                <i class="fa-solid fa-hourglass-half"></i>
            </span>
        </div>
        <p class="mt-3 text-sm font-medium text-amber-600">
            <i class="fa-solid fa-arrow-trend-up text-xs mr-1"></i>+8,4 %
        </p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 border-l-4 border-l-brand-orange p-5">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Commandes partiellement payées</p>
                <p class="mt-3 text-3xl font-bold text-gray-900"><?= $stats['partiellement'] ?></p>
            </div>
            <span class="w-10 h-10 rounded-full bg-brand-orange/10 flex items-center justify-center text-brand-orange">
                <i class="fa-solid fa-circle-half-stroke"></i>
            </span>
        </div>
        <p class="mt-3 text-sm font-medium text-brand-orange">
            <i class="fa-solid fa-arrow-trend-up text-xs mr-1"></i>+4,1 %
        </p>
    </div>
</div>

<!-- Filtres + enregistrement + tableau -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100">
    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between gap-4 flex-wrap">
        <div class="flex flex-wrap gap-2">
            <a href="<?= e($lienBase) ?>"
               class="px-4 py-1.5 rounded-full text-sm border <?= $statutCourant === '' ? 'bg-brand-orange text-white border-brand-orange' : 'border-brand-brown/20 hover:border-brand-orange' ?>">
                Tous
            </a>
            <a href="<?= e($lienBase) ?>?statut=impayee"
               class="px-4 py-1.5 rounded-full text-sm border <?= $statutCourant === 'impayee' ? 'bg-brand-orange text-white border-brand-orange' : 'border-brand-brown/20 hover:border-brand-orange' ?>">
                Impayées
            </a>
            <a href="<?= e($lienBase) ?>?statut=partielle"
               class="px-4 py-1.5 rounded-full text-sm border <?= $statutCourant === 'partielle' ? 'bg-brand-orange text-white border-brand-orange' : 'border-brand-brown/20 hover:border-brand-orange' ?>">
                Partiellement payées
            </a>
            <a href="<?= e($lienBase) ?>?statut=payee"
               class="px-4 py-1.5 rounded-full text-sm border <?= $statutCourant === 'payee' ? 'bg-brand-orange text-white border-brand-orange' : 'border-brand-brown/20 hover:border-brand-orange' ?>">
                Payées
            </a>
        </div>
        <button type="button" onclick="openModal('paiement-modal')"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-white bg-brand-orange hover:opacity-90 transition whitespace-nowrap">
            <i class="fa-solid fa-plus text-xs"></i> Enregistrer un paiement
        </button>
    </div>

    <?php if (!$commandes): ?>
        <div class="py-16 text-center text-sm text-gray-500">
            Aucune commande ne correspond à ce filtre.
        </div>
    <?php else: ?>
        <div class="w-full overflow-x-auto">
            <table class="w-full text-[13px]">
                <thead>
                    <tr class="bg-gray-50 text-left text-xs uppercase tracking-wide text-gray-500 whitespace-nowrap">
                        <th class="px-3 py-3 font-medium">Commande</th>
                        <th class="px-3 py-3 font-medium">Client</th>
                        <th class="px-3 py-3 font-medium text-right">MT. Commande</th>
                        <th class="px-3 py-3 font-medium text-right">MT. Payé</th>
                        <th class="px-3 py-3 font-medium text-right">MT. Restant</th>
                        <th class="px-3 py-3 font-medium">Date</th>
                        <th class="px-3 py-3 font-medium">Statut</th>
                        <th class="px-3 py-3 font-medium text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php foreach ($commandes as $c): ?>
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="px-3 py-3">
                                <a href="<?= e($base . $baseRoute . '/commandes/' . $c->id) ?>"
                                   class="font-medium text-brand-orange hover:underline whitespace-nowrap">
                                    Commande n°<?= $c->id ?>
                                </a>
                            </td>
                            <td class="px-3 py-3 max-w-[200px]">
                                <p class="text-gray-900 font-medium truncate" title="<?= e($c->clientNomComplet()) ?>">
                                    <?= e($c->clientNomComplet()) ?>
                                </p>
                            </td>
                            <td class="px-3 py-3 text-right text-gray-700 whitespace-nowrap"><?= e($c->montantFormate()) ?></td>
                            <td class="px-3 py-3 text-right font-medium text-green-700 whitespace-nowrap"><?= e($c->payeFormate()) ?></td>
                            <td class="px-3 py-3 text-right whitespace-nowrap">
                                <?php if ($c->montantRestant() > 0): ?>
                                    <span class="font-bold text-brand-orange"><?= e($c->restantFormate()) ?></span>
                                <?php else: ?>
                                    <span class="text-gray-400">—</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-3 py-3 text-gray-500 whitespace-nowrap"><?= e($c->dateFormatee()) ?></td>
                            <td class="px-3 py-3">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium <?= $badgeClasses($c->statutPaiement()) ?> whitespace-nowrap">
                                    <?= e($c->libelleStatut()) ?>
                                </span>
                            </td>
                            <td class="px-3 py-3">
                                <div class="flex items-center justify-end gap-2">
                                    <?php if ($c->montantRestant() > 0): ?>
                                        <button type="button" onclick="openPaiement(<?= $c->id ?>)"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium text-green-700 bg-green-50 border border-green-200 hover:bg-green-100 transition whitespace-nowrap">
                                            <i class="fa-solid fa-hand-holding-dollar text-[10px]"></i> Encaisser
                                        </button>
                                    <?php else: ?>
                                        <span class="inline-flex items-center gap-1 text-xs text-gray-400 whitespace-nowrap">
                                            <i class="fa-solid fa-circle-check text-[10px]"></i> Soldée
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="px-6 pb-4">
            <?php include __DIR__ . '/../partials/pagination.php'; ?>
        </div>
    <?php endif; ?>
</div>

<!-- ============================== MODAL ENREGISTREMENT PAIEMENT ============================== -->
<div id="paiement-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/50" onclick="closeModal('paiement-modal')"></div>
    <div class="relative bg-white rounded-2xl shadow-xl max-w-md w-full p-6">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-green-50 flex items-center justify-center text-green-600 text-xl shrink-0">
                <i class="fa-solid fa-hand-holding-dollar"></i>
            </div>
            <div>
                <h3 class="font-bold text-gray-900">Enregistrer un paiement</h3>
                <p class="text-sm text-gray-500 mt-1">Encaissez tout ou partie d'une commande.</p>
            </div>
        </div>

        <form method="post" action="<?= e($lienBase) ?>/enregistrer" class="mt-5 space-y-4">
            <input type="hidden" name="_token" value="<?= csrf() ?>">
            <?php if ($statutCourant !== ''): ?>
                <input type="hidden" name="statut" value="<?= e($statutCourant) ?>">
            <?php endif; ?>

            <div>
                <label for="paiement-select" class="block text-sm font-medium text-gray-900 mb-1">Commande</label>
                <select name="commande_id" id="paiement-select" onchange="majRestant()"
                        class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm bg-gray-50 text-gray-900 focus:outline-none focus:border-brand-orange">
                    <option value="">— Choisir une commande —</option>
                    <?php foreach ($payables as $p): ?>
                        <option value="<?= $p->id ?>"
                                data-restant-label="<?= e($p->restantFormate()) ?>"
                                data-restant-nombre="<?= (float) $p->montantRestant() ?>">
                            Commande n°<?= $p->id ?> — <?= e($p->clientNomComplet()) ?> (restant : <?= e($p->restantFormate()) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="rounded-lg bg-gray-50 border border-gray-100 px-4 py-3 flex items-center justify-between">
                <span class="text-sm text-gray-500">Reste à payer</span>
                <span class="text-lg font-bold text-green-600" id="paiement-restant">—</span>
            </div>

            <div>
                <label for="paiement-montant" class="block text-sm font-medium text-gray-900 mb-1">Montant payé</label>
                <input type="number" id="paiement-montant" name="montant" min="1" step="1" required
                       class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:border-brand-orange"
                       placeholder="Ex : 5000">
            </div>

            <div>
                <label for="paiement-mode" class="block text-sm font-medium text-gray-900 mb-1">Mode de paiement</label>
                <select name="mode_paiement" id="paiement-mode"
                        class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm bg-gray-50 text-gray-900 focus:outline-none focus:border-brand-orange">
                    <?php foreach ($modes as $mode): ?>
                        <option value="<?= e($mode) ?>"><?= e($mode) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" onclick="closeModal('paiement-modal')"
                        class="px-4 py-2 rounded-lg text-sm font-medium text-gray-600 border border-gray-200 hover:bg-gray-50 transition">
                    Annuler
                </button>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-white bg-green-500 hover:bg-green-600 transition">
                    <i class="fa-solid fa-check text-xs"></i> Confirmer
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openPaiement(id) {
        var select = document.getElementById('paiement-select');
        if (select) { select.value = String(id); majRestant(); }
        openModal('paiement-modal');
    }

    function majRestant() {
        var select = document.getElementById('paiement-select');
        var hint = document.getElementById('paiement-restant');
        var montant = document.getElementById('paiement-montant');
        if (!select || !hint) return;
        var option = select.selectedOptions[0];
        if (!option || !option.value) {
            hint.textContent = '—';
            if (montant) montant.max = '';
            return;
        }
        hint.textContent = option.dataset.restantLabel || '—';
        if (montant) montant.max = option.dataset.restantNombre || '';
    }

    document.addEventListener('DOMContentLoaded', majRestant);
</script>