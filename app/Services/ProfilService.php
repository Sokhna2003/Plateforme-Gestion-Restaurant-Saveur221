<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\ValidationException;
use App\Interfaces\ClientRepositoryInterface;
use App\Interfaces\UtilisateurRepositoryInterface;

/**
 * Lecture et modification du profil de l'utilisateur connecte
 * (client espace public OU utilisateur interne admin/gerant).
 */
class ProfilService
{
    public function __construct(
        private ClientRepositoryInterface $clientRepository,
        private UtilisateurRepositoryInterface $utilisateurRepository,
        private UploadService $uploadService,
    ) {}

    public function estClient(): bool
    {
        return isset($_SESSION['client']);
    }

    /**
     * @return array<string, mixed>
     */
    public function profil(): array
    {
        if ($this->estClient()) {
            $id = (int) ($_SESSION['client']['id'] ?? 0);
            $client = $this->clientRepository->findById($id);
            return $client?->toArray() ?? $_SESSION['client'];
        }

        $id = (int) ($_SESSION['user']['id'] ?? 0);
        $utilisateur = $this->utilisateurRepository->findById($id);
        return $utilisateur?->toArray() ?? ($_SESSION['user'] ?? []);
    }

    /**
     * Met a jour les informations (et eventuellement la photo et le mot de
     * passe) de l'utilisateur connecte, puis rafraichit la session.
     *
     * @param array<string, string> $data
     * @param array<string, mixed> $fichier Élément $_FILES['photo'] ou tableau vide
     *
     * @throws ValidationException
     */
    public function modifier(array $data, array $fichier): void
    {
        $erreurs = $this->valider($data);
        $estClient = $this->estClient();
        $id = (int) ($_SESSION['client']['id'] ?? $_SESSION['user']['id'] ?? 0);

        if ($id === 0) {
            throw new ValidationException('Aucun compte connecté.');
        }

        $email = strtolower(trim((string) ($data['email'] ?? '')));
        if (empty($erreurs['email'])) {
            if ($estClient) {
                $existant = $this->clientRepository->findByEmail($email);
                if ($existant !== null && $existant->id !== $id) {
                    $erreurs['email'] = 'Cet email est déjà utilisé.';
                }
            } else {
                $existant = $this->utilisateurRepository->findByEmail($email);
                if ($existant !== null && $existant->id !== $id) {
                    $erreurs['email'] = 'Cet email est déjà utilisé.';
                }
            }
        }

        $nouvellePhoto = null;
        try {
            $nouvellePhoto = $this->uploadService->upload($fichier, 'saveur221/profils');
        } catch (ValidationException $e) {
            $erreurs['photo'] = $e->getMessage();
        }

        if ($erreurs !== []) {
            throw new ValidationException('Données invalides.', $erreurs);
        }

        $nom = trim((string) ($data['nom'] ?? ''));
        $prenom = trim((string) ($data['prenom'] ?? ''));
        $motDePasse = (string) ($data['mot_de_passe'] ?? '');

        if ($estClient) {
            $this->clientRepository->update($id, [
                'nom' => $nom,
                'prenom' => $prenom,
                'email' => $email,
                'telephone' => trim((string) ($data['telephone'] ?? '')),
                'adresse' => trim((string) ($data['adresse'] ?? '')),
            ]);
            if ($nouvellePhoto !== null) {
                $this->clientRepository->updatePhoto($id, $nouvellePhoto);
            }
            if ($motDePasse !== '') {
                $this->clientRepository->updateMotDePasse($id, password_hash($motDePasse, PASSWORD_DEFAULT));
            }
            $profilMisAJour = $this->clientRepository->findById($id);
            $_SESSION['client'] = $profilMisAJour?->toArray() ?? $_SESSION['client'];
            return;
        }

        $utilisateur = $this->utilisateurRepository->findById($id);
        $this->utilisateurRepository->update($id, [
            'nom' => $nom,
            'prenom' => $prenom,
            'email' => $email,
            'role_id' => $utilisateur?->roleId ?? 2,
            'actif' => $utilisateur?->actif ?? true,
            'mot_de_passe' => $motDePasse !== '' ? password_hash($motDePasse, PASSWORD_DEFAULT) : '',
        ]);
        if ($nouvellePhoto !== null) {
            $this->utilisateurRepository->updatePhoto($id, $nouvellePhoto);
        }
        $profilMisAJour = $this->utilisateurRepository->findById($id);
        $_SESSION['user'] = $profilMisAJour?->toArray() ?? $_SESSION['user'];
    }

    /**
     * @param array<string, string> $data
     *
     * @return array<string, string>
     */
    private function valider(array $data): array
    {
        $erreurs = [];

        $nom = trim((string) ($data['nom'] ?? ''));
        $prenom = trim((string) ($data['prenom'] ?? ''));
        $email = strtolower(trim((string) ($data['email'] ?? '')));

        if ($nom === '') {
            $erreurs['nom'] = 'Ce champ est obligatoire.';
        }
        if ($prenom === '') {
            $erreurs['prenom'] = 'Ce champ est obligatoire.';
        }
        if ($email === '') {
            $erreurs['email'] = 'Ce champ est obligatoire.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $erreurs['email'] = 'Le format de l\'email est invalide.';
        }

        if ($this->estClient()) {
            if (trim((string) ($data['telephone'] ?? '')) === '') {
                $erreurs['telephone'] = 'Ce champ est obligatoire.';
            }
            if (trim((string) ($data['adresse'] ?? '')) === '') {
                $erreurs['adresse'] = 'Ce champ est obligatoire.';
            }
        }

        $motDePasse = (string) ($data['mot_de_passe'] ?? '');
        if ($motDePasse !== '') {
            if (strlen($motDePasse) < 6) {
                $erreurs['mot_de_passe'] = 'Le mot de passe doit contenir au moins 6 caractères.';
            } elseif ($motDePasse !== (string) ($data['mot_de_passe_confirmation'] ?? '')) {
                $erreurs['mot_de_passe_confirmation'] = 'Les mots de passe ne correspondent pas.';
            }
        }

        return $erreurs;
    }
}