<?php

declare(strict_types=1);

namespace App\Services;

/**
 * Panier en session, utilisable par un visiteur ou un client connecte.
 *
 * La structure stockee est un tableau simple :  [produit_id => quantite].
 * Les informations de produit (libelle, prix, image) sont relues a chaque
 * affichage depuis la base via ProduitService::parIds().
 */
class PanierService
{
    public const MAX_QUANTITE = 99;

    public function __construct()
    {
        if (!isset($_SESSION['panier']) || !is_array($_SESSION['panier'])) {
            $_SESSION['panier'] = [];
        }
    }

    public function ajouter(int $produitId, int $quantite = 1): void
    {
        $quantite = max(1, min($quantite, self::MAX_QUANTITE));
        $total = ($_SESSION['panier'][$produitId] ?? 0) + $quantite;

        $_SESSION['panier'][$produitId] = min(self::MAX_QUANTITE, $total);
    }

    public function modifier(int $produitId, int $quantite): void
    {
        if ($quantite <= 0) {
            $this->retirer($produitId);
            return;
        }

        $_SESSION['panier'][$produitId] = min(self::MAX_QUANTITE, $quantite);
    }

    public function retirer(int $produitId): void
    {
        unset($_SESSION['panier'][$produitId]);
    }

    public function vider(): void
    {
        $_SESSION['panier'] = [];
    }

    public function contient(int $produitId): bool
    {
        return isset($_SESSION['panier'][$produitId]);
    }

    /** @return array<int, int> Produit id => quantite */
    public function quantites(): array
    {
        return $_SESSION['panier'];
    }

    /** Nombre total d'articles dans le panier (badge du header). */
    public function count(): int
    {
        return array_sum($_SESSION['panier']);
    }
}