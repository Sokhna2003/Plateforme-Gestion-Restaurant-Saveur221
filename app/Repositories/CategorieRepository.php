<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Interfaces\CategorieRepositoryInterface;
use App\Models\Categorie;
use PDO;

class CategorieRepository implements CategorieRepositoryInterface
{
    private const SELECT_BASE = <<<'SQL'
        SELECT c.*, COUNT(p.id) AS nombre_de_produits
        FROM categories c
        LEFT JOIN produits p ON p.categorie_id = c.id AND p.supprime_le IS NULL
        SQL;

    private const GROUP_BASE = 'GROUP BY c.id, c.nom, c.description, c.image, c.date_ajout, c.supprime_le ORDER BY c.date_ajout DESC';

    public function __construct(private PDO $pdo) {}

    public function all(): array
    {
        $stmt = $this->pdo->query(
            self::SELECT_BASE . ' WHERE c.supprime_le IS NULL ' . static::GROUP_BASE
        );
        return $this->hydrate($stmt->fetchAll());
    }

    public function search(string $terme): array
    {
        $stmt = $this->pdo->prepare(
            self::SELECT_BASE .
            ' WHERE c.supprime_le IS NULL AND (c.nom LIKE ? OR c.description LIKE ?) ' . static::GROUP_BASE
        );
        $like = '%' . $terme . '%';
        $stmt->execute([$like, $like]);
        return $this->hydrate($stmt->fetchAll());
    }

    public function paginate(int $page, int $perPage, string $terme = ''): array
    {
        $page = max(1, $page);
        $offset = ($page - 1) * $perPage;

        $sql = self::SELECT_BASE . ' WHERE c.supprime_le IS NULL';
        $params = [];

        if ($terme !== '') {
            $sql .= ' AND (c.nom LIKE ? OR c.description LIKE ?)';
            $like = '%' . $terme . '%';
            $params[] = $like;
            $params[] = $like;
        }

        $sql = self::SELECT_BASE .
            ' WHERE c.supprime_le IS NULL GROUP BY c.id, c.nom, c.description, c.image, c.date_ajout, c.supprime_le ORDER BY c.date_ajout DESC LIMIT ? OFFSET ?';

        $stmt = $this->pdo->prepare($sql);
        $i = 1;
        foreach ($params as $valeur) {
            $stmt->bindValue($i++, $valeur);
        }
        $stmt->bindValue($i++, $perPage, PDO::PARAM_INT);
        $stmt->bindValue($i++, $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $this->hydrate($stmt->fetchAll());
    }

    public function compter(string $terme = ''): int
    {
        $sql = 'SELECT COUNT(*) FROM categories WHERE supprime_le IS NULL';
        $params = [];
        if ($terme !== '') {
            $sql .= ' AND (nom LIKE ? OR description LIKE ?)';
            $like = '%' . $terme . '%';
            $params[] = $like;
            $params[] = $like;
        }
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    public function findById(int $id): ?Categorie
    {
        $stmt = $this->pdo->prepare(
            self::SELECT_BASE . ' WHERE c.id = ? AND c.supprime_le IS NULL ' . static::GROUP_BASE
        );
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row === false ? null : Categorie::fromRow($row);
    }

    public function findByNom(string $nom): ?Categorie
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM categories WHERE nom = ? AND supprime_le IS NULL'
        );
        $stmt->execute([$nom]);
        $row = $stmt->fetch();
        return $row === false ? null : Categorie::fromRow($row);
    }

    public function create(array $data): Categorie
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO categories (nom, description, image) VALUES (?, ?, ?)'
        );
        $stmt->execute([
            $data['nom'],
            $data['description'] ?? null,
            $data['image'] ?? null,
        ]);

        $id = (int) $this->pdo->lastInsertId();
        return $this->findById($id) ?? throw new \RuntimeException('Catégorie introuvable après création');
    }

    public function update(int $id, array $data): void
    {
        $stmt = $this->pdo->prepare(
            'UPDATE categories SET nom = ?, description = ?, image = ? WHERE id = ? AND supprime_le IS NULL'
        );
        $stmt->execute([
            $data['nom'],
            $data['description'] ?? null,
            $data['image'] ?? null,
            $id,
        ]);
    }

    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare('UPDATE categories SET supprime_le = NOW() WHERE id = ?');
        $stmt->execute([$id]);
    }

    /** @return Categorie[] */
    public function trashed(string $terme = ''): array
    {
        $sql = self::SELECT_BASE . ' WHERE c.supprime_le IS NOT NULL';
        $params = [];
        if ($terme !== '') {
            $sql .= ' AND c.nom LIKE ?';
            $params[] = '%' . $terme . '%';
        }
        $sql .= ' ' . static::GROUP_BASE;
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $this->hydrate($stmt->fetchAll());
    }

    public function findTrashedById(int $id): ?Categorie
    {
        $stmt = $this->pdo->prepare(
            self::SELECT_BASE . ' WHERE c.id = ? AND c.supprime_le IS NOT NULL ' . static::GROUP_BASE
        );
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row === false ? null : Categorie::fromRow($row);
    }

    public function restore(int $id): void
    {
        $stmt = $this->pdo->prepare('UPDATE categories SET supprime_le = NULL WHERE id = ?');
        $stmt->execute([$id]);
    }

    public function forceDelete(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM categories WHERE id = ?');
        $stmt->execute([$id]);
    }

    public function compterProduits(int $categorieId, bool $avecCorbeille = false): int
    {
        $sql = 'SELECT COUNT(*) FROM produits WHERE categorie_id = ?';
        if (!$avecCorbeille) {
            $sql .= ' AND supprime_le IS NULL';
        }
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$categorieId]);
        return (int) $stmt->fetchColumn();
    }

    public function count(): int
    {
        return (int) $this->pdo->query(
            'SELECT COUNT(*) FROM categories WHERE supprime_le IS NULL'
        )->fetchColumn();
    }

    /** @param \stdClass[] $rows */
    private function hydrate(array $rows): array
    {
        return array_map(Categorie::fromRow(...), $rows);
    }
}