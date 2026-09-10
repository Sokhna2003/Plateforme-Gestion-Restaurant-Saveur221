<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Exceptions\ValidationException;
use App\Models\Avis;
use App\Services\AvisService;

class AvisController extends Controller
{
    private const BASE_ROUTE = '/admin';

    public function __construct(private AvisService $avisService) {}

    public function index(): string
    {
        $motCle = trim((string) $this->value('q', ''));
        $note = isset($_GET['note']) && ctype_digit((string) $_GET['note'])
            && (int) $_GET['note'] >= Avis::NOTE_MIN && (int) $_GET['note'] <= Avis::NOTE_MAX
            ? (int) $_GET['note'] : null;
        $page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;

        $resultat = $this->avisService->lister($motCle !== '' ? $motCle : null, $note, $page);

        return View::render('avis/gestion', [
            'avis' => $resultat['avis'],
            'page' => $resultat['page'],
            'totalPages' => $resultat['totalPages'],
            'total' => $resultat['total'],
            'stats' => $this->avisService->statistiques(),
            'motCle' => $motCle,
            'note' => $note,
            'pageTitle' => 'Avis',
            'baseRoute' => self::BASE_ROUTE,
        ], 'base');
    }

    public function detail(int $id): string
    {
        $avis = $this->avisService->trouver($id);
        if ($avis === null) {
            flash('error', 'Cet avis n\'existe pas.');
            View::redirect(self::BASE_ROUTE . '/avis');
        }

        return View::render('avis/detail', [
            'avis' => $avis,
            'pageTitle' => 'Avis n°' . $id,
            'baseRoute' => self::BASE_ROUTE,
        ], 'base');
    }

    public function supprimer(int $id): void
    {
        try {
            $this->avisService->supprimer($id);
            flash('success', 'Avis supprimé.');
        } catch (ValidationException $e) {
            flash('error', $e->getMessage());
        }
        View::redirect($this->redirectionAvis());
    }

    private function redirectionAvis(): string
    {
        $params = $_GET;
        unset($params['_token'], $params['page']);
        return self::BASE_ROUTE . '/avis' . ($params ? '?' . http_build_query($params) : '');
    }
}