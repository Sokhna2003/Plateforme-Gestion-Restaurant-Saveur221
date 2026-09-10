<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Exceptions\NotFoundException;
use App\Exceptions\ValidationException;
use App\Services\StockService;

class StockController extends Controller
{
    public function __construct(private StockService $stockService) {}

    public function index(): string
    {
        $etat = isset($_GET['etat']) && $_GET['etat'] !== ''
            ? (string) $_GET['etat'] : null;
        $page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;

        $resultat = $this->stockService->lister($etat, $page);

        return View::render('stock/index', [
            'produits' => $resultat['produits'],
            'page' => $resultat['page'],
            'totalPages' => $resultat['totalPages'],
            'total' => $resultat['total'],
            'etat' => $etat ?? '',
            'stats' => $this->stockService->statistiques(),
            'pageTitle' => 'État des stocks',
            'baseRoute' => $this->baseRoute(),
        ], 'base');
    }

    public function reapprovisionner(int $id): void
    {
        try {
            $this->stockService->reapprovisionner($id, (int) ($_POST['quantite'] ?? 0));
            flash('success', 'Stock réapprovisionné avec succès.');
        } catch (ValidationException $e) {
            flash('error', $e->getMessage());
        } catch (NotFoundException) {
            flash('error', 'Ce produit n\'existe pas.');
        }
        View::redirect($this->baseRoute() . '/stock');
    }

    public function modifierSeuil(int $id): void
    {
        try {
            $this->stockService->modifierSeuil($id, (int) ($_POST['seuil'] ?? 0));
            flash('success', 'Seuil d\'alerte mis à jour.');
        } catch (ValidationException $e) {
            flash('error', $e->getMessage());
        } catch (NotFoundException) {
            flash('error', 'Ce produit n\'existe pas.');
        }
        View::redirect($this->baseRoute() . '/stock');
    }

    private function baseRoute(): string
    {
        return ($_SESSION['user']['role'] ?? '') === 'ADMIN' ? '/admin' : '/gerant';
    }
}