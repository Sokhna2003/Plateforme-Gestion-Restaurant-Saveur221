<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Interfaces\UtilisateurRepositoryInterface;
use App\Models\Utilisateur;
use PDO;

class UtilisateurRepository implements UtilisateurRepositoryInterface
{
    private const SELECT_WITH_ROLE = <<<'SQL'
        SELECT u.*, r.libelle AS role_libelle
        FROM utilisateurs u
        JOIN roles r ON u.role_id = r.id
        SQL;

    public function __construct(private PDO $pdo) {}

    public function findById(int $id): ?Utilisateur
    {
        $stmt = $this->pdo->prepare(self::SELECT_WITH_ROLE . ' WHERE u.id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row === false ? null : Utilisateur::fromRow($row);
    }

    public function findByEmail(string $email): ?Utilisateur
    {
        $stmt = $this->pdo->prepare(self::SELECT_WITH_ROLE . ' WHERE u.email = ?');
        $stmt->execute([$email]);
        $row = $stmt->fetch();
        return $row === false ? null : Utilisateur::fromRow($row);
    }

    public function all(): array
    {
        $stmt = $this->pdo->query(self::SELECT_WITH_ROLE . ' ORDER BY u.nom');
        return array_map(Utilisateur::fromRow(...), $stmt->fetchAll());
    }

    public function search(string $terme): array
    {
        $stmt = $this->pdo->prepare(
            self::SELECT_WITH_ROLE . ' WHERE u.nom LIKE ? OR u.prenom LIKE ? OR u.email LIKE ? ORDER BY u.nom'
        );
        $like = '%' . $terme . '%';
        $stmt->execute([$like, $like, $like]);
        return array_map(Utilisateur::fromRow(...), $stmt->fetchAll());
    }

    public function count(): int
    {
        return (int) $this->pdo->query('SELECT COUNT(*) FROM utilisateurs')->fetchColumn();
    }

    public function listerRoles(): array
    {
        return $this->pdo->query('SELECT id, libelle FROM roles ORDER BY id')->fetchAll();
    }

    public function paginer(int $page, int $perPage, ?int $roleId, ?string $actif, ?string $motCle): array
    {
        $page = max(1, $page);
        $offset = ($page - 1) * $perPage;

        [$sql, $params] = $this->buildWhere($roleId, $actif, $motCle);

        $sql .= ' ORDER BY u.date_creation DESC, u.id DESC LIMIT ? OFFSET ?';

        $stmt = $this->pdo->prepare($sql);
        $i = 1;
        foreach ($params as $valeur) {
            $stmt->bindValue($i++, $valeur);
        }
        $stmt->bindValue($i++, $perPage, PDO::PARAM_INT);
        $stmt->bindValue($i++, $offset, PDO::PARAM_INT);

        $stmt->execute();
        return array_map(Utilisateur::fromRow(...), $stmt->fetchAll());
    }

    public function compterGestion(?int $roleId, ?string $actif, ?string $motCle): int
    {
        [$sql, $params] = $this->buildWhere($roleId, $actif, $motCle, true);
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    public function compterParRole(int $roleId): int
    {
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM utilisateurs WHERE role_id = ?');
        $stmt->execute([$roleId]);
        return (int) $stmt->fetchColumn();
    }

    public function compterActifs(): int
    {
        return (int) $this->pdo->query('SELECT COUNT(*) FROM utilisateurs WHERE actif = 1')->fetchColumn();
    }

    public function create(array $data): Utilisateur
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe, role_id, actif)
             VALUES (?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $data['nom'],
            $data['prenom'],
            $data['email'],
            $data['mot_de_passe'],
            $data['role_id'],
            $data['actif'] ? 1 : 0,
        ]);

        $id = (int) $this->pdo->lastInsertId();
        return $this->findById($id) ?? throw new \RuntimeException('Utilisateur introuvable après création');
    }

    public function update(int $id, array $data): void
    {
        $sql = 'UPDATE utilisateurs SET nom = ?, prenom = ?, email = ?, role_id = ?, actif = ?';
        $params = [
            $data['nom'],
            $data['prenom'],
            $data['email'],
            $data['role_id'],
            $data['actif'] ? 1 : 0,
        ];

        if (!empty($data['mot_de_passe'])) {
            $sql .= ', mot_de_passe = ?';
            $params[] = $data['mot_de_passe'];
        }

        $sql .= ' WHERE id = ?';
        $params[] = $id;

        $this->pdo->prepare($sql)->execute($params);
    }

    public function definirActif(int $id, bool $actif): void
    {
        $this->pdo->prepare('UPDATE utilisateurs SET actif = ? WHERE id = ?')
            ->execute([$actif ? 1 : 0, $id]);
    }

    public function delete(int $id): void
    {
        $this->pdo->prepare('DELETE FROM utilisateurs WHERE id = ?')->execute([$id]);
    }

    /**
     * Construit le WHERE de la liste de gestion.
     *
     * @return array{0: string, 1: array<int, int|string>}
     */
    private function buildWhere(?int $roleId, ?string $actif, ?string $motCle, bool $countOnly = false): array
    {
        $sql = $countOnly
            ? 'SELECT COUNT(*) FROM utilisateurs u JOIN roles r ON u.role_id = r.id'
            : self::SELECT_WITH_ROLE;
        $sql .= ' WHERE 1 = 1';
        $params = [];

        if ($roleId !== null) {
            $sql .= ' AND u.role_id = ?';
            $params[] = $roleId;
        }
        if ($actif !== null && $actif !== '') {
            $sql .= ' AND u.actif = ?';
            $params[] = $actif === '1' ? 1 : 0;
        }
        if ($motCle !== null && $motCle !== '') {
            $sql .= ' AND (u.nom LIKE ? OR u.prenom LIKE ? OR u.email LIKE ?)';
            $like = '%' . $motCle . '%';
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
        }

        return [$sql, $params];
    }
}