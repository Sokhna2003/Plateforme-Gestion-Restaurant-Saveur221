<?php

declare(strict_types=1);

namespace App\Models;

use stdClass;

/**
 * Ligne d'une commande (un article commande).
 */
class LigneCommande
{
    public function __construct(
        public readonly int $id,
        public readonly int $commandeId,
        public readonly int $produitId,
        public readonly int $quantite,
        public readonly float $prixUnitaire,
        public readonly string $produitLibelle,
    ) {}

    public static function fromRow(stdClass $row): self
    {
        return new self(
            id: (int) $row->id,
            commandeId: (int) $row->commande_id,
            produitId: (int) $row->produit_id,
            quantite: (int) $row->quantite,
            prixUnitaire: (float) $row->prix_unitaire,
            produitLibelle: $row->produit_libelle,
        );
    }

    public function sousTotal(): float
    {
        return $this->quantite * $this->prixUnitaire;
    }

    public function prixFormate(): string
    {
        return number_format($this->prixUnitaire, 0, ',', ' ') . ' FCFA';
    }

    public function sousTotalFormate(): string
    {
        return number_format($this->sousTotal(), 0, ',', ' ') . ' FCFA';
    }
}