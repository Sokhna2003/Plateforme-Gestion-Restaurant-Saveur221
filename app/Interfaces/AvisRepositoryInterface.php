<?php

declare(strict_types=1);

namespace App\Interfaces;

use App\Models\Avis;

interface AvisRepositoryInterface
{
    public function findById(int $id): ?Avis;

    /** @return Avis[] */
    public function paginer(?string $terme = null, ?int $note = null, int $page = 1, int $perPage = 8): array;

    public function compterGestion(?string $terme = null, ?int $note = null): int;

    public function count(): int;

    public function noteMoyenne(): float;

    public function compterNote(int $note): int;

    public function compterAvecCommentaire(): int;

    public function supprimer(int $id): void;
}