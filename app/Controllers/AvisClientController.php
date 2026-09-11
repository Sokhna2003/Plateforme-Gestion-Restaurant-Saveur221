<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Exceptions\NotFoundException;
use App\Exceptions\ValidationException;
use App\Models\Commande;
use App\Services\AvisService;
use App\Services\CommandeService;

/**
 * Formulaire et enregistrement d'un avis client.
 */
class AvisClientController extends Controller
{
    public function __construct(
        private AvisService $avisService,
        private CommandeService $commandeService,
    ) {}

    public function formulaire(int $id): string
    {
        $commande = $this->commandeDisponiblePourAvis($id);

        return View::render('avis/client_form', [
            'commande' => $commande,
            'pageTitle' => 'Laisser un avis',
            'activeNav' => 'mes-commandes',
        ], 'client');
    }

    public function enregistrer(int $id): void
    {
        $commande = $this->commandeDisponiblePourAvis($id);

        try {
            $this->avisService->creer(
                (int) $commande->clientId,
                $id,
                (int) $this->value('note', 0),
                trim((string) $this->value('commentaire', '')),
            );
            flash('success', 'Merci pour votre avis !');
            View::redirect('/client/commandes/' . $id);
        } catch (ValidationException $e) {
            flash('error', $e->getMessage());
            View::redirectBack('/client/commandes/' . $id . '/avis');
        }
    }

    /**
     * Vérifie que la commande peut recevoir un avis et appartient au client connecté.
     */
    private function commandeDisponiblePourAvis(int $id): Commande
    {
        $client = client();
        if ($client === null) {
            View::redirect('/connexion');
        }

        try {
            $commande = $this->commandeService->trouver($id);
        } catch (NotFoundException) {
            flash('error', 'Cette commande n\'existe pas.');
            View::redirect('/client/commandes');
        }

        if ($commande->clientId !== (int) $client['id']) {
            flash('error', 'Vous ne pouvez pas laisser d\'avis sur cette commande.');
            View::redirect('/client/commandes');
        }

        if ($commande->statut !== Commande::STATUT_RETIREE) {
            flash('error', 'Vous pouvez laisser un avis uniquement après avoir retiré votre commande.');
            View::redirect('/client/commandes/' . $id);
        }

        if ($this->avisService->trouverPourCommande($id) !== null) {
            flash('error', 'Vous avez déjà laissé un avis pour cette commande.');
            View::redirect('/client/commandes/' . $id);
        }

        return $commande;
    }
}
