<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\ValidationException;
use App\Interfaces\AvisRepositoryInterface;
use App\Models\Avis;

class AvisService
{
    public function __construct(private AvisRepositoryInterface $avis) {}

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

    public function supprimer(int $id): void
    {
        if ($this->avis->findById($id) === null) {
            throw new ValidationException('Cet avis n\'existe pas.');
        }
        $this->avis->supprimer($id);
    }
}