<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Exceptions\NotFoundException;
use App\Exceptions\ValidationException;
use App\Models\Commande;
use App\Services\AvisService;
use App\Services\CommandeService;
use App\Services\PanierService;
use App\Services\ProduitService;

/**
 * Passage de commande et suivi côté client connecté.
 */
class CommandeClientController extends Controller
{
    public function __construct(
        private CommandeService $commandeService,
        private PanierService $panierService,
        private ProduitService $produitService,
        private AvisService $avisService,
    ) {}

    public function checkout(): string
    {
        $quantites = $this->panierService->quantites();
        if ($quantites === []) {
            View::redirect('/panier');
        }

        $recap = $this->recapPanier($quantites);

        return View::render('commandes/checkout', [
            'lignes' => $recap['lignes'],
            'total' => $recap['total'],
            'totalArticles' => $recap['totalArticles'],
            'modes' => CommandeService::MODES_CHECKOUT,
            'modesImmediats' => CommandeService::MODES_PAIEMENT_IMMEDIAT,
            'pageTitle' => 'Finaliser la commande',
            'activeNav' => 'commande',
        ], 'client');
    }

    public function placer(): void
    {
        $client = client();
        if ($client === null) {
            View::redirect('/connexion');
        }

        try {
            $resultat = $this->commandeService->placerCommande(
                (int) $client['id'],
                $this->panierService->quantites(),
                trim((string) $this->value('mode_paiement', '')),
            );
            $this->panierService->vider();
            flash('success', 'Commande n°' . $resultat['id'] . ' enregistrée. Merci !');
            View::redirect('/commande/confirmation/' . $resultat['id']);
        } catch (ValidationException $e) {
            flash('error', $e->getMessage());
            View::redirectBack('/commande');
        }
    }

    public function confirmation(int $id): string
    {
        $commande = $this->commandePourClientConnecte($id);

        return View::render('commandes/confirmation', [
            'commande' => $commande,
            'lignes' => $this->commandeService->lignes($id),
            'paiement' => $this->commandeService->paiement($id),
            'pageTitle' => 'Commande confirmée',
            'activeNav' => 'mes-commandes',
        ], 'client');
    }

    public function mesCommandes(): string
    {
        $client = client();
        if ($client === null) {
            View::redirect('/connexion');
        }

        $commandes = $this->commandeService->pourClient((int) $client['id']);
        $idsAvisPossibles = [];
        foreach ($commandes as $cmd) {
            if ($cmd->statut === Commande::STATUT_RETIREE && $this->avisService->trouverPourCommande((int) $cmd->id) === null) {
                $idsAvisPossibles[] = (int) $cmd->id;
            }
        }

        return View::render('commandes/client_liste', [
            'commandes' => $commandes,
            'idsAvisPossibles' => $idsAvisPossibles,
            'pageTitle' => 'Mes commandes',
            'activeNav' => 'mes-commandes',
        ], 'client');
    }

    public function detail(int $id): string
    {
        $commande = $this->commandePourClientConnecte($id);
        $avis = $this->avisService->trouverPourCommande($id);

        return View::render('commandes/client_detail', [
            'commande' => $commande,
            'lignes' => $this->commandeService->lignes($id),
            'paiement' => $this->commandeService->paiement($id),
            'avis' => $avis,
            'peutLaisserAvis' => $commande->statut === Commande::STATUT_RETIREE && $avis === null,
            'pageTitle' => 'Commande n°' . $id,
            'activeNav' => 'mes-commandes',
        ], 'client');
    }

    /**
     * Vérifie que la commande appartient bien au client connecté, sinon
     * redirige vers « Mes commandes » avec un message flash.
     */
    private function commandePourClientConnecte(int $id): Commande
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
            flash('error', 'Vous ne pouvez pas consulter cette commande.');
            View::redirect('/client/commandes');
        }

        return $commande;
    }

    /**
     * @param array<int, int> $quantites
     * @return array{lignes: array<int, array{produit: \App\Models\Produit, quantite: int, sousTotal: float}>, total: float, totalArticles: int}
     */
    private function recapPanier(array $quantites): array
    {
        $lignes = [];
        foreach ($this->produitService->parIds(array_keys($quantites)) as $produit) {
            $lignes[] = [
                'produit' => $produit,
                'quantite' => $quantites[$produit->id],
                'sousTotal' => $produit->prix * $quantites[$produit->id],
            ];
        }

        return [
            'lignes' => $lignes,
            'total' => array_sum(array_column($lignes, 'sousTotal')),
            'totalArticles' => array_sum($quantites),
        ];
    }
}