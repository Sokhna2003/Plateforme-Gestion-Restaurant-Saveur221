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
        if ($email === '' || $motDePasse === '') {
            throw new ValidationException('Email ou mot de passe incorrect.', [
                'email' => $email === '' ? 'L\'adresse email est requise.' : '',
                'mot_de_passe' => $motDePasse === '' ? 'Le mot de passe est requis.' : '',
            ]);
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new ValidationException('Email ou mot de passe incorrect.', [
                'email' => 'Le format de l\'email est invalide.',
            ]);
        }
        // 1) Verifier la table clients
        $client = $this->clientRepository->findByEmail($email);
        if ($client !== null) {
            if (!$this->verifierMotDePasse($motDePasse, $client->motDePasse)) {
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
            if (!$this->verifierMotDePasse($motDePasse, $utilisateur->motDePasse)) {
                throw new ValidationException('Email ou mot de passe incorrect.');
            }
            $_SESSION['user'] = $utilisateur->toArray();
            session_regenerate_id(true);

            $redirect = ($utilisateur->role === 'ADMIN') ? '/admin' : '/gerant';
            return ['type' => 'utilisateur', 'redirect' => $redirect];
        }

        throw new ValidationException('Email ou mot de passe incorrect.');
    }

    /**
     * Verifie un mot de passe contre le hash stocke.
     * Gere : bcrypt (PHP), SHA-256 (Java), et clair (seed).
     * Re-hash automatiquement en bcrypt si le mot de passe etait en clair ou SHA-256.
     */
    private function verifierMotDePasse(string $saisi, string $stocke): bool
    {
        // 1) Bcrypt
        if (str_starts_with($stocke, '$2y$') || str_starts_with($stocke, '$2a$') || str_starts_with($stocke, '$2b$')) {
            return password_verify($saisi, $stocke);
        }

        // 2) SHA-256 (hash hex de 64 car.) — insere par le module Java
        if (ctype_xdigit($stocke) && strlen($stocke) === 64) {
            if (hash_equals($stocke, hash('sha256', $saisi))) {
                $nouveauHash = password_hash($saisi, PASSWORD_DEFAULT);
                $this->clientRepository->updateMotDePasseByHash($stocke, $nouveauHash);
                $this->utilisateurRepository->updateMotDePasseByHash($stocke, $nouveauHash);
                return true;
            }
            return false;
        }

        // 3) Mot de passe en clair (seed)
        if (hash_equals($stocke, $saisi)) {
            $nouveauHash = password_hash($saisi, PASSWORD_DEFAULT);
            $this->clientRepository->updateMotDePasseByHash($stocke, $nouveauHash);
            $this->utilisateurRepository->updateMotDePasseByHash($stocke, $nouveauHash);
            return true;
        }

        return false;
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
            $erreurs['nom'] = 'Ce champ est obligatoire.';
        }
        if (empty($data['prenom']) || trim($data['prenom']) === '') {
            $erreurs['prenom'] = 'Ce champ est obligatoire.';
        }
        if (empty($data['email']) || trim($data['email']) === '') {
            $erreurs['email'] = 'Ce champ est obligatoire.';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $erreurs['email'] = 'Le format de l\'email est invalide.';
        }
        if (empty($data['telephone']) || trim($data['telephone']) === '') {
            $erreurs['telephone'] = 'Ce champ est obligatoire.';
        }
        if (empty($data['adresse']) || trim($data['adresse']) === '') {
            $erreurs['adresse'] = 'Ce champ est obligatoire.';
        }
        if (empty($data['mot_de_passe']) || trim($data['mot_de_passe']) === '') {
            $erreurs['mot_de_passe'] = 'Ce champ est obligatoire.';
        } elseif (strlen($data['mot_de_passe']) < 6) {
            $erreurs['mot_de_passe'] = 'Le mot de passe doit contenir au moins 6 caractères.';
        }
        if (empty($data['mot_de_passe_confirmation']) || trim($data['mot_de_passe_confirmation']) === '') {
            $erreurs['mot_de_passe_confirmation'] = 'Ce champ est obligatoire.';
        } elseif (($data['mot_de_passe'] ?? '') !== ($data['mot_de_passe_confirmation'] ?? '')) {
            $erreurs['mot_de_passe_confirmation'] = 'Les mots de passe ne correspondent pas.';
        }

        if (!empty($erreurs)) {
            throw new ValidationException('Données invalides.', $erreurs);
        }
    }
}
