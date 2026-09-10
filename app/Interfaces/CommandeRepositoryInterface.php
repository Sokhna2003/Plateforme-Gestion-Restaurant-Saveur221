<?php

declare(strict_types=1);

namespace App\Interfaces;

use App\Models\Commande;
use App\Models\LigneCommande;
use stdClass;

interface CommandeRepositoryInterface
{
    /** @return Commande[] */
    public function paginer(?string $terme = null, ?string $statut = null, int $page = 1, int $perPage = 8): array;

    public function compter(?string $terme = null, ?string $statut = null): int;

    public function compterParStatut(string $statut): int;

    public function findById(int $id): ?Commande;

    /** @return LigneCommande[] */
    public function lignes(int $commandeId): array;

    public function modifierStatut(int $id, string $statut): void;

    public function trouverPaiement(int $commandeId): ?stdClass;
}