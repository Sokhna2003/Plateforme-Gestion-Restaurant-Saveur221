<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\NotFoundException;
use App\Exceptions\ValidationException;
use App\Interfaces\CategorieRepositoryInterface;
use App\Models\Categorie;

class CategorieService
{
    public const PER_PAGE = 6;

    public function __construct(
        private CategorieRepositoryInterface $categorieRepository,
        private UploadService $uploadService,
    ) {}

    /**
     * @return array{categories: Categorie[], page: int, totalPages: int, total: int}
     */
    public function lister(string $motCle = '', int $page = 1): array
    {
        $motCle = trim($motCle);
        $total = $this->categorieRepository->compter($motCle);
        $totalPages = max(1, (int) ceil($total / self::PER_PAGE));
        $page = min(max(1, $page), $totalPages);

        return [
            'categories' => $this->categorieRepository->paginate($page, self::PER_PAGE, $motCle),
            'page' => $page,
            'totalPages' => $totalPages,
            'total' => $total,
        ];
    }

    public function trouver(int $id): Categorie
    {
        $categorie = $this->categorieRepository->findById($id);
        if ($categorie === null) {
            throw new NotFoundException();
        }
        return $categorie;
    }

    /**
     * @param array<string, mixed> $data
     * @param array<string, mixed>|null $fichier $_FILES['image']
     * @throws ValidationException
     */
    public function creer(array $data, ?array $fichier = null): Categorie
    {
        $this->valider($data);

        $image = $this->uploadImage($fichier, true);

        return $this->categorieRepository->create([
            'nom' => trim($data['nom']),
            'description' => $data['description'] !== '' ? trim($data['description']) : null,
            'image' => $image,
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
        $this->valider($data, $id);

        $image = $this->uploadImage($fichier, false);

        $this->categorieRepository->update($id, [
            'nom' => trim($data['nom']),
            'description' => $data['description'] !== '' ? trim($data['description']) : null,
            'image' => $image ?? $data['image_actuelle'] ?? null,
        ]);
    }

    /**
     * Déplace une catégorie dans la corbeille (soft delete).
     *
     * @throws ValidationException si la catégorie contient des produits actifs
     */
    public function supprimer(int $id): void
    {
        $categorie = $this->trouver($id);

        $produits = $this->categorieRepository->compterProduits($id, false);
        if ($produits > 0) {
            throw new ValidationException(
                'Impossible de supprimer cette catégorie : elle contient ' . $produits . ' produit(s).'
            );
        }

        $this->categorieRepository->delete($id);
    }

    /**
     * @return Categorie[]
     */
    public function listerCorbeille(string $motCle = ''): array
    {
        return $this->categorieRepository->trashed(trim($motCle));
    }

    /**
     * @throws NotFoundException
     */
    public function restaurer(int $id): void
    {
        if ($this->categorieRepository->findTrashedById($id) === null) {
            throw new NotFoundException();
        }
        $this->categorieRepository->restore($id);
    }

    /**
     * Suppression définitive (nécessite une catégorie en corbeille sans produit,
     * même en corbeille — sinon violation de la clé étrangère).
     *
     * @throws ValidationException
     * @throws NotFoundException
     */
    public function supprimerDefinitivement(int $id): void
    {
        if ($this->categorieRepository->findTrashedById($id) === null) {
            throw new NotFoundException();
        }

        $produits = $this->categorieRepository->compterProduits($id, true);
        if ($produits > 0) {
            throw new ValidationException(
                'Suppression impossible : ' . $produits . ' produit(s) sont encore liés à cette catégorie.'
            );
        }

        $this->categorieRepository->forceDelete($id);
    }

    /**
     * @param array<string, mixed>|null $fichier
     * @throws ValidationException
     */
    private function uploadImage(?array $fichier, bool $obligatoire): ?string
    {
        if ($fichier === null || (($fichier['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE)) {
            if ($obligatoire) {
                throw new ValidationException('Veuillez ajouter une catégorie avec une image.', [
                    'image' => 'Ce champ est obligatoire.',
                ]);
            }
            return null;
        }

        return $this->uploadService->upload($fichier);
    }

    /**
     * @throws ValidationException
     */
    private function valider(array $data, ?int $idExclu = null): void
    {
        $erreurs = [];

        if (empty(trim($data['nom'] ?? ''))) {
            $erreurs['nom'] = 'Ce champ est obligatoire.';
        } else {
            $existant = $this->categorieRepository->findByNom(trim($data['nom']));
            if ($existant !== null && $existant->id !== $idExclu) {
                $erreurs['nom'] = 'Cette catégorie existe déjà.';
            }
        }

        $desc = $data['description'] ?? '';
        if (trim($desc) !== '' && mb_strlen($desc) > 255) {
            $erreurs['description'] = 'La description ne doit pas dépasser 255 caractères.';
        }

        if (!empty($erreurs)) {
            throw new ValidationException('Données invalides.', $erreurs);
        }
    }
}