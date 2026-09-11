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

    public function compterTotal(): int;

    public function chiffreAffairesJour(): float;

    public function findById(int $id): ?Commande;

    /** @return LigneCommande[] */
    public function lignes(int $commandeId): array;

    public function modifierStatut(int $id, string $statut): void;

    /**
     * Crée une commande avec ses lignes (transaction gérée par l'appelant).
     * Le statut initial est EN_ATTENTE (valeur par défaut de la colonne).
     *
     * @param array<int, array{produit_id: int, quantite: int, prix_unitaire: float}> $lignes
     */
    public function creer(int $clientId, array $lignes, float $montantTotal): int;

    /** @return Commande[] Commandes d'un client, la plus récente en premier. */
    public function listerPourClient(int $clientId): array;

    public function trouverPaiement(int $commandeId): ?stdClass;
}