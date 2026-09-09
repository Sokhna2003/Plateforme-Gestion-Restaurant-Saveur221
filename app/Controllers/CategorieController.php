<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Exceptions\ValidationException;
use App\Services\CategorieService;

class CategorieController extends Controller
{
    public function __construct(private CategorieService $categorieService) {}

    public function index(): string
    {
        $motCle = (string) $this->value('q', '');
        $page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
        $vue = isset($_GET['vue']) && $_GET['vue'] === 'carte' ? 'carte' : 'liste';

        $resultat = $this->categorieService->lister($motCle, $page);

        return View::render('categories/index', [
            'categories' => $resultat['categories'],
            'page' => $resultat['page'],
            'totalPages' => $resultat['totalPages'],
            'total' => $resultat['total'],
            'motCle' => $motCle,
            'vue' => $vue,
            'pageTitle' => 'Catégories',
            'baseRoute' => $this->baseRoute(),
        ], 'base');
    }

    public function createForm(): string
    {
        $old = $_SESSION['old'] ?? [];
        $errors = $_SESSION['form_errors'] ?? [];
        unset($_SESSION['old'], $_SESSION['form_errors']);

        return View::render('categories/create', [
            'pageTitle' => 'Ajouter une catégorie',
            'old' => $old,
            'errors' => $errors,
            'baseRoute' => $this->baseRoute(),
        ], 'base');
    }

    public function store(): void
    {
        try {
            $this->categorieService->creer([
                'nom' => $_POST['nom'] ?? '',
                'description' => $_POST['description'] ?? '',
            ], $_FILES['image'] ?? null);
            flash('success', 'Catégorie ajoutée avec succès.');
            View::redirect($this->baseRoute() . '/categories');
        } catch (ValidationException $e) {
            $_SESSION['form_errors'] = $e->getErrors();
            $_SESSION['old'] = $_POST;
            flash('error', $e->getMessage());
            View::redirectBack($this->baseRoute() . '/categories/creer');
        }
    }

    public function editForm(int $id): string
    {
        $categorie = $this->categorieService->trouver($id);
        $old = $_SESSION['old'] ?? [];
        $errors = $_SESSION['form_errors'] ?? [];
        unset($_SESSION['old'], $_SESSION['form_errors']);

        return View::render('categories/edit', [
            'pageTitle' => 'Modifier la catégorie',
            'categorie' => $categorie,
            'old' => $old,
            'errors' => $errors,
            'baseRoute' => $this->baseRoute(),
        ], 'base');
    }

    public function update(int $id): void
    {
        try {
            $this->categorieService->modifier($id, [
                'nom' => $_POST['nom'] ?? '',
                'description' => $_POST['description'] ?? '',
                'image_actuelle' => $_POST['image_actuelle'] ?? null,
            ], $_FILES['image'] ?? null);
            flash('success', 'Catégorie modifiée avec succès.');
            View::redirect($this->baseRoute() . '/categories');
        } catch (ValidationException $e) {
            $_SESSION['form_errors'] = $e->getErrors();
            $_SESSION['old'] = $_POST;
            flash('error', $e->getMessage());
            View::redirectBack($this->baseRoute() . '/categories/' . $id . '/modifier');
        }
    }

    /**
     * @throws \App\Exceptions\NotFoundException
     */
    public function delete(int $id): void
    {
        try {
            $this->categorieService->supprimer($id);
            flash('success', 'Catégorie déplacée dans la corbeille.');
        } catch (ValidationException $e) {
            flash('error', $e->getMessage());
        }
        View::redirect($this->baseRoute() . '/categories');
    }

    /**
     * Retourne la base de route selon la session (/admin ou /gerant).
     */
    private function baseRoute(): string
    {
        return ($_SESSION['user']['role'] ?? '') === 'ADMIN' ? '/admin' : '/gerant';
    }
}