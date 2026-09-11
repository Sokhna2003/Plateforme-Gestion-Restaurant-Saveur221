<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\NotFoundException;
use App\Exceptions\ValidationException;
use App\Interfaces\CategorieRepositoryInterface;
use App\Interfaces\ProduitRepositoryInterface;
use App\Models\Produit;

/**
 * Couche metier au-dessus des Repositories : le Controller ne parle jamais
 * directement a PDO, il passe par ce Service.
 */
class ProduitService
{
    public const PER_PAGE = 8;

    public function __construct(
        private ProduitRepositoryInterface $produitRepository,
        private CategorieRepositoryInterface $categorieRepository,
        private UploadService $uploadService,
    ) {}

    /**
     * Liste paginee du catalogue public, avec recherche et filtre par categorie
     * optionnels.
     *
     * @return array{produits: array, page: int, totalPages: int}
     */
    public function lister(?string $motCle = null, ?int $categorieId = null, int $page = 1, int $perPage = 8): array
    {
        $total = $this->produitRepository->compter($categorieId, $motCle);
        $totalPages = max(1, (int) ceil($total / $perPage));
        $page = max(1, min($page, $totalPages));

        $produits = $this->produitRepository->paginate($page, $perPage, $categorieId, $motCle);

        return [
            'produits' => $produits,
            'page' => $page,
            'totalPages' => $totalPages,
        ];
    }

    /**
     * Liste paginee de l'espace d'administration (tous produits, avec filtres).
     *
     * @return array{produits: Produit[], page: int, totalPages: int, total: int}
     */
    public function listerAdministration(
        ?string $motCle = null,
        ?int $categorieId = null,
        ?string $disponible = null,
        int $page = 1,
        int $perPage = self::PER_PAGE
    ): array {
        $total = $this->produitRepository->compterAdministration($categorieId, $disponible, $motCle);
        $totalPages = max(1, (int) ceil($total / $perPage));
        $page = max(1, min($page, $totalPages));

        $produits = $this->produitRepository->paginerAdministration($page, $perPage, $categorieId, $disponible, $motCle);

        return [
            'produits' => $produits,
            'page' => $page,
            'totalPages' => $totalPages,
            'total' => $total,
        ];
    }

    /** @return \App\Models\Categorie[] */
    public function listerCategories(): array
    {
        return $this->categorieRepository->all();
    }

    /** @return \App\Models\Produit[] */
    public function listerPopulaires(int $limite = 4): array
    {
        return $this->produitRepository->paginate(1, $limite);
    }

    /**
     * Retrouve plusieurs produits par identifiant (pour le panier notamment).
     *
     * @param int[] $ids
     * @return \App\Models\Produit[]
     */
    public function parIds(array $ids): array
    {
        return $this->produitRepository->findByIds($ids);
    }

    /**
     * Detail d'un produit + une selection de produits similaires.
     * Retourne null si le produit n'existe pas.
     *
     * @return array{produit: \App\Models\Produit, similaires: array}|null
     */
    public function detail(int $id): ?array
    {
        $produit = $this->produitRepository->findById($id);
        if ($produit === null) {
            return null;
        }

        $similaires = $this->produitRepository->similaires($produit->categorieId, $id, 3);

        return [
            'produit' => $produit,
            'similaires' => $similaires,
        ];
    }

    /**
     * @throws NotFoundException
     */
    public function trouver(int $id): Produit
    {
        $produit = $this->produitRepository->findById($id);
        if ($produit === null) {
            throw new NotFoundException();
        }
        return $produit;
    }

    /**
     * @param array<string, mixed> $data
     * @param array<string, mixed>|null $fichier $_FILES['image']
     * @throws ValidationException
     */
    public function creer(array $data, ?array $fichier = null): Produit
    {
        $this->valider($data);

        return $this->produitRepository->create([
            'libelle' => trim($data['libelle']),
            'description' => $data['description'] !== '' ? trim($data['description']) : null,
            'prix' => (float) $data['prix'],
            'quantite_stock' => (int) $data['quantite_stock'],
            'seuil_alerte' => (int) $data['seuil_alerte'],
            'categorie_id' => (int) $data['categorie_id'],
            'disponible' => isset($data['disponible']) && (string) $data['disponible'] === '1',
            'image' => $this->uploadImage($fichier, false),
        ]);
    }

    /**
     * @param array<string, mixed> $data
     * @param array<string, mixed>|null $fichier $_FILES['image']
     * @throws ValidationException
     */
    public function modifier(int $id, array $data, ?array $fichier = null): void
    {
        $this->trouver($id);
        $this->valider($data);

        $image = $this->uploadImage($fichier, false);

        $this->produitRepository->update($id, [
            'libelle' => trim($data['libelle']),
            'description' => $data['description'] !== '' ? trim($data['description']) : null,
            'prix' => (float) $data['prix'],
            'quantite_stock' => (int) $data['quantite_stock'],
            'seuil_alerte' => (int) $data['seuil_alerte'],
            'categorie_id' => (int) $data['categorie_id'],
            'disponible' => isset($data['disponible']) && (string) $data['disponible'] === '1',
            'image' => $image ?? $data['image_actuelle'] ?? null,
        ]);
    }

    /**
     * Deplace un produit dans la corbeille (soft delete).
     */
    public function supprimer(int $id): void
    {
        $this->trouver($id);
        $this->produitRepository->delete($id);
    }

    /**
     * Bascule la disponibilite d'un produit (interrupteur on/off).
     *
     * @throws NotFoundException
     */
    public function basculerDisponibilite(int $id): void
    {
        $produit = $this->trouver($id);
        $this->produitRepository->definirDisponibilite($id, !$produit->disponible);
    }

    /**
     * @param array<string, mixed>|null $fichier
     * @throws ValidationException
     */
    private function uploadImage(?array $fichier, bool $obligatoire): ?string
    {
        if ($fichier === null || (($fichier['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE)) {
            if ($obligatoire) {
                throw new ValidationException('Veuillez fournir une image pour le produit.', [
                    'image' => 'Ce champ est obligatoire.',
                ]);
            }
            return null;
        }

        return $this->uploadService->upload($fichier, 'saveur221/produits');
    }

    /**
     * @param array<string, mixed> $data
     * @throws ValidationException
     */
    private function valider(array $data): void
    {
        $erreurs = [];

        if (empty(trim($data['libelle'] ?? ''))) {
            $erreurs['libelle'] = 'Ce champ est obligatoire.';
        } elseif (mb_strlen(trim($data['libelle'])) > 150) {
            $erreurs['libelle'] = 'Le libellé ne doit pas dépasser 150 caractères.';
        }

        $prix = $data['prix'] ?? '';
        if ($prix === '' || !is_numeric($prix) || (float) $prix < 0) {
            $erreurs['prix'] = 'Le prix doit être un nombre positif.';
        }

        if (empty($data['categorie_id'])) {
            $erreurs['categorie_id'] = 'Veuillez choisir une catégorie.';
        } else {
            $categorie = $this->categorieRepository->findById((int) $data['categorie_id']);
            if ($categorie === null) {
                $erreurs['categorie_id'] = 'Cette catégorie n\'existe pas.';
            }
        }

        foreach (['quantite_stock', 'seuil_alerte'] as $champ) {
            $valeur = $data[$champ] ?? '';
            if ($valeur === '' || !is_numeric($valeur) || (int) $valeur < 0) {
                $erreurs[$champ] = 'La valeur doit être un nombre entier positif.';
            }
        }

        if (!empty($erreurs)) {
            throw new ValidationException('Données invalides.', $erreurs);
        }
    }
}