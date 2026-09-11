<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Exceptions\ValidationException;
use App\Services\AvisService;
use App\Services\ClientService;
use App\Services\CommandeService;
use App\Services\StockService;
use App\Services\UtilisateurService;

class UtilisateurController extends Controller
{
    private const BASE_ROUTE = '/admin';

    public function __construct(
        private UtilisateurService $utilisateurService,
        private ClientService $clientService,
        private CommandeService $commandeService,
        private StockService $stockService,
        private AvisService $avisService,
    ) {}

    public function dashboard(): string
    {
        $estAdmin = ($_SESSION['user']['role'] ?? '') === 'ADMIN';
        $estGerant = in_array($_SESSION['user']['role'] ?? '', ['GERANT', 'ADMIN'], true);

        $donnees = [
            'pageTitle' => 'Dashboard',
            'estAdmin' => $estAdmin,
            'estGerant' => $estGerant,
            'estClient' => false,
        ];

        if ($estAdmin) {
            $donnees = array_merge($donnees, $this->donneesDashboardAdmin());
        } elseif ($estGerant) {
            $donnees = array_merge($donnees, $this->donneesDashboardGerant());
        }

        return View::render('dashboard/dashboard', $donnees, 'base');
    }

    /** @return array<string, mixed> */
    private function donneesDashboardAdmin(): array
    {
        $statsClients = $this->clientService->statistiques();

        return [
            'stats' => [
                'utilisateurs' => $this->utilisateurService->statistiques()['total'],
                'clients' => $statsClients['totalClients'],
                'commandes' => $this->commandeService->total(),
                'chiffreAffaires' => $statsClients['chiffreAffaires'],
                'produits' => $this->stockService->statistiques()['total'],
                'avis' => $this->avisService->statistiques()['total'],
            ],
            'commandesRecentes' => $this->commandeService->recentes(5),
            'clientsRecents' => $this->clientService->lister(null, null, 1, 5)['clients'],
            'avisRecents' => $this->avisService->lister(null, null, 1, 3)['avis'],
            'routeListeCommandes' => self::BASE_ROUTE . '/commandes',
            'routeListeClients' => self::BASE_ROUTE . '/clients',
        ];
    }

    /** @return array<string, mixed> */
    private function donneesDashboardGerant(): array
    {
        $statsCommandes = $this->commandeService->statistiques();
        $statsStock = $this->stockService->statistiques();
        $commandesRecentes = $this->commandeService->recentes(5);
        $base = ($_SESSION['user']['role'] ?? '') === 'ADMIN' ? self::BASE_ROUTE : '/gerant';

        $produitsParCommande = [];
        foreach ($commandesRecentes as $commande) {
            $resume = [];
            foreach ($this->commandeService->lignes($commande->id) as $ligne) {
                $resume[] = $ligne->quantite . '-' . $ligne->produitLibelle;
            }
            $produitsParCommande[$commande->id] = implode(', ', $resume);
        }

        return [
            'stats' => [
                'enAttente' => $statsCommandes['enAttente'],
                'enPreparation' => $statsCommandes['enPreparation'],
                'prete' => $statsCommandes['prete'],
                'stockFaible' => $statsStock['faible'],
                'stockRupture' => $statsStock['rupture'],
                'caJour' => $this->commandeService->chiffreAffairesDuJour(),
            ],
            'commandesRecentes' => $commandesRecentes,
            'produitsParCommande' => $produitsParCommande,
            'routeListeCommandes' => $base . '/commandes',
        ];
    }

    // ---------------------------------------------------------------
    // Gestion des utilisateurs internes (admin)
    // ---------------------------------------------------------------

    public function index(): string
    {
        return $this->rendreGestion(['mode' => '']);
    }

    public function createForm(): string
    {
        return $this->rendreGestion([
            'mode' => 'creer',
            'utilisateur' => null,
            'old' => $_SESSION['old'] ?? [],
            'errors' => $_SESSION['form_errors'] ?? [],
        ], true);
    }

    public function store(): void
    {
        try {
            $this->utilisateurService->creer($this->input());
            flash('success', 'Utilisateur ajouté avec succès.');
            View::redirect(self::BASE_ROUTE . '/utilisateurs');
        } catch (ValidationException $e) {
            $_SESSION['form_errors'] = $e->getErrors();
            $_SESSION['old'] = $_POST;
            flash('error', $e->getMessage());
            View::redirectBack(self::BASE_ROUTE . '/utilisateurs/creer');
        }
    }

    public function editForm(int $id): string
    {
        return $this->rendreGestion([
            'mode' => 'modifier',
            'utilisateur' => $this->utilisateurService->trouver($id),
            'old' => $_SESSION['old'] ?? [],
            'errors' => $_SESSION['form_errors'] ?? [],
        ], true);
    }

    public function update(int $id): void
    {
        try {
            $this->utilisateurService->modifier($id, $this->input(), $this->utilisateurConnecteId());
            flash('success', 'Utilisateur modifié avec succès.');
            View::redirect(self::BASE_ROUTE . '/utilisateurs');
        } catch (ValidationException $e) {
            $_SESSION['form_errors'] = $e->getErrors();
            $_SESSION['old'] = $_POST;
            flash('error', $e->getMessage());
            View::redirectBack(self::BASE_ROUTE . '/utilisateurs/' . $id . '/modifier');
        }
    }

    public function supprimer(int $id): void
    {
        try {
            $this->utilisateurService->supprimer($id, $this->utilisateurConnecteId());
            flash('success', 'Utilisateur supprimé.');
        } catch (\Throwable $e) {
            flash('error', $e->getMessage());
        }
        View::redirect($this->redirectionUtilisateurs());
    }

    public function basculerActif(int $id): void
    {
        try {
            $this->utilisateurService->basculerActif($id, $this->utilisateurConnecteId());
            flash('success', 'Statut du compte mis à jour.');
        } catch (\Throwable $e) {
            flash('error', $e->getMessage());
        }
        View::redirect($this->redirectionUtilisateurs());
    }

    /**
     * @param array<string, mixed> $extras
     */
    private function rendreGestion(array $extras, bool $pillerSession = false): string
    {
        $motCle = (string) $this->value('q', '');
        $roleId = isset($_GET['role_id']) && $_GET['role_id'] !== ''
            ? (int) $_GET['role_id'] : null;
        $actif = isset($_GET['actif']) && in_array((string) $_GET['actif'], ['1', '0'], true)
            ? (string) $_GET['actif'] : null;
        $page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;

        $resultat = $this->utilisateurService->lister($motCle, $roleId, $actif, $page);

        if ($pillerSession) {
            unset($_SESSION['old'], $_SESSION['form_errors']);
        }

        return View::render('utilisateurs/gestion', array_merge([
            'utilisateurs' => $resultat['utilisateurs'],
            'page' => $resultat['page'],
            'totalPages' => $resultat['totalPages'],
            'total' => $resultat['total'],
            'roles' => $this->utilisateurService->roles(),
            'stats' => $this->utilisateurService->statistiques(),
            'motCle' => $motCle,
            'roleId' => $roleId,
            'actif' => $actif,
            'pageTitle' => 'Utilisateurs',
            'baseRoute' => self::BASE_ROUTE,
            'utilisateurConnecteId' => $this->utilisateurConnecteId(),
        ], $extras), 'base');
    }

    private function redirectionUtilisateurs(): string
    {
        $params = $_GET;
        unset($params['_token'], $params['page']);
        return self::BASE_ROUTE . '/utilisateurs' . ($params ? '?' . http_build_query($params) : '');
    }

    private function utilisateurConnecteId(): int
    {
        return (int) ($_SESSION['user']['id'] ?? 0);
    }
}