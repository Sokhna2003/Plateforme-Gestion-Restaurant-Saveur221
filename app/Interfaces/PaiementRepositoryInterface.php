<?php

declare(strict_types=1);

namespace App\Interfaces;

use App\Models\PaiementCommande;

interface PaiementRepositoryInterface
{
    /**
     * Liste paginée des commandes avec leur état de paiement.
     *
     * @param string|null $statut ''|null = tous, sinon payee | impayee | partielle
     * @return array{commandes: PaiementCommande[], page: int, totalPages: int, total: int}
     */
    public function paginer(?string $statut, int $page, int $perPage): array;

    /**
     * Statistiques pour les cartes : total encaissé, nb impayées, nb partiellement payées.
     *
     * @return array{totalEncaise: float, impayees: int, partiellement: int}
     */
    public function statistiques(): array;

    /**
     * Commandes avec un reste à payer (impayées ou partiellement payées),
     * utilisées dans le sélecteur du modal d'enregistrement.
     *
     * @return PaiementCommande[]
     */
    public function commandesPayables(): array;

    /**
     * Résumé d'une commande avec son montant déjà payé.
     */
    public function resume(int $commandeId): ?PaiementCommande;

    /**
     * Enregistre un paiement (INSERT).
     */
    public function payer(int $commandeId, float $montant, string $mode): void;
}