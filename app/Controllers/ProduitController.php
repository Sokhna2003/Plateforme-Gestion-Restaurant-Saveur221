<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Services\ProduitService;

class ProduitController extends Controller
{
    private ProduitService $produitService;

    public function __construct()
    {
        $this->produitService = new ProduitService();
    }

    public function index(): void
    {
        $motCle = $_GET['q'] ?? null;
        $categorieId = isset($_GET['categorie']) ? (int) $_GET['categorie'] : null;
        $page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;

        $resultat = $this->produitService->lister($motCle, $categorieId, $page, 8);
        $categories = $this->produitService->listerCategories();

        $this->view('produits.index', [
            'produits' => $resultat['produits'],
            'page' => $resultat['page'],
            'totalPages' => $resultat['totalPages'],
            'categories' => $categories,
            'motCle' => $motCle,
            'categorieId' => $categorieId,
            'pageTitle' => 'Menu',
            'activeNav' => 'menu',
        ]);
    }

    public function detail(string $id): void
    {
        $resultat = $this->produitService->detail((int) $id);

        if ($resultat === null) {
            http_response_code(404);
            echo "Produit introuvable.";
            return;
        }

        $this->view('produits.detail', [
            'produit' => $resultat['produit'],
            'similaires' => $resultat['similaires'],
            'pageTitle' => $resultat['produit']->libelle,
            'activeNav' => 'menu',
        ]);
    }
}
