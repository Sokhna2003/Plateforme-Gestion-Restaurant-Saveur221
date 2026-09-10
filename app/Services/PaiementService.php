<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\NotFoundException;
use App\Exceptions\ValidationException;
use App\Interfaces\PaiementRepositoryInterface;
use App\Models\PaiementCommande;

/**
 * Couche metier des paiements des commandes.
 */
class PaiementService
{
    public const PER_PAGE = 8;

    /** Modes de paiement proposés lors de l'enregistrement. */
    public const MODES = ['Espèces', 'Wave', 'Orange Money', 'Free Money', 'Carte bancaire', 'Virement'];

    public function __construct(
        private PaiementRepositoryInterface $paiementRepository,
    ) {}

    /**
     * Liste paginée des commandes avec leur état de paiement.
     *
     * @return array{commandes: PaiementCommande[], page: int, totalPages: int, total: int}
     */
    public function lister(?string $statut = null, int $page = 1): array
    {
        $statut = $this->normaliserStatut($statut);
        return $this->paiementRepository->paginer($statut, max(1, $page), self::PER_PAGE);
    }

    /**
     * Compteurs pour les trois cartes du haut.
     *
     * @return array{totalEncaise: float, impayees: int, partiellement: int}
     */
    public function statistiques(): array
    {
        return $this->paiementRepository->statistiques();
    }

    /**
     * Commandes avec un reste à payer (liste du sélecteur du modal).
     *
     * @return PaiementCommande[]
     */
    public function commandesPayables(): array
    {
        return $this->paiementRepository->commandesPayables();
    }

    /**
     * Enregistre un paiement pour une commande.
     *
     * @throws NotFoundException
     * @throws ValidationException
     */
    public function enregistrer(int $commandeId, string|int|float $montant, string $mode): void
    {
        $commande = $this->paiementRepository->resume($commandeId);
        if ($commande === null) {
            throw new NotFoundException();
        }

        $mode = trim($mode);
        if ($mode === '') {
            throw new ValidationException('Veuillez choisir un mode de paiement.');
        }

        if (!is_numeric($montant) || (float) $montant <= 0) {
            throw new ValidationException('Le montant du paiement doit être un nombre positif.');
        }

        $restant = $commande->montantRestant();
        if ($restant <= 0) {
            throw new ValidationException('Cette commande est déjà entièrement payée.');
        }

        $montant = (float) $montant;
        if ($montant > $restant) {
            throw new ValidationException(
                'Le montant dépasse le reste à payer de ' . number_format($restant, 0, ',', ' ') . ' FCFA.'
            );
        }

        $this->paiementRepository->payer($commandeId, $montant, $mode);
    }

    private function normaliserStatut(?string $statut): ?string
    {
        if ($statut !== null && in_array($statut, [
            PaiementCommande::STATUT_PAYEE,
            PaiementCommande::STATUT_IMPAYEE,
            PaiementCommande::STATUT_PARTIEL,
        ], true)) {
            return $statut;
        }
        return null;
    }
}