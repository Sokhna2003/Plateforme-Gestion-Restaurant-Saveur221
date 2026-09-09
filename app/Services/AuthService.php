<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\View;
use App\Exceptions\ValidationException;
use App\Interfaces\ClientRepositoryInterface;
use App\Interfaces\UtilisateurRepositoryInterface;
use App\Models\Client;
use App\Models\Utilisateur;

class AuthService
{
    public function __construct(
        private ClientRepositoryInterface $clientRepository,
        private UtilisateurRepositoryInterface $utilisateurRepository,
    ) {}

    /**
     * Inscription d'un nouveau client → redirige vers /connexion.
     *
     * @throws ValidationException si l'email existe deja ou donnees invalides
     */
    public function inscrire(array $data): Client
    {
        $this->validerInscription($data);

        if ($this->clientRepository->findByEmail($data['email']) !== null) {
            throw new ValidationException('Cet email est déjà utilisé.');
        }

        $data['mot_de_passe'] = password_hash($data['mot_de_passe'], PASSWORD_DEFAULT);

        return $this->clientRepository->create($data);
    }

    /**
     * Connexion : verifie clients OU utilisateurs internes.
     *
     * @throws ValidationException si email inexistant, mot de passe incorrect, ou compte desactive
     */
    public function connecter(string $email, string $motDePasse): array
    {
        // 1) Verifier la table clients
        $client = $this->clientRepository->findByEmail($email);
        if ($client !== null) {
            if (!password_verify($motDePasse, $client->motDePasse)) {
                throw new ValidationException('Email ou mot de passe incorrect.');
            }
            $_SESSION['client'] = $client->toArray();
            session_regenerate_id(true);
            return ['type' => 'client', 'redirect' => '/client'];
        }

        // 2) Verifier la table utilisateurs (admin / gerant)
        $utilisateur = $this->utilisateurRepository->findByEmail($email);
        if ($utilisateur !== null) {
            if (!$utilisateur->actif) {
                throw new ValidationException('Votre compte a été désactivé.');
            }
            if (!password_verify($motDePasse, $utilisateur->motDePasse)) {
                throw new ValidationException('Email ou mot de passe incorrect.');
            }
            $_SESSION['user'] = $utilisateur->toArray();
            session_regenerate_id(true);

            $redirect = ($utilisateur->role === 'ADMIN') ? '/admin' : '/gerant';
            return ['type' => 'utilisateur', 'redirect' => $redirect];
        }

        throw new ValidationException('Email ou mot de passe incorrect.');
    }

    public function deconnecter(): void
    {
        unset($_SESSION['client'], $_SESSION['user']);
        session_regenerate_id(true);
    }

    public function estConnecte(): bool
    {
        return isset($_SESSION['client']) || isset($_SESSION['user']);
    }

    public function estClient(): bool
    {
        return isset($_SESSION['client']);
    }

    public function estAdmin(): bool
    {
        return ($_SESSION['user']['role'] ?? '') === 'ADMIN';
    }

    public function estGerant(): bool
    {
        $role = $_SESSION['user']['role'] ?? '';
        return $role === 'GERANT' || $role === 'ADMIN';
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getUtilisateurConnecte(): ?array
    {
        return $_SESSION['client'] ?? $_SESSION['user'] ?? null;
    }

    private function validerInscription(array $data): void
    {
        $erreurs = [];

        if (empty($data['nom']) || trim($data['nom']) === '') {
            $erreurs['nom'] = 'Le nom est requis.';
        }
        if (empty($data['prenom']) || trim($data['prenom']) === '') {
            $erreurs['prenom'] = 'Le prénom est requis.';
        }
        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $erreurs['email'] = 'Un email valide est requis.';
        }
        if (empty($data['mot_de_passe']) || strlen($data['mot_de_passe']) < 6) {
            $erreurs['mot_de_passe'] = 'Le mot de passe doit contenir au moins 6 caractères.';
        }
        if (($data['mot_de_passe'] ?? '') !== ($data['mot_de_passe_confirmation'] ?? '')) {
            $erreurs['mot_de_passe_confirmation'] = 'Les mots de passe ne correspondent pas.';
        }

        if (!empty($erreurs)) {
            throw new ValidationException('Données invalides.', $erreurs);
        }
    }
}
