<?php

declare(strict_types=1);

use App\Models\Avis;
use App\Models\Commande;

$estAdmin = $estAdmin ?? false;
$estGerant = $estGerant ?? false;
$estClient = $estClient ?? false;

$nomComplet = nomComplet();

$img = static fn (string $nom): string => $base . '/assets/images/' . rawurlencode($nom);

$statutBadge = static function (string $statut): string {
    return match ($statut) {
        Commande::STATUT_EN_ATTENTE => 'bg-amber-100 text-amber-700',
        Commande::STATUT_EN_PREPARATION => 'bg-brand-orange/10 text-brand-orange',
        Commande::STATUT_PRETE => 'bg-blue-100 text-blue-700',
        Commande::STATUT_RETIREE => 'bg-green-100 text-green-700',
        default => 'bg-gray-100 text-gray-500',
    };
};

$etoiles = static function (int $nb) {
    $html = '<div class="flex items-center gap-0.5">';
    for ($i = 1; $i <= 5; $i++) {
        $html .= $i <= $nb
            ? '<i class="fa-solid fa-star text-amber-400 text-xs"></i>'
            : '<i class="fa-regular fa-star text-gray-300 text-xs"></i>';
    }
    $html .= '</div>';
    return $html;
};

$formatPrix = static fn (float $prix): string => number_format($prix, 0, ',', ' ') . ' FCFA';
?>

<?php if ($estClient): ?>

<div class="max-w-2xl mx-auto text-center py-12">
    <div class="w-20 h-20 rounded-full bg-brand-orange/10 flex items-center justify-center mx-auto mb-6">
        <span class="text-brand-orange font-serif font-bold text-2xl"><?= strtoupper(substr($nomComplet, 0, 1)) ?></span>
    </div>
    <h1 class="font-serif text-3xl font-bold mb-2">Bienvenue, <?= e($nomComplet) ?> !</h1>
    <p class="text-brand-brown/60 mb-8">Consultez vos commandes et gérer votre compte.</p>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <a href="<?= $base ?>/menu" class="bg-white border border-brand-brown/10 rounded-xl p-6 hover:border-brand-orange transition">
            <div class="text-brand-orange text-2xl mb-2"><i class="fa-solid fa-utensils"></i></div>
            <h3 class="font-serif font-bold mb-1">Voir le menu</h3>
            <p class="text-sm text-brand-brown/50">Découvrez nos plats</p>
        </a>
        <a href="<?= $base ?>/client/commandes" class="bg-white border border-brand-brown/10 rounded-xl p-6 hover:border-brand-orange transition">
            <div class="text-brand-orange text-2xl mb-2"><i class="fa-solid fa-receipt"></i></div>
            <h3 class="font-serif font-bold mb-1">Mes commandes</h3>
            <p class="text-sm text-brand-brown/50">Historique et suivi</p>
        </a>
    </div>
</div>

<?php elseif ($estAdmin): ?>

<!-- ============================== EN-TETE ============================== -->
<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-900">Aperçu global</h2>
    <p class="text-sm text-gray-500 mt-1">Voici un aperçu global de l'activité de Saveur 221 aujourd'hui.</p>
</div>

<!-- ============================== CARTES ============================== -->
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4 mb-8">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Utilisateurs</p>
                <p class="mt-3 text-3xl font-bold text-gray-900"><?= (int) $stats['utilisateurs'] ?></p>
            </div>
            <span class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600">
                <i class="fa-solid fa-users"></i>
            </span>
        </div>
        <div class="mt-4 h-2 rounded-full bg-gray-100 overflow-hidden">
            <div class="h-2 rounded-full bg-blue-500 w-3/5"></div>
        </div>
        <p class="mt-2 text-xs font-medium text-blue-600">60 %</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Clients</p>
                <p class="mt-3 text-3xl font-bold text-gray-900"><?= (int) $stats['clients'] ?></p>
            </div>
            <span class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center text-green-600">
                <i class="fa-solid fa-user"></i>
            </span>
        </div>
        <div class="mt-4 h-2 rounded-full bg-gray-100 overflow-hidden">
            <div class="h-2 rounded-full bg-green-500 w-2/3"></div>
        </div>
        <p class="mt-2 text-xs font-medium text-green-600">67 %</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Commandes</p>
                <p class="mt-3 text-3xl font-bold text-gray-900"><?= (int) $stats['commandes'] ?></p>
            </div>
            <span class="w-10 h-10 rounded-full bg-amber-100 flex items-center justify-center text-amber-600">
                <i class="fa-solid fa-receipt"></i>
            </span>
        </div>
        <div class="mt-4 h-2 rounded-full bg-gray-100 overflow-hidden">
            <div class="h-2 rounded-full bg-amber-400 w-3/4"></div>
        </div>
        <p class="mt-2 text-xs font-medium text-amber-600">75 %</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Chiffre d'affaires</p>
                <p class="mt-3 text-2xl font-bold text-gray-900 truncate"><?= number_format((float) $stats['chiffreAffaires'], 0, ',', ' ') ?></p>
                <p class="text-xs text-gray-400 mt-0.5">FCFA</p>
            </div>
            <span class="w-10 h-10 rounded-full bg-brand-orange/10 flex items-center justify-center text-brand-orange">
                <i class="fa-solid fa-coins"></i>
            </span>
        </div>
        <div class="mt-4 h-2 rounded-full bg-gray-100 overflow-hidden">
            <div class="h-2 rounded-full bg-brand-orange w-4/5"></div>
        </div>
        <p class="mt-2 text-xs font-medium text-brand-orange">80 %</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Produits</p>
                <p class="mt-3 text-3xl font-bold text-gray-900"><?= (int) $stats['produits'] ?></p>
            </div>
            <span class="w-10 h-10 rounded-full bg-violet-100 flex items-center justify-center text-violet-600">
                <i class="fa-solid fa-utensils"></i>
            </span>
        </div>
        <div class="mt-4 h-2 rounded-full bg-gray-100 overflow-hidden">
            <div class="h-2 rounded-full bg-violet-500 w-1/2"></div>
        </div>
        <p class="mt-2 text-xs font-medium text-violet-600">50 %</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Avis</p>
                <p class="mt-3 text-3xl font-bold text-gray-900"><?= (int) $stats['avis'] ?></p>
            </div>
            <span class="w-10 h-10 rounded-full bg-pink-100 flex items-center justify-center text-pink-600">
                <i class="fa-solid fa-star"></i>
            </span>
        </div>
        <div class="mt-4 h-2 rounded-full bg-gray-100 overflow-hidden">
            <div class="h-2 rounded-full bg-pink-500 w-1/4"></div>
        </div>
        <p class="mt-2 text-xs font-medium text-pink-500">25 %</p>
    </div>
</div>

<!-- ============================== TENDANCES & ALERTES ============================== -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <p class="text-sm font-semibold text-gray-900 mb-3">Tendances revenus hebdomadaires</p>
        <img src="<?= e($img('Tendances-revenus-hebdomadaires.png')) ?>" alt="Tendances des revenus hebdomadaires"
             class="w-full h-auto rounded-lg">
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <p class="text-sm font-semibold text-gray-900 mb-3">Alertes & activités</p>
        <img src="<?= e($img('alertes-activités.png')) ?>" alt="Alertes et activités"
             class="w-full h-auto rounded-lg">
    </div>
</div>

<!-- ============================== COMMANDES RECENTES ============================== -->
<div class="bg-white border border-gray-200 rounded-xl overflow-hidden mb-8">
    <div class="flex items-center justify-between px-6 py-3 border-b border-gray-100">
        <h3 class="font-semibold text-gray-900">Commandes récentes</h3>
        <a href="<?= $base . $routeListeCommandes ?>"
           class="text-xs font-medium text-brand-orange hover:opacity-80 transition">Tout voir</a>
    </div>
    <?php if (empty($commandesRecentes)): ?>
        <div class="p-10 text-center text-gray-500 text-sm">
            <i class="fa-solid fa-receipt mb-3 text-3xl text-gray-300 block"></i>
            Aucune commande pour le moment.
        </div>
    <?php else: ?>
    <div class="overflow-x-auto">
    <table class="w-full text-sm">
        <thead>
            <tr class="bg-gray-50 text-left text-xs uppercase tracking-wide text-gray-500 whitespace-nowrap">
                <th class="px-4 py-3 font-medium">Commande</th>
                <th class="px-4 py-3 font-medium">Client</th>
                <th class="px-4 py-3 font-medium">Date</th>
                <th class="px-4 py-3 font-medium">Statut</th>
                <th class="px-4 py-3 font-medium">Montant</th>
                <th class="px-4 py-3 font-medium text-right">Action</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            <?php foreach ($commandesRecentes as $cmd): ?>
            <tr class="hover:bg-gray-50/50 transition">
                <td class="px-4 py-4 font-medium text-gray-900 whitespace-nowrap">n°<?= $cmd->id ?></td>
                <td class="px-4 py-4">
                    <p class="font-medium text-gray-900 truncate"><?= e($cmd->clientNomComplet()) ?></p>
                    <p class="text-xs text-gray-500 truncate"><?= e($cmd->clientEmail ?? '') ?></p>
                </td>
                <td class="px-4 py-4 text-gray-500 text-xs whitespace-nowrap"><?= e($cmd->dateFormatee()) ?></td>
                <td class="px-4 py-4">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium <?= $statutBadge($cmd->statut) ?> whitespace-nowrap">
                        <i class="fa-solid fa-circle text-[6px]"></i> <?= e($cmd->libelleStatut()) ?>
                    </span>
                </td>
                <td class="px-4 py-4 text-gray-900 text-xs font-medium whitespace-nowrap"><?= e($cmd->montantFormate()) ?></td>
                <td class="px-4 py-4">
                    <div class="flex items-center justify-end">
                        <a href="<?= $base . $routeListeCommandes ?>"
                           class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-medium text-brand-orange bg-brand-orange/10 hover:bg-brand-orange hover:text-white transition">
                            <i class="fa-solid fa-eye text-xs"></i> Voir
                        </a>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    </div>
    <?php endif; ?>
</div>

<!-- ============================== CLIENTS RECENTS & AVIS RECENTS ============================== -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
        <div class="flex items-center justify-between px-6 py-3 border-b border-gray-100">
            <h3 class="font-semibold text-gray-900">Clients récents</h3>
            <a href="<?= $base . $routeListeClients ?>"
               class="text-xs font-medium text-brand-orange hover:opacity-80 transition">Tout voir</a>
        </div>
        <?php if (empty($clientsRecents)): ?>
            <div class="p-10 text-center text-gray-500 text-sm">Aucun client pour le moment.</div>
        <?php else: ?>
        <div class="divide-y divide-gray-100">
            <?php foreach ($clientsRecents as $cl): ?>
            <div class="flex items-center justify-between px-6 py-3">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-9 h-9 rounded-full bg-brand-orange/10 flex items-center justify-center text-brand-orange font-bold text-xs shrink-0">
                        <?= e($cl->initiales()) ?>
                    </div>
                    <div class="min-w-0">
                        <p class="font-medium text-gray-900 truncate"><?= e($cl->nomComplet()) ?></p>
                        <p class="text-xs text-gray-500 truncate"><?= e($cl->email) ?></p>
                    </div>
                </div>
                <a href="<?= $base . $routeListeClients ?>"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium text-brand-orange bg-brand-orange/10 hover:bg-brand-orange hover:text-white transition shrink-0">
                    <i class="fa-solid fa-eye text-xs"></i> Voir
                </a>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>

    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
        <div class="flex items-center justify-between px-6 py-3 border-b border-gray-100">
            <h3 class="font-semibold text-gray-900">Avis récents</h3>
            <a href="<?= $base ?>/admin/avis"
               class="text-xs font-medium text-brand-orange hover:opacity-80 transition">Tout voir</a>
        </div>
        <?php if (empty($avisRecents)): ?>
            <div class="p-10 text-center text-gray-500 text-sm">Aucun avis pour le moment.</div>
        <?php else: ?>
        <div class="divide-y divide-gray-100">
            <?php foreach ($avisRecents as $a): ?>
            <div class="flex items-start gap-3 px-6 py-3">
                <div class="w-9 h-9 rounded-full bg-brand-orange/10 flex items-center justify-center text-brand-orange font-bold text-xs shrink-0">
                    <?= e($a->initiales()) ?>
                </div>
                <div class="min-w-0 flex-1">
                    <div class="flex items-center justify-between gap-2">
                        <p class="font-medium text-gray-900 text-sm truncate"><?= e($a->clientNomComplet()) ?></p>
                        <p class="text-[11px] text-gray-400 whitespace-nowrap"><?= e($a->dateAvisFormatee()) ?></p>
                    </div>
                    <?= $etoiles($a->note) ?>
                    <?php if ($a->commentaire !== null && $a->commentaire !== ''): ?>
                        <p class="text-xs text-gray-500 mt-1 truncate"><?= e($a->commentaireCourt(80)) ?></p>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php elseif ($estGerant): ?>

<!-- ============================== EN-TETE ============================== -->
<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-900">Aperçu du restaurant</h2>
    <p class="text-sm text-gray-500 mt-1">Voici un aperçu de l'activité de votre restaurant Saveur 221 aujourd'hui.</p>
</div>

<!-- ============================== CARTES ============================== -->
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4 mb-8">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 border-t-4 border-t-amber-400 p-5">
        <p class="text-sm font-medium text-gray-500">Commandes en attente</p>
        <p class="mt-3 text-3xl font-bold text-gray-900"><?= (int) $stats['enAttente'] ?></p>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 border-t-4 border-t-brand-orange p-5">
        <p class="text-sm font-medium text-gray-500">Commandes en préparation</p>
        <p class="mt-3 text-3xl font-bold text-gray-900"><?= (int) $stats['enPreparation'] ?></p>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 border-t-4 border-t-blue-500 p-5">
        <p class="text-sm font-medium text-gray-500">Commandes prêtes</p>
        <p class="mt-3 text-3xl font-bold text-gray-900"><?= (int) $stats['prete'] ?></p>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 border-t-4 border-t-violet-500 p-5">
        <p class="text-sm font-medium text-gray-500">Stock faible</p>
        <p class="mt-3 text-3xl font-bold text-gray-900"><?= (int) $stats['stockFaible'] ?></p>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 border-t-4 border-t-red-500 p-5">
        <p class="text-sm font-medium text-gray-500">Produits en rupture</p>
        <p class="mt-3 text-3xl font-bold text-gray-900"><?= (int) $stats['stockRupture'] ?></p>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 border-t-4 border-t-green-500 p-5">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Chiffre d'affaires du jour</p>
                <p class="mt-3 text-2xl font-bold text-gray-900 truncate"><?= number_format((float) $stats['caJour'], 0, ',', ' ') ?></p>
                <p class="text-xs text-gray-400 mt-0.5">FCFA</p>
            </div>
            <span class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center text-green-600">
                <i class="fa-solid fa-coins"></i>
            </span>
        </div>
    </div>
</div>

<!-- ============================== IMAGES STOCK / VENTES + COMMANDES RECENTES ============================== -->
<div class="grid grid-cols-1 lg:grid-cols-5 gap-6 mb-8">
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <p class="text-sm font-semibold text-gray-900 mb-3">Stock faible</p>
            <img src="<?= e($img('Stock-faible.png')) ?>" alt="Stock faible"
                 class="w-full h-auto rounded-lg">
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <p class="text-sm font-semibold text-gray-900 mb-3">Aperçu des ventes</p>
            <img src="<?= e($img('apercu-ventes.png')) ?>" alt="Aperçu des ventes"
                 class="w-full h-auto rounded-lg">
        </div>
    </div>

    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden lg:col-span-3">
        <div class="flex items-center justify-between px-6 py-3 border-b border-gray-100">
            <h3 class="font-semibold text-gray-900">Commandes récentes</h3>
            <a href="<?= $base . $routeListeCommandes ?>"
               class="text-xs font-medium text-brand-orange hover:opacity-80 transition">Tout voir</a>
        </div>
        <?php if (empty($commandesRecentes)): ?>
            <div class="p-10 text-center text-gray-500 text-sm">
                <i class="fa-solid fa-receipt mb-3 text-3xl text-gray-300 block"></i>
                Aucune commande pour le moment.
            </div>
        <?php else: ?>
        <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 text-left text-xs uppercase tracking-wide text-gray-500 whitespace-nowrap">
                    <th class="px-4 py-3 font-medium">Client</th>
                    <th class="px-4 py-3 font-medium">Date</th>
                    <th class="px-4 py-3 font-medium">Qt. Produits</th>
                    <th class="px-4 py-3 font-medium">Statut</th>
                    <th class="px-4 py-3 font-medium text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php foreach ($commandesRecentes as $cmd): ?>
                <tr class="hover:bg-gray-50/50 transition">
                    <td class="px-4 py-4">
                        <p class="font-medium text-gray-900 truncate"><?= e($cmd->clientNomComplet()) ?></p>
                        <p class="text-xs text-gray-500 truncate"><?= e($cmd->clientEmail ?? '') ?></p>
                    </td>
                    <td class="px-4 py-4 text-gray-500 text-xs whitespace-nowrap"><?= e($cmd->dateFormatee()) ?></td>
                    <td class="px-4 py-4 text-gray-700 text-xs max-w-[12rem] truncate"><?= e($produitsParCommande[$cmd->id] ?? '') ?></td>
                    <td class="px-4 py-4">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium <?= $statutBadge($cmd->statut) ?> whitespace-nowrap">
                            <i class="fa-solid fa-circle text-[6px]"></i> <?= e($cmd->libelleStatut()) ?>
                        </span>
                    </td>
                    <td class="px-4 py-4">
                        <div class="flex items-center justify-end">
                            <a href="<?= $base . $routeListeCommandes ?>"
                               class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-medium text-brand-orange bg-brand-orange/10 hover:bg-brand-orange hover:text-white transition">
                                <i class="fa-solid fa-eye text-xs"></i> Voir
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php endif; ?>