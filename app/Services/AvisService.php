<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\NotFoundException;
use App\Exceptions\ValidationException;
use App\Interfaces\AvisRepositoryInterface;
use App\Models\Avis;
use App\Models\Commande;

class AvisService
{
    public const COMMENTAIRE_MAX = 500;

    public function __construct(
        private AvisRepositoryInterface $avis,
        private CommandeService $commandeService,
    ) {}

    /**
     * @return array{avis: Avis[], total: int, page: int, totalPages: int}
     */
    public function lister(?string $terme = null, ?int $note = null, int $page = 1, int $perPage = 8): array
    {
        $total = $this->avis->compterGestion($terme, $note);

        return [
            'avis' => $this->avis->paginer($terme, $note, $page, $perPage),
            'total' => $total,
            'page' => $page,
            'totalPages' => max(1, (int) ceil($total / $perPage)),
        ];
    }

    /** @return array<string, mixed> */
    public function statistiques(): array
    {
        return [
            'total' => $this->avis->count(),
            'noteMoyenne' => $this->avis->noteMoyenne(),
            'cinqEtoiles' => $this->avis->compterNote(Avis::NOTE_MAX),
            'avecCommentaire' => $this->avis->compterAvecCommentaire(),
        ];
    }

    public function trouver(int $id): ?Avis
    {
        return $this->avis->findById($id);
    }

    public function trouverPourCommande(int $commandeId): ?Avis
    {
        return $this->avis->trouverPourCommande($commandeId);
    }

    /**
     * Enregistre l'avis d'un client sur une commande retirée.
     *
     * @throws ValidationException
     */
    public function creer(int $clientId, int $commandeId, int $note, ?string $commentaire): void
    {
        if ($note < Avis::NOTE_MIN || $note > Avis::NOTE_MAX) {
            throw new ValidationException('La note doit être comprise entre 1 et 5.');
        }

        $commentaire = trim((string) $commentaire);
        if (mb_strlen($commentaire) > self::COMMENTAIRE_MAX) {
            throw new ValidationException('Le commentaire ne doit pas dépasser ' . self::COMMENTAIRE_MAX . ' caractères.');
        }

        try {
            $commande = $this->commandeService->trouver($commandeId);
        } catch (NotFoundException) {
            throw new ValidationException('Cette commande n\'existe pas.');
        }

        if ($commande->clientId !== $clientId) {
            throw new ValidationException('Vous ne pouvez pas laisser d\'avis sur cette commande.');
        }

        if ($commande->statut !== Commande::STATUT_RETIREE) {
            throw new ValidationException('Vous pouvez laisser un avis uniquement après avoir retiré votre commande.');
        }

        if ($this->avis->trouverPourCommande($commandeId) !== null) {
            throw new ValidationException('Vous avez déjà laissé un avis pour cette commande.');
        }

        $this->avis->creer($clientId, $commandeId, $note, $commentaire !== '' ? $commentaire : null);
    }

    public function supprimer(int $id): void
    {
        if ($this->avis->findById($id) === null) {
            throw new ValidationException('Cet avis n\'existe pas.');
        }
        $this->avis->supprimer($id);
    }
}