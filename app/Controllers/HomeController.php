<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Services\ProduitService;

class HomeController extends Controller
{
    public function __construct(private ProduitService $produitService) {}

    public function index(): string
    {
        $categories = $this->produitService->listerCategories();
        $populaires = $this->produitService->listerPopulaires(4);

        return View::render('home/index', [
            'categories' => $categories,
            'populaires' => $populaires,
            'pageTitle' => 'Accueil',
            'activeNav' => 'accueil',
        ]);
    }
}
