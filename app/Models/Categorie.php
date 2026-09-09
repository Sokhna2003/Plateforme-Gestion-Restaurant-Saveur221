<?php

declare(strict_types=1);

namespace App\Models;

use stdClass;

class Categorie
{
    public function __construct(
        public readonly int $id,
        public readonly string $nom,
        public readonly ?string $description,
        public readonly ?string $image,
        public readonly string $dateAjout,
        public readonly int $nombreDeProduits,
        public readonly ?string $supprimeLe = null,
    ) {}

    public static function fromRow(stdClass $row): self
    {
        return new self(
            id: (int) $row->id,
            nom: $row->nom,
            description: $row->description ?? null,
            image: $row->image ?? null,
            dateAjout: $row->date_ajout ?? '',
            nombreDeProduits: (int) ($row->nombre_de_produits ?? 0),
            supprimeLe: $row->supprime_le ?? null,
        );
    }
}