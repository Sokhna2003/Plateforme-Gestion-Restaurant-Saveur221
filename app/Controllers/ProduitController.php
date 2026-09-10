<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Exceptions\NotFoundException;
use App\Exceptions\ValidationException;
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

    // ---------------------------------------------------------------
    // Espace d'administration (admin + gerant)
    // ---------------------------------------------------------------

    public function gestion(): string
    {
        return $this->rendreGestion(['mode' => '']);
    }

    public function createForm(): string
    {
        return $this->rendreGestion([
            'mode' => 'creer',
            'old' => $_SESSION['old'] ?? [],
            'errors' => $_SESSION['form_errors'] ?? [],
            'produit' => null,
        ], true);
    }

    public function store(): void
    {
        try {
            $this->produitService->creer($this->input(), $_FILES['image'] ?? null);
            flash('success', 'Produit ajouté avec succès.');
            View::redirect($this->baseRoute() . '/produits');
        } catch (ValidationException $e) {
            $_SESSION['form_errors'] = $e->getErrors();
            $_SESSION['old'] = $_POST;
            flash('error', $e->getMessage());
            View::redirectBack($this->baseRoute() . '/produits/creer');
        }
    }

    public function editForm(int $id): string
    {
        return $this->rendreGestion([
            'mode' => 'modifier',
            'produit' => $this->produitService->trouver($id),
            'old' => $_SESSION['old'] ?? [],
            'errors' => $_SESSION['form_errors'] ?? [],
        ], true);
    }

    public function update(int $id): void
    {
        try {
            $this->produitService->modifier($id, $this->input(), $_FILES['image'] ?? null);
            flash('success', 'Produit modifié avec succès.');
            View::redirect($this->baseRoute() . '/produits');
        } catch (ValidationException $e) {
            $_SESSION['form_errors'] = $e->getErrors();
            $_SESSION['old'] = $_POST;
            flash('error', $e->getMessage());
            View::redirectBack($this->baseRoute() . '/produits/' . $id . '/modifier');
        }
    }

    public function delete(int $id): void
    {
        try {
            $this->produitService->supprimer($id);
            flash('success', 'Produit déplacé dans la corbeille.');
        } catch (\Throwable $e) {
            flash('error', $e->getMessage());
        }
        View::redirect($this->redirectionProduits());
    }

    public function basculerDisponibilite(int $id): void
    {
        try {
            $this->produitService->basculerDisponibilite($id);
            flash('success', 'Disponibilité mise à jour.');
        } catch (\Throwable $e) {
            flash('error', 'Impossible de modifier la disponibilité.');
        }
        View::redirect($this->redirectionProduits());
    }

    /**
     * @param array<string, mixed> $extras
     */
    private function rendreGestion(array $extras, bool $pillerSession = false): string
    {
        $motCle = (string) $this->value('q', '');
        $categorieId = isset($_GET['categorie']) && $_GET['categorie'] !== ''
            ? (int) $_GET['categorie'] : null;
        $disponible = isset($_GET['disponible']) && ($_GET['disponible'] === '1' || $_GET['disponible'] === '0')
            ? (string) $_GET['disponible'] : null;
        $page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
        $vue = isset($_GET['vue']) && $_GET['vue'] === 'carte' ? 'carte' : 'liste';

        $resultat = $this->produitService->listerAdministration($motCle, $categorieId, $disponible, $page);

        if ($pillerSession) {
            unset($_SESSION['old'], $_SESSION['form_errors']);
        }

        return View::render('produits/gestion', array_merge([
            'produits' => $resultat['produits'],
            'page' => $resultat['page'],
            'totalPages' => $resultat['totalPages'],
            'total' => $resultat['total'],
            'categories' => $this->produitService->listerCategories(),
            'motCle' => $motCle,
            'categorieId' => $categorieId,
            'disponible' => $disponible,
            'vue' => $vue,
            'pageTitle' => 'Produits',
            'baseRoute' => $this->baseRoute(),
        ], $extras), 'base');
    }

    private function redirectionProduits(): string
    {
        $params = $_GET;
        unset($params['_token'], $params['page']);
        return $this->baseRoute() . '/produits' . ($params ? '?' . http_build_query($params) : '');
    }

    private function baseRoute(): string
    {
        return ($_SESSION['user']['role'] ?? '') === 'ADMIN' ? '/admin' : '/gerant';
    }
}