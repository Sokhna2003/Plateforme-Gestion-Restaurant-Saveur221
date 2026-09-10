<?php

declare(strict_types=1);

namespace App\Interfaces;

use App\Models\Utilisateur;

interface UtilisateurRepositoryInterface
{
    public function findById(int $id): ?Utilisateur;

    public function findByEmail(string $email): ?Utilisateur;

    /** @return Utilisateur[] */
    public function all(): array;

    /** @return Utilisateur[] */
    public function search(string $terme): array;

    public function count(): int;

    /** @return \stdClass[] */
    public function listerRoles(): array;

    /**
     * Liste paginee des utilisateurs internes, avec filtres optionnels.
     *
     * @return Utilisateur[]
     */
    public function paginer(int $page, int $perPage, ?int $roleId, ?string $actif, ?string $motCle): array;

    public function compterGestion(?int $roleId, ?string $actif, ?string $motCle): int;

    public function compterParRole(int $roleId): int;

    public function compterActifs(): int;

    public function create(array $data): Utilisateur;

    public function update(int $id, array $data): void;

    public function definirActif(int $id, bool $actif): void;

    public function delete(int $id): void;
}