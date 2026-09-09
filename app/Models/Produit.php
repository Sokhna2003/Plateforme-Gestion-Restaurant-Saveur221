<?php

declare(strict_types=1);

namespace App\Models;

use stdClass;

class Produit
{
    public function __construct(
        public readonly int $id,
        public readonly string $libelle,
        public readonly ?string $description,
        public readonly float $prix,
        public readonly int $quantiteStock,
        public readonly int $seuilAlerte,
        public readonly bool $disponible,
        public readonly ?string $image,
        public readonly int $categorieId,
        public readonly ?string $categorieNom = null,
    ) {}

    public static function fromRow(stdClass $row): self
    {
        return new self(
            id: (int) $row->id,
            libelle: $row->libelle,
            description: $row->description ?? null,
            prix: (float) $row->prix,
            quantiteStock: (int) $row->quantite_stock,
            seuilAlerte: (int) $row->seuil_alerte,
            disponible: (bool) $row->disponible,
            image: $row->image ?? null,
            categorieId: (int) $row->categorie_id,
            categorieNom: $row->categorie_nom ?? null,
        );
    }

    public function estDisponible(): bool
    {
        return $this->disponible && $this->quantiteStock > 0;
    }

    public function estEnRupture(): bool
    {
        return $this->quantiteStock <= $this->seuilAlerte;
    }

    public function prixFormate(): string
    {
        return number_format($this->prix, 0, ',', ' ') . ' FCFA';
    }
}
