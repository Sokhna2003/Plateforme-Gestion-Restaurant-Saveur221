<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\NotFoundException;
use App\Exceptions\ValidationException;
use App\Interfaces\CategorieRepositoryInterface;
use App\Interfaces\ProduitRepositoryInterface;

class TrashService
{
    /** Entités gérées par la corbeille : clé => libellé (dans l'ordre) */
    private const ENTITES = [
        'categorie' => 'Catégories',
        'produit' => 'Produits',
    ];

    public function __construct(
        private CategorieRepositoryInterface $categorieRepository,
        private ProduitRepositoryInterface $produitRepository,
    ) {}

    public static function entites(): array
    {
        return self::ENTITES;
    }

    /**
     * @return array<array<string, mixed>>
     */
    public function lister(?string $entite, string $motCle = ''): array
    {
        $motCle = trim($motCle);
        $items = [];

        if ($entite === null || $entite === 'categorie' || $entite === 'tout') {
            foreach ($this->categorieRepository->trashed($motCle) as $cat) {
                $items[] = [
                    'type' => 'Categorie',
                    'entite' => 'categorie',
                    'id' => $cat->id,
                    'nom' => $cat->nom,
                    'detail' => $cat->description ?? 'Aucune description',
                    'image' => $cat->image,
                    'dateSuppression' => $cat->supprimeLe ?? '',
                    'infos' => $cat->nombreDeProduits . ' produit(s)',
                ];
            }
        }

        if ($entite === null || $entite === 'produit' || $entite === 'tout') {
            foreach ($this->produitRepository->trashed($motCle) as $produit) {
                $items[] = [
                    'type' => 'Produit',
                    'entite' => 'produit',
                    'id' => $produit->id,
                    'nom' => $produit->libelle,
                    'detail' => $produit->categorieNom ?? 'Sans catégorie',
                    'image' => $produit->image,
                    'dateSuppression' => $produit->supprimeLe ?? '',
                    'infos' => $produit->prixFormate(),
                ];
            }
        }

        usort($items, fn (array $a, array $b) => strcmp($b['dateSuppression'], $a['dateSuppression']));
        return $items;
    }

    /**
     * @throws NotFoundException
     * @throws ValidationException
     */
    public function restaurer(string $entite, int $id): void
    {
        if ($entite === 'categorie') {
            if ($this->categorieRepository->findTrashedById($id) === null) {
                throw new NotFoundException();
            }
            $this->categorieRepository->restore($id);
            return;
        }

        if ($entite === 'produit') {
            if ($this->produitRepository->findTrashedById($id) === null) {
                throw new NotFoundException();
            }
            $this->produitRepository->restore($id);
            return;
        }

        throw new NotFoundException();
    }

    /**
     * @throws NotFoundException
     * @throws ValidationException
     */
    public function supprimerDefinitivement(string $entite, int $id): void
    {
        if ($entite === 'categorie') {
            if ($this->categorieRepository->findTrashedById($id) === null) {
                throw new NotFoundException();
            }
            $produits = $this->categorieRepository->compterProduits($id, true);
            if ($produits > 0) {
                throw new ValidationException(
                    'Suppression impossible : ' . $produits . ' produit(s) restent liés à cette catégorie.'
                );
            }
            $this->categorieRepository->forceDelete($id);
            return;
        }

        if ($entite === 'produit') {
            if ($this->produitRepository->findTrashedById($id) === null) {
                throw new NotFoundException();
            }
            $this->produitRepository->forceDelete($id);
            return;
        }

        throw new NotFoundException();
    }

    public function estEntiteValide(?string $entite): bool
    {
        return $entite === null || $entite === 'tout' || array_key_exists($entite, self::ENTITES);
    }
}