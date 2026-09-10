<?php

declare(strict_types=1);

namespace App\Interfaces;

use App\Models\Produit;

interface ProduitRepositoryInterface
{
    /** @return Produit[] */
    public function all(): array;

    /** @return Produit[] */
    public function disponibles(): array;

    public function findById(int $id): ?Produit;

    /** @return Produit[] */
    public function search(string $motCle): array;

    /** @return Produit[] */
    public function parCategorie(int $categorieId): array;

    /** @return Produit[] */
    public function similaires(int $categorieId, int $excludeId, int $limite = 3): array;

    /** @return Produit[] */
    public function paginate(int $page, int $perPage = 8, ?int $categorieId = null, ?string $motCle = null): array;

    public function compter(?int $categorieId = null, ?string $motCle = null): int;

    /** @return Produit[] */
    public function paginerAdministration(
        int $page,
        int $perPage = 8,
        ?int $categorieId = null,
        ?string $disponible = null,
        ?string $motCle = null
    ): array;

    public function compterAdministration(
        ?int $categorieId = null,
        ?string $disponible = null,
        ?string $motCle = null
    ): int;

    public function definirDisponibilite(int $id, bool $disponible): void;

    public function create(array $data): Produit;

    public function update(int $id, array $data): void;

    public function delete(int $id): void;

    /** @return Produit[] */
    public function trashed(string $terme = ''): array;

    public function findTrashedById(int $id): ?Produit;

    public function restore(int $id): void;

    public function forceDelete(int $id): void;

    public function decrementStock(int $id, int $quantite = 1): void;

    public function incrementStock(int $id, int $quantite = 1): void;

    /** @return Produit[] */
    public function paginerStock(?string $etat = null, int $page = 1, int $perPage = 8): array;

    public function compterStock(?string $etat = null): int;

    public function definirSeuil(int $id, int $seuil): void;
}
