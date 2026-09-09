<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Services\TrashService;

class CorbeilleController extends Controller
{
    public function __construct(private TrashService $trashService) {}

    public function index(): string
    {
        $entite = $_GET['entite'] ?? null;
        $motCle = (string) $this->value('q', '');

        if (!$this->trashService->estEntiteValide($entite)) {
            $entite = null;
        }

        return View::render('corbeille/index', [
            'items' => $this->trashService->lister($entite, $motCle),
            'entites' => TrashService::entites(),
            'entiteActive' => $entite ?? 'tout',
            'motCle' => $motCle,
            'pageTitle' => 'Corbeille',
            'baseRoute' => $this->baseRoute(),
        ], 'base');
    }

    public function restaurer(string $entite, int $id): void
    {
        try {
            $this->trashService->restaurer($entite, $id);
            flash('success', 'Élément restauré avec succès.');
        } catch (\Throwable $e) {
            flash('error', 'Impossible de restaurer cet élément.');
        }
        View::redirect($this->baseRoute() . '/corbeille?entite=' . $entite);
    }

    public function supprimerDefinitivement(string $entite, int $id): void
    {
        try {
            $this->trashService->supprimerDefinitivement($entite, $id);
            flash('success', 'Élément supprimé définitivement.');
        } catch (\Throwable $e) {
            $message = method_exists($e, 'getMessage') ? $e->getMessage() : 'Opération impossible.';
            flash('error', $message);
        }
        View::redirect($this->baseRoute() . '/corbeille?entite=' . $entite);
    }

    /**
     * Retourne la base de route selon la session (/admin ou /gerant).
     */
    private function baseRoute(): string
    {
        return ($_SESSION['user']['role'] ?? '') === 'ADMIN' ? '/admin' : '/gerant';
    }
}