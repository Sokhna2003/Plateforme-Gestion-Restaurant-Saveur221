<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Exceptions\NotFoundException;
use App\Exceptions\ValidationException;
use App\Services\PaiementService;

class PaiementController extends Controller
{
    public function __construct(private PaiementService $paiementService) {}

    public function index(): string
    {
        $statut = isset($_GET['statut']) && $_GET['statut'] !== '' ? (string) $_GET['statut'] : null;
        $page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;

        $resultat = $this->paiementService->lister($statut, $page);

        return View::render('paiements/index', [
            'commandes' => $resultat['commandes'],
            'page' => $resultat['page'],
            'totalPages' => $resultat['totalPages'],
            'total' => $resultat['total'],
            'statut' => $statut ?? '',
            'stats' => $this->paiementService->statistiques(),
            'payables' => $this->paiementService->commandesPayables(),
            'pageTitle' => 'Paiements',
            'baseRoute' => $this->baseRoute(),
        ], 'base');
    }

    public function enregistrer(): void
    {
        try {
            $commandeId = (int) ($_POST['commande_id'] ?? 0);
            $montant = (string) ($_POST['montant'] ?? '');
            $mode = (string) ($_POST['mode_paiement'] ?? '');
            $this->paiementService->enregistrer($commandeId, $montant, $mode);
            flash('success', 'Paiement enregistré avec succès.');
        } catch (ValidationException $e) {
            flash('error', $e->getMessage());
        } catch (NotFoundException) {
            flash('error', 'Cette commande n\'existe pas.');
        }

        View::redirect($this->redirectionListe());
    }

    private function baseRoute(): string
    {
        return ($_SESSION['user']['role'] ?? '') === 'ADMIN' ? '/admin' : '/gerant';
    }

    private function redirectionListe(): string
    {
        $filtre = isset($_POST['statut']) && $_POST['statut'] !== '' ? (string) $_POST['statut'] : '';

        $url = $this->baseRoute() . '/paiements';

        return $url . ($filtre !== '' ? '?statut=' . urlencode($filtre) : '');
    }
}