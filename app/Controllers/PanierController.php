<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Exceptions\NotFoundException;
use App\Services\PanierService;
use App\Services\ProduitService;

class PanierController extends Controller
{
    public function __construct(
        private PanierService $panierService,
        private ProduitService $produitService,
    ) {}

    public function index(): string
    {
        $quantites = $this->panierService->quantites();

        $lignes = [];
        foreach ($this->produitService->parIds(array_keys($quantites)) as $produit) {
            $lignes[] = [
                'produit' => $produit,
                'quantite' => $quantites[$produit->id],
                'sousTotal' => $produit->prix * $quantites[$produit->id],
            ];
        }

        return View::render('panier/index', [
            'lignes' => $lignes,
            'total' => array_sum(array_column($lignes, 'sousTotal')),
            'totalArticles' => array_sum($quantites),
            'pageTitle' => 'Panier',
            'activeNav' => 'menu',
        ]);
    }

    /**
     * Ajoute un produit au panier depuis le menu ou la page detail.
     */
    public function ajouter(): void
    {
        $produitId = (int) $this->value('produit_id', 0);
        $quantite = max(1, (int) $this->value('quantite', 1));

        try {
            $produit = $this->produitService->trouver($produitId);
        } catch (NotFoundException) {
            flash('error', 'Ce produit n\'existe pas.');
            View::redirectBack('/menu');
        }

        if (!$produit->estDisponible()) {
            flash('error', $produit->libelle . ' n\'est pas disponible pour le moment.');
            View::redirectBack('/menu');
        }

        $this->panierService->ajouter($produit->id, $quantite);
        flash('success', $produit->libelle . ' ajouté au panier.');
        View::redirectBack('/menu');
    }

    public function modifier(int $id): void
    {
        $quantite = (int) $this->value('quantite', 1);

        if (!$this->panierService->contient($id)) {
            flash('error', 'Ce produit n\'est pas dans votre panier.');
            View::redirect('/panier');
        }

        $this->panierService->modifier($id, $quantite);
        flash('success', 'Quantité mise à jour.');
        View::redirect('/panier');
    }

    public function retirer(int $id): void
    {
        if ($this->panierService->contient($id)) {
            $this->panierService->retirer($id);
            flash('success', 'Produit retiré du panier.');
        }
        View::redirect('/panier');
    }

    public function vider(): void
    {
        $this->panierService->vider();
        flash('success', 'Votre panier a été vidé.');
        View::redirect('/panier');
    }
}