<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\NotFoundException;
use App\Exceptions\ValidationException;
use App\Interfaces\ProduitRepositoryInterface;
use App\Models\Produit;

/**
 * Gestion de l'état des stocks (inventaire de la cuisine).
 */
class StockService
{
    public const PER_PAGE = 8;

    private const ETATS = ['normal', 'faible', 'rupture'];

    public function __construct(
        private ProduitRepositoryInterface $produitRepository,
    ) {}

    /**
     * Chiffres clés pour les cartes statistiques.
     *
     * @return array{total: int, faible: int, rupture: int}
     */
    public function statistiques(): array
    {
        return [
            'total' => $this->produitRepository->compterStock(),
            'faible' => $this->produitRepository->compterStock('faible'),
            'rupture' => $this->produitRepository->compterStock('rupture'),
        ];
    }

    /**
     * Inventaire paginé, avec filtre optionnel par état de stock.
     *
     * @return array{produits: Produit[], page: int, totalPages: int, total: int}
     */
    public function lister(?string $etat = null, int $page = 1, int $perPage = self::PER_PAGE): array
    {
        $etat = $this->normaliserEtat($etat);

        $total = $this->produitRepository->compterStock($etat);
        $totalPages = max(1, (int) ceil($total / $perPage));
        $page = max(1, min($page, $totalPages));

        $produits = $this->produitRepository->paginerStock($etat, $page, $perPage);

        return [
            'produits' => $produits,
            'page' => $page,
            'totalPages' => $totalPages,
            'total' => $total,
        ];
    }

    /**
     * Ajoute une quantité au stock d'un produit.
     *
     * @throws NotFoundException
     * @throws ValidationException
     */
    public function reapprovisionner(int $id, int $quantite): void
    {
        $this->trouver($id);

        if ($quantite < 1) {
            throw new ValidationException('La quantité doit être un entier supérieur à 0.', [
                'quantite' => 'La quantité ne peut pas être vide ni négative.',
            ]);
        }

        $this->produitRepository->incrementStock($id, $quantite);
    }

    /**
     * Modifie le seuil d'alerte d'un produit.
     *
     * @throws NotFoundException
     * @throws ValidationException
     */
    public function modifierSeuil(int $id, int $seuil): void
    {
        $this->trouver($id);

        if ($seuil < 0) {
            throw new ValidationException('Le seuil d\'alerte doit être un entier positif.', [
                'seuil' => 'Le seuil ne peut pas être négatif.',
            ]);
        }

        $this->produitRepository->definirSeuil($id, $seuil);
    }

    /**
     * @throws NotFoundException
     */
    private function trouver(int $id): Produit
    {
        $produit = $this->produitRepository->findById($id);
        if ($produit === null) {
            throw new NotFoundException();
        }
        return $produit;
    }

    private function normaliserEtat(?string $etat): ?string
    {
        if ($etat !== null && in_array($etat, self::ETATS, true)) {
            return $etat;
        }
        return null;
    }
}