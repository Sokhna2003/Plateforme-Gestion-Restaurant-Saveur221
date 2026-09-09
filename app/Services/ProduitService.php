<?php

declare(strict_types=1);

namespace App\Services;

use App\Interfaces\CategorieRepositoryInterface;
use App\Interfaces\ProduitRepositoryInterface;

/**
 * Couche metier au-dessus des Repositories : le Controller ne parle jamais
 * directement a PDO, il passe par ce Service.
 */
class ProduitService
{
    public function __construct(
        private ProduitRepositoryInterface $produitRepository,
        private CategorieRepositoryInterface $categorieRepository,
    ) {}

    /**
     * Liste paginee du catalogue, avec recherche et filtre par categorie
     * optionnels.
     *
     * @return array{produits: array, page: int, totalPages: int}
     */
    public function lister(?string $motCle = null, ?int $categorieId = null, int $page = 1, int $perPage = 8): array
    {
        $total = $this->produitRepository->compter($categorieId, $motCle);
        $totalPages = max(1, (int) ceil($total / $perPage));
        $page = max(1, min($page, $totalPages));

        $produits = $this->produitRepository->paginate($page, $perPage, $categorieId, $motCle);

        return [
            'produits' => $produits,
            'page' => $page,
            'totalPages' => $totalPages,
        ];
    }

    /** @return \App\Models\Categorie[] */
    public function listerCategories(): array
    {
        return $this->categorieRepository->all();
    }

    /** @return \App\Models\Produit[] */
    public function listerPopulaires(int $limite = 4): array
    {
        return $this->produitRepository->paginate(1, $limite);
    }

    /**
     * Detail d'un produit + une selection de produits similaires.
     * Retourne null si le produit n'existe pas.
     *
     * @return array{produit: \App\Models\Produit, similaires: array}|null
     */
    public function detail(int $id): ?array
    {
        $produit = $this->produitRepository->findById($id);
        if ($produit === null) {
            return null;
        }

        $similaires = $this->produitRepository->similaires($produit->categorieId, $id, 3);

        return [
            'produit' => $produit,
            'similaires' => $similaires,
        ];
    }
}
