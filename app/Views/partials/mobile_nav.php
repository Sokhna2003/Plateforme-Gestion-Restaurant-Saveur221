<?php

declare(strict_types=1);

// Barre de navigation basse (mobile) + centre de contrôle admin / gérant.
// - Visiteur : bottom bar seule (Accueil, Menu, Panier, Connexion).
// - Client   : bottom bar seule (Accueil, Menu, Panier, Commandes).
// - Admin / gérant : Dashboard, Catégories [Centre] Produits, Stock + centre de contrôle
//   (tuiles icône en haut / nom en bas : Commandes, Paiements, Statistiques, Corbeille
//   et, pour l'admin, Utilisateurs, Clients, Avis) + Déconnexion.
// A inclure dans un layout juste avant </body>. Variables disponibles : $base.

$cheminMobile = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$baseMobile = (string) ($base ?? '');

/** Exact (page racine par exemple) */
$mobileExact = static function (string $url) use ($cheminMobile, $baseMobile): bool {
    return $cheminMobile === $baseMobile . $url;
};

/** Exact ou sous-route (ex : /menu, /menu/detail) */
$mobileActif = static function (string $url) use ($cheminMobile, $baseMobile): bool {
    $complet = $baseMobile . $url;
    return $cheminMobile === $complet || str_starts_with($cheminMobile, $complet . '/');
};

$userInterneMobile = $_SESSION['user'] ?? null;
$clientMobile = $_SESSION['client'] ?? null;

// Le centre de contrôle n'existe que pour admin / gérant.
$aCentreMobile = $userInterneMobile !== null;

$navMobile = [];
$ccRaccourcis = [];

if ($userInterneMobile !== null) {
    // ----- Utilisateur interne (admin / gérant) -----
    $prefixeMobile = (($userInterneMobile['role'] ?? '') === 'ADMIN') ? '/admin' : '/gerant';
    $estAdminMobile = (($userInterneMobile['role'] ?? '') === 'ADMIN');
    $navMobile = [
        ['icon' => 'fa-solid fa-gauge-high', 'label' => 'Dashboard', 'url' => $prefixeMobile, 'actif' => $mobileExact($prefixeMobile)],
        ['icon' => 'fa-solid fa-folder-open', 'label' => 'Catégories', 'url' => $prefixeMobile . '/categories', 'actif' => $mobileActif($prefixeMobile . '/categories')],
        ['icon' => 'fa-solid fa-utensils', 'label' => 'Produits', 'url' => $prefixeMobile . '/produits', 'actif' => $mobileActif($prefixeMobile . '/produits')],
        ['icon' => 'fa-solid fa-boxes-stacked', 'label' => 'Stock', 'url' => $prefixeMobile . '/stock', 'actif' => $mobileActif($prefixeMobile . '/stock')],
    ];
    $ccRaccourcis = [
        ['icon' => 'fa-solid fa-receipt', 'label' => 'Commandes', 'url' => $prefixeMobile . '/commandes'],
        ['icon' => 'fa-solid fa-credit-card', 'label' => 'Paiements', 'url' => $prefixeMobile . '/paiements'],
        ['icon' => 'fa-solid fa-chart-line', 'label' => 'Statistiques', 'url' => $prefixeMobile . '/stats'],
        ['icon' => 'fa-solid fa-trash-can', 'label' => 'Corbeille', 'url' => $prefixeMobile . '/corbeille'],
    ];
    if ($estAdminMobile) {
        $ccRaccourcis[] = ['icon' => 'fa-solid fa-users', 'label' => 'Utilisateurs', 'url' => '/admin/utilisateurs'];
        $ccRaccourcis[] = ['icon' => 'fa-solid fa-user', 'label' => 'Clients', 'url' => '/admin/clients'];
        $ccRaccourcis[] = ['icon' => 'fa-solid fa-star', 'label' => 'Avis', 'url' => '/admin/avis'];
    }
} elseif ($clientMobile !== null) {
    // ----- Client connecté (sans centre de contrôle) -----
    $navMobile = [
        ['icon' => 'fa-solid fa-house', 'label' => 'Accueil', 'url' => '/', 'actif' => $mobileExact('/')],
        ['icon' => 'fa-solid fa-utensils', 'label' => 'Menu', 'url' => '/menu', 'actif' => $mobileActif('/menu')],
        [
            'icon' => 'fa-solid fa-cart-shopping',
            'label' => 'Panier',
            'url' => '/panier',
            'actif' => $mobileExact('/panier'),
            'badge' => (int) panier_count(),
        ],
        ['icon' => 'fa-solid fa-clipboard-list', 'label' => 'Commandes', 'url' => '/client/commandes', 'actif' => $mobileActif('/client/commandes')],
    ];
} else {
    // ----- Visiteur -----
    $navMobile = [
        ['icon' => 'fa-solid fa-house', 'label' => 'Accueil', 'url' => '/', 'actif' => $mobileExact('/')],
        ['icon' => 'fa-solid fa-utensils', 'label' => 'Menu', 'url' => '/menu', 'actif' => $mobileActif('/menu')],
        [
            'icon' => 'fa-solid fa-cart-shopping',
            'label' => 'Panier',
            'url' => '/panier',
            'actif' => $mobileExact('/panier'),
            'badge' => (int) panier_count(),
        ],
        ['icon' => 'fa-solid fa-right-to-bracket', 'label' => 'Connexion', 'url' => '/connexion', 'actif' => $mobileExact('/connexion')],
    ];
}

// Rendu d'un item de navigation.
$rendreItem = static function (array $item) use ($baseMobile): string {
    $badge = isset($item['badge']) && (int) $item['badge'] > 0
        ? '<span class="absolute -top-1.5 -right-2 bg-green-600 text-white text-[10px] min-w-4 h-4 px-1 rounded-full flex items-center justify-center">' . (int) $item['badge'] . '</span>'
        : '';
    $classe = $item['actif']
        ? 'text-brand-orange'
        : 'text-brand-brown/50 hover:text-brand-orange';
    $icone = '<i class="' . $item['icon'] . '"></i>' . $badge;
    return '<a href="' . $baseMobile . $item['url'] . '" class="flex flex-col items-center justify-center gap-0.5 py-2 text-[11px] font-medium ' . $classe . ' transition">
        <span class="relative text-lg leading-none">' . $icone . '</span>
        <span>' . $item['label'] . '</span>
    </a>';
};

// Bouton central (centre de contrôle) : icône + nom, comme les autres items.
$rendreCentre = static function (): string {
    return '<div class="relative flex items-center justify-center">
        <button type="button" onclick="basculeCentreDeControle()" aria-label="Centre de contrôle"
                class="flex flex-col items-center justify-center gap-0.5 py-2 text-[11px] font-semibold text-brand-orange transition active:scale-95">
            <span class="relative flex items-center justify-center">
                <span class="w-9 h-9 rounded-full bg-brand-orange/10 flex items-center justify-center text-brand-orange text-lg">
                    <i class="fa-solid fa-sliders"></i>
                </span>
            </span>
            <span>Menu</span>
        </button>
    </div>';
};

// Tuile du centre de contrôle : icône en haut, nom en bas.
$rendreTuile = static function (array $tuile) use ($baseMobile): string {
    return '<a href="' . $baseMobile . $tuile['url'] . '" class="flex flex-col items-center justify-center gap-2 px-3 py-4 text-brand-brown hover:text-brand-orange transition">
        <span class="w-11 h-11 rounded-full bg-brand-orange/10 text-brand-orange flex items-center justify-center text-base">
            <i class="' . $tuile['icon'] . '"></i>
        </span>
        <span class="text-xs font-semibold text-center">' . $tuile['label'] . '</span>
    </a>';
};
?>

<!-- ===================== Barre de navigation mobile (bottom bar) ===================== -->
<div class="md:hidden fixed bottom-0 inset-x-0 z-40">
    <nav class="relative bg-white border-t border-brand-brown/10 px-2 pt-1 pb-[env(safe-area-inset-bottom)]">
        <div class="grid <?= $aCentreMobile ? 'grid-cols-5' : 'grid-cols-4' ?>">
            <?= $rendreItem($navMobile[0] ?? ['icon' => '', 'label' => '', 'url' => '/', 'actif' => false]) ?>
            <?= $rendreItem($navMobile[1] ?? ['icon' => '', 'label' => '', 'url' => '/', 'actif' => false]) ?>
            <?php if ($aCentreMobile): ?>
                <?= $rendreCentre() ?>
            <?php endif; ?>
            <?= $rendreItem($navMobile[2] ?? ['icon' => '', 'label' => '', 'url' => '/', 'actif' => false]) ?>
            <?= $rendreItem($navMobile[3] ?? ['icon' => '', 'label' => '', 'url' => '/', 'actif' => false]) ?>
        </div>
    </nav>
</div>

<?php if ($aCentreMobile): ?>
<!-- ===================== Centre de contrôle (admin / gérant) ===================== -->
<div id="centre-controle" class="hidden md:hidden fixed inset-0 z-50">
    <div class="absolute inset-0 bg-black/60" onclick="fermeCentreDeControle()"></div>
    <div class="absolute bottom-0 inset-x-0 bg-white rounded-t-3xl shadow-2xl max-h-[88vh] overflow-y-auto">
        <div class="flex justify-center pt-3 pb-1">
            <span class="w-10 h-1.5 rounded-full bg-gray-300"></span>
        </div>

        <div class="px-6 pb-8 pt-2">
            <p class="text-sm font-bold text-gray-900 mb-3">Accès rapides</p>

            <!-- Raccourcis : icône en haut, nom en bas -->
            <div class="grid grid-cols-2 gap-3">
                <?php foreach ($ccRaccourcis as $tuile): ?>
                    <?= $rendreTuile($tuile) ?>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<script>
    function basculeCentreDeControle() {
        const el = document.getElementById('centre-controle');
        if (el) {
            el.classList.toggle('hidden');
        }
    }
    function fermeCentreDeControle() {
        const el = document.getElementById('centre-controle');
        if (el) el.classList.add('hidden');
    }
</script>
<?php endif; ?>