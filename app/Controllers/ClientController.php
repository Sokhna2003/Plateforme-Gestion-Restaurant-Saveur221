<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Exceptions\ValidationException;
use App\Services\ClientService;

class ClientController extends Controller
{
    private const BASE_ROUTE = '/admin';

    public function __construct(private ClientService $clientService) {}

    public function dashboard(): string
    {
        return View::render('dashboard/dashboard', [
            'pageTitle' => 'Mon espace',
            'estAdmin' => false,
            'estGerant' => false,
            'estClient' => true,
        ], 'client');
    }

    // ---------------------------------------------------------------
    // Gestion des clients (admin)
    // ---------------------------------------------------------------

    public function index(): string
    {
        $motCle = trim((string) $this->value('q', ''));
        $avecCommandes = isset($_GET['commandes']) && in_array((string) $_GET['commandes'], ['avec', 'sans'], true)
            ? (string) $_GET['commandes'] : null;
        $page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;

        $resultat = $this->clientService->lister($motCle !== '' ? $motCle : null, $avecCommandes, $page);

        return View::render('clients/gestion', [
            'clients' => $resultat['clients'],
            'page' => $resultat['page'],
            'totalPages' => $resultat['totalPages'],
            'total' => $resultat['total'],
            'stats' => $this->clientService->statistiques(),
            'motCle' => $motCle,
            'avecCommandes' => $avecCommandes ?? '',
            'pageTitle' => 'Clients',
            'baseRoute' => self::BASE_ROUTE,
        ], 'base');
    }

    public function detail(int $id): string
    {
        $client = $this->clientService->trouver($id);
        if ($client === null) {
            flash('error', 'Ce client n\'existe pas.');
            View::redirect(self::BASE_ROUTE . '/clients');
        }

        $commandes = $this->clientService->commandesPour($id);

        return View::render('clients/detail', [
            'client' => $client,
            'commandes' => $commandes,
            'pageTitle' => 'Client – ' . $client->nomComplet(),
            'baseRoute' => self::BASE_ROUTE,
        ], 'base');
    }

    public function supprimer(int $id): void
    {
        try {
            $this->clientService->supprimer($id);
            flash('success', 'Client supprimé.');
        } catch (ValidationException $e) {
            flash('error', $e->getMessage());
        }
        View::redirect($this->redirectionClients());
    }

    private function redirectionClients(): string
    {
        $params = $_GET;
        unset($params['_token'], $params['page']);
        return self::BASE_ROUTE . '/clients' . ($params ? '?' . http_build_query($params) : '');
    }
}