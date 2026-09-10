<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Exceptions\ValidationException;
use App\Services\UtilisateurService;

class UtilisateurController extends Controller
{
    private const BASE_ROUTE = '/admin';

    public function __construct(private UtilisateurService $utilisateurService) {}

    public function dashboard(): string
    {
        $estAdmin = ($_SESSION['user']['role'] ?? '') === 'ADMIN';
        $estGerant = in_array($_SESSION['user']['role'] ?? '', ['GERANT', 'ADMIN'], true);

        return View::render('dashboard/dashboard', [
            'pageTitle' => 'Dashboard',
            'estAdmin' => $estAdmin,
            'estGerant' => $estGerant,
        ], 'base');
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