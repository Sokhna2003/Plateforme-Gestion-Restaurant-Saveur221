<?php

declare(strict_types=1);

/**
 * Partiel de pagination — flèches gauche/droite + numéro de page au milieu.
 *
 * @var int $page
 * @var int $totalPages
 * @var string $lienBase URL sans paramètres
 */

$lienBase = $lienBase ?? '';
$page = $page ?? 1;
$totalPages = $totalPages ?? 1;

$params = $_GET;
unset($params['page']);
$qs = http_build_query($params);

$href = static function (int $p) use ($lienBase, $qs): string {
    $url = $lienBase;
    if ($qs !== '') {
        $url .= '?' . $qs;
    }
    return $url . ($qs !== '' ? '&' : '?') . 'page=' . $p;
};
?>

<?php if ($totalPages > 1): ?>
<div class="flex justify-end mt-4">
    <nav class="flex items-center gap-1 bg-white border border-gray-200 rounded-lg p-1" aria-label="Pagination">
        <?php if ($page > 1): ?>
            <a href="<?= e($href($page - 1)) ?>"
               class="w-9 h-9 flex items-center justify-center rounded-md text-brand-orange hover:bg-brand-orange hover:text-white transition"
               title="Page précédente">
                <i class="fa-solid fa-chevron-left text-xs"></i>
            </a>
        <?php else: ?>
            <span class="w-9 h-9 flex items-center justify-center rounded-md text-gray-300 cursor-not-allowed">
                <i class="fa-solid fa-chevron-left text-xs"></i>
            </span>
        <?php endif; ?>

        <span class="min-w-[4.5rem] text-center text-sm font-medium text-gray-700">
            Page <?= e((string) $page) ?> / <?= e((string) $totalPages) ?>
        </span>

        <?php if ($page < $totalPages): ?>
            <a href="<?= e($href($page + 1)) ?>"
               class="w-9 h-9 flex items-center justify-center rounded-md text-brand-orange hover:bg-brand-orange hover:text-white transition"
               title="Page suivante">
                <i class="fa-solid fa-chevron-right text-xs"></i>
            </a>
        <?php else: ?>
            <span class="w-9 h-9 flex items-center justify-center rounded-md text-gray-300 cursor-not-allowed">
                <i class="fa-solid fa-chevron-right text-xs"></i>
            </span>
        <?php endif; ?>
    </nav>
</div>
<?php endif; ?>