<?php

declare(strict_types=1);

namespace App\Interfaces;

use App\Models\Client;
use App\Models\Commande;

interface ClientRepositoryInterface
{
    public function findById(int $id): ?Client;

    public function findByEmail(string $email): ?Client;

    public function create(array $data): Client;

    public function update(int $id, array $data): void;

    public function updatePhoto(int $id, ?string $photo): void;

    /** @return Client[] */
    public function all(): array;

    public function search(string $terme): array;

    public function count(): int;

    /** @return Client[] */
    public function paginer(?string $terme = null, ?string $avecCommandes = null, int $page = 1, int $perPage = 8): array;

    public function compterGestion(?string $terme = null, ?string $avecCommandes = null): int;

    public function compterAvecCommandes(): int;

    public function compterCommandes(): int;

    public function chiffreAffaires(): float;

    /** @return Commande[] */
    public function commandesPour(int $clientId): array;

    public function aDesCommandesOuAvis(int $clientId): bool;

    public function supprimer(int $id): void;
}