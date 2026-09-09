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
    ) {}

    public static function fromRow(stdClass $row): self
    {
        return new self(
            id: (int) $row->id,
            nom: $row->nom,
            description: $row->description ?? null,
        );
    }
}
