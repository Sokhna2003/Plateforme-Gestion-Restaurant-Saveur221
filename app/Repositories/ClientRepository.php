<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Interfaces\ClientRepositoryInterface;
use App\Models\Client;
use PDO;

class ClientRepository implements ClientRepositoryInterface
{
    public function __construct(private PDO $pdo) {}

    public function findById(int $id): ?Client
    {
        $stmt = $this->pdo->prepare('SELECT * FROM clients WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row === false ? null : Client::fromRow($row);
    }

    public function findByEmail(string $email): ?Client
    {
        $stmt = $this->pdo->prepare('SELECT * FROM clients WHERE email = ?');
        $stmt->execute([$email]);
        $row = $stmt->fetch();
        return $row === false ? null : Client::fromRow($row);
    }

    public function create(array $data): Client
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO clients (nom, prenom, email, telephone, adresse, mot_de_passe)
             VALUES (?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $data['nom'],
            $data['prenom'],
            $data['email'],
            $data['telephone'] ?? null,
            $data['adresse'] ?? null,
            $data['mot_de_passe'],
        ]);

        $id = (int) $this->pdo->lastInsertId();
        return $this->findById($id) ?? throw new \RuntimeException('Client introuvable après création');
    }

    public function update(int $id, array $data): void
    {
        $stmt = $this->pdo->prepare(
            'UPDATE clients SET nom = ?, prenom = ?, email = ?, telephone = ?, adresse = ? WHERE id = ?'
        );
        $stmt->execute([
            $data['nom'],
            $data['prenom'],
            $data['email'],
            $data['telephone'] ?? null,
            $data['adresse'] ?? null,
            $id,
        ]);
    }

    public function updateMotDePasse(int $id, string $motDePasse): void
    {
        $stmt = $this->pdo->prepare('UPDATE clients SET mot_de_passe = ? WHERE id = ?');
        $stmt->execute([$motDePasse, $id]);
    }

    public function all(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM clients ORDER BY date_inscription DESC');
        return array_map(Client::fromRow(...), $stmt->fetchAll());
    }

    public function search(string $terme): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM clients WHERE nom LIKE ? OR prenom LIKE ? OR email LIKE ? ORDER BY nom'
        );
        $like = '%' . $terme . '%';
        $stmt->execute([$like, $like, $like]);
        return array_map(Client::fromRow(...), $stmt->fetchAll());
    }

    public function count(): int
    {
        return (int) $this->pdo->query('SELECT COUNT(*) FROM clients')->fetchColumn();
    }

    public function updateMotDePasseByHash(string $ancienHash, string $nouveauHash): void
    {
        $stmt = $this->pdo->prepare('UPDATE clients SET mot_de_passe = ? WHERE mot_de_passe = ?');
        $stmt->execute([$nouveauHash, $ancienHash]);
    }
}
