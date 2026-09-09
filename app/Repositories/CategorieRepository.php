<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Interfaces\CategorieRepositoryInterface;
use App\Models\Categorie;
use PDO;

class CategorieRepository implements CategorieRepositoryInterface
{
    public function __construct(private PDO $pdo) {}

    public function all(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM categories ORDER BY nom');
        return array_map(Categorie::fromRow(...), $stmt->fetchAll());
    }

    public function findById(int $id): ?Categorie
    {
        $stmt = $this->pdo->prepare('SELECT * FROM categories WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row === false ? null : Categorie::fromRow($row);
    }

    public function create(array $data): Categorie
    {
        $stmt = $this->pdo->prepare('INSERT INTO categories (nom, description) VALUES (?, ?)');
        $stmt->execute([$data['nom'], $data['description'] ?? null]);

        $id = (int) $this->pdo->lastInsertId();
        return $this->findById($id) ?? throw new \RuntimeException('Catégorie introuvable après création');
    }

    public function update(int $id, array $data): void
    {
        $stmt = $this->pdo->prepare('UPDATE categories SET nom = ?, description = ? WHERE id = ?');
        $stmt->execute([$data['nom'], $data['description'] ?? null, $id]);
    }

    public function delete(int $id): void
    {
        $this->pdo->prepare('DELETE FROM categories WHERE id = ?')->execute([$id]);
    }

    public function count(): int
    {
        return (int) $this->pdo->query('SELECT COUNT(*) FROM categories')->fetchColumn();
    }
}
