<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Services\ProduitService;

class HomeController extends Controller
{
    private ProduitService $produitService;

    public function __construct()
    {
        $this->produitService = new ProduitService();
    }

    public function index(): void
    {
        $categories = $this->produitService->listerCategories();
        $populaires = $this->produitService->listerPopulaires(4);

        $this->view('home.index', [
            'categories' => $categories,
            'populaires' => $populaires,
            'pageTitle' => 'Accueil',
            'activeNav' => 'accueil',
        ]);
    }
}
