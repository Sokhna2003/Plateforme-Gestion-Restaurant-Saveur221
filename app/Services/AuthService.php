<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\ValidationException;
use App\Interfaces\ClientRepositoryInterface;
use App\Models\Client;

class AuthService
{
    public function __construct(private ClientRepositoryInterface $clientRepository) {}

    /**
     * Inscription d'un nouveau client.
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

        $client = $this->clientRepository->create($data);

        $this->connecterSession($client);

        return $client;
    }

    /**
     * Connexion d'un client.
     *
     * @throws ValidationException si email inexistant ou mot de passe incorrect
     */
    public function connecter(string $email, string $motDePasse): Client
    {
        $client = $this->clientRepository->findByEmail($email);

        if ($client === null) {
            throw new ValidationException('Email ou mot de passe incorrect.');
        }

        if (!password_verify($motDePasse, $client->motDePasse)) {
            throw new ValidationException('Email ou mot de passe incorrect.');
        }

        $this->connecterSession($client);

        return $client;
    }

    public function deconnecter(): void
    {
        unset($_SESSION['client']);
        session_regenerate_id(true);
    }

    public function estConnecte(): bool
    {
        return isset($_SESSION['client']);
    }

    public function getClientConnecte(): ?Client
    {
        if (!$this->estConnecte()) {
            return null;
        }

        $id = $_SESSION['client']['id'] ?? null;
        if ($id === null) {
            return null;
        }

        return $this->clientRepository->findById((int) $id);
    }

    private function connecterSession(Client $client): void
    {
        $_SESSION['client'] = $client->toArray();
        session_regenerate_id(true);
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
