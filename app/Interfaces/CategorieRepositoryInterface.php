<?php

declare(strict_types=1);

namespace App\Interfaces;

use App\Models\Categorie;

interface CategorieRepositoryInterface
{
    /** @return Categorie[] */
    public function all(): array;

    public function findById(int $id): ?Categorie;

    public function create(array $data): Categorie;

    public function update(int $id, array $data): void;

    public function delete(int $id): void;

    public function count(): int;
}
