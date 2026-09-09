<?php

declare(strict_types=1);

namespace App\Interfaces;

use App\Models\Categorie;

interface CategorieRepositoryInterface
{
    /** @return Categorie[] */
    public function all(): array;

    /** @return Categorie[] */
    public function search(string $terme): array;

    /**
     * @return Categorie[]
     */
    public function paginate(int $page, int $perPage, string $terme = ''): array;

    public function compter(string $terme = ''): int;

    public function findById(int $id): ?Categorie;

    public function findByNom(string $nom): ?Categorie;

    public function create(array $data): Categorie;

    public function update(int $id, array $data): void;

    public function delete(int $id): void;

    /** @return Categorie[] */
    public function trashed(string $terme = ''): array;

    public function findTrashedById(int $id): ?Categorie;

    public function restore(int $id): void;

    public function forceDelete(int $id): void;

    public function compterProduits(int $categorieId, bool $avecCorbeille = false): int;

    public function count(): int;
}