<?php

declare(strict_types=1);

namespace App\Interfaces;

use App\Models\Client;

interface ClientRepositoryInterface
{
    public function findById(int $id): ?Client;

    public function findByEmail(string $email): ?Client;

    public function create(array $data): Client;

    public function update(int $id, array $data): void;

    /** @return Client[] */
    public function all(): array;

    public function search(string $terme): array;

    public function count(): int;
}
