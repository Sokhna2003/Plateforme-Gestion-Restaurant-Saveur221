<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\NotFoundException;
use App\Exceptions\ValidationException;
use App\Interfaces\CommandeRepositoryInterface;
use App\Models\Commande;
use App\Models\LigneCommande;

/**
 * Couche metier des commandes.
 */
class CommandeService
{
    public const PER_PAGE = 8;

    public function __construct(
        private CommandeRepositoryInterface $commandeRepository,
    ) {}

    /**
     * Liste paginee, avec recherche (client ou produit) et filtre par statut.
     *
     * @return array{commandes: Commande[], page: int, totalPages: int, total: int}
     */
    public function lister(?string $terme = null, ?string $statut = null, int $page = 1): array
    {
        $statut = $this->normaliserStatut($statut);

        $total = $this->commandeRepository->compter($terme, $statut);
        $totalPages = max(1, (int) ceil($total / self::PER_PAGE));
        $page = max(1, min($page, $totalPages));

        $commandes = $this->commandeRepository->paginer($terme, $statut, $page, self::PER_PAGE);

        return [
            'commandes' => $commandes,
            'page' => $page,
            'totalPages' => $totalPages,
            'total' => $total,
        ];
    }

    /**
     * Compteurs pour les trois cartes du haut.
     *
     * @return array{enAttente: int, enPreparation: int, prete: int}
     */
    public function statistiques(): array
    {
        return [
            'enAttente' => $this->commandeRepository->compterParStatut(Commande::STATUT_EN_ATTENTE),
            'enPreparation' => $this->commandeRepository->compterParStatut(Commande::STATUT_EN_PREPARATION),
            'prete' => $this->commandeRepository->compterParStatut(Commande::STATUT_PRETE),
        ];
    }

    /**
     * @throws NotFoundException
     */
    public function trouver(int $id): Commande
    {
        $commande = $this->commandeRepository->findById($id);
        if ($commande === null) {
            throw new NotFoundException();
        }
        return $commande;
    }

    /** @return LigneCommande[] */
    public function lignes(int $commandeId): array
    {
        return $this->commandeRepository->lignes($commandeId);
    }

    /** @return array<string, mixed>|null */
    public function paiement(int $commandeId): ?array
    {
        $paiement = $this->commandeRepository->trouverPaiement($commandeId);
        if ($paiement === null) {
            return null;
        }
        return get_object_vars($paiement);
    }

    /**
     * Fait avancer la commande vers le statut suivant (ex: En attente -> En préparation).
     *
     * @throws NotFoundException
     * @throws ValidationException
     */
    public function passerAuStatutSuivant(int $id, string $statutDemande): void
    {
        $commande = $this->trouver($id);

        if ($commande->statutSuivant() === null) {
            throw new ValidationException(
                'Ce statut ne peut plus être modifié (' . $commande->libelleStatut() . ').'
            );
        }

        if ($statutDemande !== $commande->statutSuivant()) {
            throw new ValidationException('Transition de statut invalide.');
        }

        $this->commandeRepository->modifierStatut($id, $statutDemande);
    }

    /**
     * @throws NotFoundException
     * @throws ValidationException
     */
    public function annuler(int $id): void
    {
        $commande = $this->trouver($id);

        if (!$commande->peutAnnuler()) {
            throw new ValidationException(
                'Cette commande ne peut plus être annulée (' . $commande->libelleStatut() . ').'
            );
        }

        $this->commandeRepository->modifierStatut($id, Commande::STATUT_ANNULEE);
    }

    private function normaliserStatut(?string $statut): ?string
    {
        if ($statut !== null && in_array($statut, Commande::statuts(), true)) {
            return $statut;
        }
        return null;
    }
}