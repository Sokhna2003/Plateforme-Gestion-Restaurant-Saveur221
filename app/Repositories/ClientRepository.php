<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Interfaces\ClientRepositoryInterface;
use App\Models\Client;
use App\Models\Commande;
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

    /** @return Client[] */
    public function paginer(?string $terme = null, ?string $avecCommandes = null, int $page = 1, int $perPage = 8): array
    {
        [$where, $params] = $this->buildWhere($terme, $avecCommandes);
        $offset = ($page - 1) * $perPage;
        $stmt = $this->pdo->prepare(
            'SELECT c.*,
                    (SELECT COUNT(*) FROM commandes cm WHERE cm.client_id = c.id) AS nb_commandes,
                    (SELECT COALESCE(SUM(cm.montant_total), 0) FROM commandes cm WHERE cm.client_id = c.id) AS total_depense
             FROM clients c ' . $where
            . ' ORDER BY c.date_inscription DESC, c.id DESC LIMIT ' . $perPage . ' OFFSET ' . $offset
        );
        $stmt->execute($params);
        return array_map(Client::fromRow(...), $stmt->fetchAll());
    }

    public function compterGestion(?string $terme = null, ?string $avecCommandes = null): int
    {
        [$where, $params] = $this->buildWhere($terme, $avecCommandes);
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM clients c ' . $where);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    public function compterAvecCommandes(): int
    {
        return (int) $this->pdo->query('SELECT COUNT(DISTINCT client_id) FROM commandes')->fetchColumn();
    }

    public function compterCommandes(): int
    {
        return (int) $this->pdo->query('SELECT COUNT(*) FROM commandes')->fetchColumn();
    }

    public function chiffreAffaires(): float
    {
        return (float) $this->pdo->query('SELECT COALESCE(SUM(montant_total), 0) FROM commandes')->fetchColumn();
    }

    /** @return Commande[] */
    public function commandesPour(int $clientId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM commandes WHERE client_id = ? ORDER BY date_commande DESC, id DESC'
        );
        $stmt->execute([$clientId]);
        return array_map(Commande::fromRow(...), $stmt->fetchAll());
    }

    public function aDesCommandesOuAvis(int $clientId): bool
    {
        $stmt = $this->pdo->prepare(
            'SELECT (EXISTS (SELECT 1 FROM commandes WHERE client_id = ?)) OR (EXISTS (SELECT 1 FROM avis WHERE client_id = ?))'
        );
        $stmt->execute([$clientId, $clientId]);
        return (bool) $stmt->fetchColumn();
    }

    public function supprimer(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM clients WHERE id = ?');
        $stmt->execute([$id]);
    }

    /**
     * @return array{string, array<int, string>}
     */
    private function buildWhere(?string $terme, ?string $avecCommandes): array
    {
        $conditions = [];
        $params = [];

        if ($terme !== null && trim($terme) !== '') {
            $conditions[] = '(c.nom LIKE ? OR c.prenom LIKE ? OR c.email LIKE ? OR c.telephone LIKE ?)';
            $like = '%' . trim($terme) . '%';
            array_push($params, $like, $like, $like, $like);
        }

        if ($avecCommandes === 'avec') {
            $conditions[] = 'EXISTS (SELECT 1 FROM commandes cm WHERE cm.client_id = c.id)';
        } elseif ($avecCommandes === 'sans') {
            $conditions[] = 'NOT EXISTS (SELECT 1 FROM commandes cm WHERE cm.client_id = c.id)';
        }

        return [($conditions === [] ? '' : 'WHERE ' . implode(' AND ', $conditions)), $params];
    }
}
