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

    public function updateMotDePasseByHash(string $ancienHash, string $nouveauHash): void
    {
        $stmt = $this->pdo->prepare('UPDATE utilisateurs SET mot_de_passe = ? WHERE mot_de_passe = ?');
        $stmt->execute([$nouveauHash, $ancienHash]);
    }
}
