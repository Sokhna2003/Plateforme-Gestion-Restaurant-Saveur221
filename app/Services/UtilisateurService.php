<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\NotFoundException;
use App\Exceptions\ValidationException;
use App\Interfaces\UtilisateurRepositoryInterface;
use App\Models\Utilisateur;

class UtilisateurService
{
    public const PER_PAGE = 8;

    public function __construct(private UtilisateurRepositoryInterface $utilisateurRepository) {}

    /**
     * Liste paginee des utilisateurs internes, avec recherche et filtres.
     *
     * @return array{utilisateurs: Utilisateur[], page: int, totalPages: int, total: int}
     */
    public function lister(?string $motCle, ?int $roleId, ?string $actif, int $page, int $perPage = self::PER_PAGE): array
    {
        $total = $this->utilisateurRepository->compterGestion($roleId, $actif, $motCle);
        $totalPages = max(1, (int) ceil($total / $perPage));
        $page = max(1, min($page, $totalPages));

        $utilisateurs = $this->utilisateurRepository->paginer($page, $perPage, $roleId, $actif, $motCle);

        return [
            'utilisateurs' => $utilisateurs,
            'page' => $page,
            'totalPages' => $totalPages,
            'total' => $total,
        ];
    }

    /**
     * Statistiques globales des utilisateurs internes.
     *
     * @return array{total: int, admins: int, gerants: int, actifs: int}
     */
    public function statistiques(): array
    {
        return [
            'total' => $this->utilisateurRepository->count(),
            'admins' => $this->utilisateurRepository->compterParRole(1),
            'gerants' => $this->utilisateurRepository->compterParRole(2),
            'actifs' => $this->utilisateurRepository->compterActifs(),
        ];
    }

    /** @return \stdClass[] */
    public function roles(): array
    {
        return $this->utilisateurRepository->listerRoles();
    }

    /**
     * @throws NotFoundException
     */
    public function trouver(int $id): Utilisateur
    {
        $utilisateur = $this->utilisateurRepository->findById($id);
        if ($utilisateur === null) {
            throw new NotFoundException();
        }
        return $utilisateur;
    }

    /**
     * @param array<string, mixed> $data
     * @throws ValidationException
     */
    public function creer(array $data): void
    {
        $this->valider($data, true);

        if ($this->utilisateurRepository->findByEmail($data['email']) !== null) {
            throw new ValidationException('Cet email est déjà utilisé.', [
                'email' => 'Cet email est déjà utilisé.',
            ]);
        }

        $this->utilisateurRepository->create([
            'nom' => trim($data['nom']),
            'prenom' => trim($data['prenom']),
            'email' => $data['email'],
            'mot_de_passe' => password_hash($data['mot_de_passe'], PASSWORD_DEFAULT),
            'role_id' => (int) $data['role_id'],
            'actif' => isset($data['actif']) && (string) $data['actif'] === '1',
        ]);
    }

    /**
     * @param array<string, mixed> $data
     * @throws ValidationException
     */
    public function modifier(int $id, array $data, int $utilisateurConnecteId): void
    {
        $utilisateur = $this->trouver($id);
        $this->valider($data, false);

        // Un utilisateur ne peut pas desactiver son propre compte.
        $actif = isset($data['actif']) && (string) $data['actif'] === '1';
        if ($id === $utilisateurConnecteId && $utilisateur->actif && !$actif) {
            throw new ValidationException('Vous ne pouvez pas désactiver votre propre compte.', [
                'actif' => 'Vous ne pouvez pas désactiver votre propre compte.',
            ]);
        }

        $email = $data['email'];
        if ($email !== $utilisateur->email) {
            $existant = $this->utilisateurRepository->findByEmail($email);
            if ($existant !== null && $existant->id !== $id) {
                throw new ValidationException('Cet email est déjà utilisé.', [
                    'email' => 'Cet email est déjà utilisé.',
                ]);
            }
        }

        $donnees = [
            'nom' => trim($data['nom']),
            'prenom' => trim($data['prenom']),
            'email' => $email,
            'role_id' => (int) $data['role_id'],
            'actif' => $actif,
            'mot_de_passe' => '',
        ];

        $motDePasse = (string) ($data['mot_de_passe'] ?? '');
        if ($motDePasse !== '') {
            $donnees['mot_de_passe'] = password_hash($motDePasse, PASSWORD_DEFAULT);
        }

        $this->utilisateurRepository->update($id, $donnees);
    }

    /**
     * @throws ValidationException
     */
    public function basculerActif(int $id, int $utilisateurConnecteId): void
    {
        $utilisateur = $this->trouver($id);

        if ($id === $utilisateurConnecteId) {
            throw new ValidationException('Vous ne pouvez pas désactiver votre propre compte.');
        }

        $this->utilisateurRepository->definirActif($id, !$utilisateur->actif);
    }

    /**
     * @throws ValidationException
     */
    public function supprimer(int $id, int $utilisateurConnecteId): void
    {
        if ($id === $utilisateurConnecteId) {
            throw new ValidationException('Vous ne pouvez pas supprimer votre propre compte.');
        }

        $this->trouver($id);
        $this->utilisateurRepository->delete($id);
    }

    /**
     * @param array<string, mixed> $data
     * @throws ValidationException
     */
    private function valider(array $data, bool $creation): void
    {
        $erreurs = [];

        foreach (['nom', 'prenom'] as $champ) {
            $valeur = trim((string) ($data[$champ] ?? ''));
            if ($valeur === '') {
                $erreurs[$champ] = 'Ce champ est obligatoire.';
            } elseif (mb_strlen($valeur) > 100) {
                $erreurs[$champ] = 'Ce champ ne doit pas dépasser 100 caractères.';
            }
        }

        $email = trim((string) ($data['email'] ?? ''));
        if ($email === '') {
            $erreurs['email'] = 'Ce champ est obligatoire.';
        } elseif (mb_strlen($email) > 150) {
            $erreurs['email'] = 'L\'email ne doit pas dépasser 150 caractères.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $erreurs['email'] = 'Le format de l\'email est invalide.';
        }

        $roleIds = array_map(static fn (\stdClass $r): int => (int) $r->id, $this->roles());
        if (!in_array((int) ($data['role_id'] ?? 0), $roleIds, true)) {
            $erreurs['role_id'] = 'Veuillez choisir un rôle.';
        }

        $motDePasse = (string) ($data['mot_de_passe'] ?? '');
        $confirmation = (string) ($data['mot_de_passe_confirmation'] ?? '');

        if ($creation) {
            if ($motDePasse === '') {
                $erreurs['mot_de_passe'] = 'Ce champ est obligatoire.';
            } elseif (strlen($motDePasse) < 6) {
                $erreurs['mot_de_passe'] = 'Le mot de passe doit contenir au moins 6 caractères.';
            }
            if ($confirmation === '') {
                $erreurs['mot_de_passe_confirmation'] = 'Ce champ est obligatoire.';
            } elseif ($motDePasse !== $confirmation) {
                $erreurs['mot_de_passe_confirmation'] = 'Les mots de passe ne correspondent pas.';
            }
        } elseif ($motDePasse !== '') {
            if (strlen($motDePasse) < 6) {
                $erreurs['mot_de_passe'] = 'Le mot de passe doit contenir au moins 6 caractères.';
            }
            if ($confirmation !== $motDePasse) {
                $erreurs['mot_de_passe_confirmation'] = 'Les mots de passe ne correspondent pas.';
            }
        }

        if (!empty($erreurs)) {
            throw new ValidationException('Données invalides.', $erreurs);
        }
    }
}