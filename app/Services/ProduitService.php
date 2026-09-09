<?php

namespace App\Services;

use App\Models\CategorieModel;
use App\Models\ProduitModel;

/**
 * Couche metier au-dessus des Models : le Controller ne parle jamais
 * directement a PDO ni aux Models, il passe par ce Service.
 */
class ProduitService
{
    private ProduitModel $produitModel;
    private CategorieModel $categorieModel;

    public function __construct()
    {
        $this->produitModel = new ProduitModel();
        $this->categorieModel = new CategorieModel();
    }

    /**
     * Liste paginee du catalogue, avec recherche et filtre par categorie
     * optionnels.
     *
     * @return array{produits: array, page: int, totalPages: int}
     */
    public function lister(?string $motCle = null, ?int $categorieId = null, int $page = 1, int $perPage = 8): array
    {
        $total = $this->produitModel->compter($categorieId, $motCle);
        $totalPages = max(1, (int) ceil($total / $perPage));
        $page = max(1, min($page, $totalPages));

        $produits = $this->produitModel->paginate($page, $perPage, $categorieId, $motCle);

        return [
            'produits' => $produits,
            'page' => $page,
            'totalPages' => $totalPages,
        ];
    }

    public function listerCategories(): array
    {
        return $this->categorieModel->all();
    }

    public function listerPopulaires(int $limite = 4): array
    {
        return $this->produitModel->paginate(1, $limite);
    }

    /**
     * Detail d'un produit + une selection de produits similaires.
     * Retourne null si le produit n'existe pas.
     */
    public function detail(int $id): ?array
    {
        $produit = $this->produitModel->find($id);
        if ($produit === null) {
            return null;
        }

        $similaires = $this->produitModel->similaires((int) $produit->categorie_id, $id, 3);

        return [
            'produit' => $produit,
            'similaires' => $similaires,
        ];
    }
}