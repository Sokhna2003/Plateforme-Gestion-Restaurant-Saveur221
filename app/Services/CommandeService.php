<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;
use App\Exceptions\NotFoundException;
use App\Exceptions\ValidationException;
use App\Interfaces\CommandeRepositoryInterface;
use App\Interfaces\PaiementRepositoryInterface;
use App\Interfaces\ProduitRepositoryInterface;
use App\Models\Commande;
use App\Models\LigneCommande;

/**
 * Couche metier des commandes.
 */
class CommandeService
{
    public const PER_PAGE = 8;

    /** Modes de paiement proposés au passage de commande. */
    public const MODES_CHECKOUT = ['Espèces', 'Wave', 'Orange Money', 'Free Money', 'Carte bancaire', 'Virement'];

    /** Modes réglés immédiatement au checkout (les autres = paiement à la remise). */
    public const MODES_PAIEMENT_IMMEDIAT = ['Wave', 'Orange Money', 'Free Money', 'Carte bancaire', 'Virement'];

    public function __construct(
        private CommandeRepositoryInterface $commandeRepository,
        private ProduitRepositoryInterface $produitRepository,
        private PaiementRepositoryInterface $paiementRepository,
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

    public function total(): int
    {
        return $this->commandeRepository->compterTotal();
    }

    public function chiffreAffairesDuJour(): float
    {
        return $this->commandeRepository->chiffreAffairesJour();
    }

    /** @return Commande[] */
    public function recentes(int $limite = 5): array
    {
        return $this->commandeRepository->paginer(null, null, 1, $limite);
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

    /**
     * Commandes d'un client, la plus récente en premier.
     *
     * @return Commande[]
     */
    public function pourClient(int $clientId): array
    {
        return $this->commandeRepository->listerPourClient($clientId);
    }

    /**
     * Place une commande à partir du panier : crée la commande et ses lignes,
     * décrémente les stocks (blocage si stock insuffisant) et enregistre le
     * paiement immédiat si le mode choisi le requiert. Le tout en transaction.
     *
     * @param array<int, int> $quantites Produit id => quantité
     * @return array{id: int, montant: float, modePaiement: string, paye: bool}
     * @throws ValidationException
     */
    public function placerCommande(int $clientId, array $quantites, string $modePaiement): array
    {
        $quantites = array_filter($quantites, static fn (int $quantite): bool => $quantite > 0);
        if ($quantites === []) {
            throw new ValidationException('Votre panier est vide.');
        }

        $modePaiement = trim($modePaiement);
        if (!in_array($modePaiement, self::MODES_CHECKOUT, true)) {
            throw new ValidationException('Veuillez choisir un mode de paiement valide.');
        }

        $produits = $this->produitRepository->findByIds(array_keys($quantites));
        if (count($produits) !== count($quantites)) {
            throw new ValidationException('Certains produits de votre panier n\'existent plus.');
        }

        $lignes = [];
        $montantTotal = 0.0;
        foreach ($produits as $produit) {
            $quantite = $quantites[$produit->id];
            if (!$produit->estDisponible() || $produit->quantiteStock < $quantite) {
                throw new ValidationException(
                    'Stock insuffisant pour « ' . $produit->libelle . ' » (disponible : '
                    . $produit->quantiteStock . ').'
                );
            }
            $montantTotal += $produit->prix * $quantite;
            $lignes[] = [
                'produit_id' => $produit->id,
                'quantite' => $quantite,
                'prix_unitaire' => $produit->prix,
            ];
        }

        $paye = in_array($modePaiement, self::MODES_PAIEMENT_IMMEDIAT, true);

        $pdo = Database::getInstance()->getConnection();
        $pdo->beginTransaction();
        try {
            $commandeId = $this->commandeRepository->creer($clientId, $lignes, $montantTotal);

            foreach ($lignes as $ligne) {
                if (!$this->produitRepository->decrementerStockSiDisponible($ligne['produit_id'], $ligne['quantite'])) {
                    throw new ValidationException('Stock insuffisant pour un des produits de votre panier.');
                }
            }

            if ($paye) {
                $this->paiementRepository->payer($commandeId, $montantTotal, $modePaiement);
            }

            $pdo->commit();
        } catch (\Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $e;
        }

        return [
            'id' => $commandeId,
            'montant' => $montantTotal,
            'modePaiement' => $modePaiement,
            'paye' => $paye,
        ];
    }

    private function normaliserStatut(?string $statut): ?string
    {
        if ($statut !== null && in_array($statut, Commande::statuts(), true)) {
            return $statut;
        }
        return null;
    }
}