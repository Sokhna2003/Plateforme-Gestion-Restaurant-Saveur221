<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Exceptions\NotFoundException;
use App\Services\ProduitService;

class ProduitController extends Controller
{
    public function __construct(private ProduitService $produitService) {}

    public function index(): string
    {
        $motCle = $this->value('q');
        $categorieId = isset($_GET['categorie']) ? (int) $_GET['categorie'] : null;
        $page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;

        $resultat = $this->produitService->lister($motCle, $categorieId, $page, 8);
        $categories = $this->produitService->listerCategories();

        return View::render('produits/index', [
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

    public function detail(int $id): string
    {
        $resultat = $this->produitService->detail($id);

        if ($resultat === null) {
            throw new NotFoundException();
        }

        return View::render('produits/detail', [
            'produit' => $resultat['produit'],
            'similaires' => $resultat['similaires'],
            'pageTitle' => $resultat['produit']->libelle,
            'activeNav' => 'menu',
        ]);
    }
}
