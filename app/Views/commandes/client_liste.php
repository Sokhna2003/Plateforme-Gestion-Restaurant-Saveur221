<?php
$statutBadge = static function (string $statut): string {
    return match ($statut) {
        'EN_ATTENTE' => 'bg-amber-100 text-amber-700',
        'EN_PREPARATION' => 'bg-brand-orange/10 text-brand-orange',
        'PRETE' => 'bg-blue-100 text-blue-700',
        'RETIREE' => 'bg-green-100 text-green-700',
        'ANNULEE' => 'bg-red-100 text-red-600',
        default => 'bg-gray-100 text-gray-500',
    };
};
?>

<div class="mb-8">
    <h1 class="font-serif text-3xl font-bold mb-2">Mes commandes</h1>
    <span class="block w-16 h-1 bg-brand-orange"></span>
</div>

<?php if (empty($commandes)): ?>
    <div class="text-center py-16 bg-white rounded-2xl shadow-sm border border-brand-brown/10">
        <i class="fa-solid fa-receipt text-5xl text-brand-brown/20 mb-5"></i>
        <h2 class="font-serif text-2xl font-bold mb-2">Aucune commande pour le moment</h2>
        <p class="text-brand-brown/60 mb-8">Parcourez le menu et passez votre première commande.</p>
        <a href="<?= $base ?>/menu"
           class="inline-block bg-brand-orange text-white px-6 py-3 rounded-full font-medium hover:opacity-90 transition">
            Découvrir le menu
        </a>
    </div>
<?php else: ?>
    <div class="space-y-4">
        <?php foreach ($commandes as $cmd): ?>
            <div class="bg-white rounded-2xl shadow-sm border border-brand-brown/10 p-5 flex items-center gap-4">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-3">
                        <p class="font-serif font-bold">Commande n°<?= (int) $cmd->id ?></p>
                        <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-medium <?= $statutBadge($cmd->statut) ?>">
                            <?= e($cmd->libelleStatut()) ?>
                        </span>
                    </div>
                    <p class="text-sm text-brand-brown/50 mt-1 truncate"><?= e($cmd->dateFormatee()) ?> ·
                        <?= (int) ($cmd->nbProduits ?? 0) ?> <?= ($cmd->nbProduits ?? 0) > 1 ? 'articles' : 'article' ?> ·
                        <?= e((string) ($cmd->produitsLibelles ?? '')) ?></p>
                </div>
                <div class="text-right shrink-0">
                    <p class="font-semibold text-brand-orange"><?= $cmd->montantFormate() ?></p>
                    <?php if (in_array((int) $cmd->id, $idsAvisPossibles ?? [], true)): ?>
                        <a href="<?= $base ?>/client/commandes/<?= (int) $cmd->id ?>/avis"
                           class="inline-block mt-2 text-xs font-medium bg-brand-orange/10 text-brand-orange px-3 py-1 rounded-full hover:bg-brand-orange hover:text-white transition">
                            Laisser un avis
                        </a>
                    <?php endif; ?>
                    <a href="<?= $base ?>/client/commandes/<?= (int) $cmd->id ?>"
                       class="inline-block mt-2 text-xs font-medium text-brand-orange hover:underline">
                        Voir le détail
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>