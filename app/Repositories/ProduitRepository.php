<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Interfaces\ProduitRepositoryInterface;
use App\Models\Produit;
use PDO;

class ProduitRepository implements ProduitRepositoryInterface
{
    private const SELECT_WITH_CATEGORIE = <<<'SQL'
        SELECT p.id, p.libelle, p.description, p.prix, p.quantite_stock,
               p.seuil_alerte, p.disponible, p.image,
               c.id AS categorie_id, c.nom AS categorie_nom
        FROM produits p
        JOIN categories c ON p.categorie_id = c.id
        SQL;

    public function __construct(private PDO $pdo) {}

    public function all(): array
    {
        $stmt = $this->pdo->query(self::SELECT_WITH_CATEGORIE . ' ORDER BY p.libelle');
        return $this->hydrate($stmt->fetchAll());
    }

    public function disponibles(): array
    {
        $stmt = $this->pdo->query(self::SELECT_WITH_CATEGORIE . ' WHERE p.disponible = 1 ORDER BY p.libelle');
        return $this->hydrate($stmt->fetchAll());
    }

    public function findById(int $id): ?Produit
    {
        $stmt = $this->pdo->prepare(self::SELECT_WITH_CATEGORIE . ' WHERE p.id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row === false ? null : Produit::fromRow($row);
    }

    public function search(string $motCle): array
    {
        $stmt = $this->pdo->prepare(self::SELECT_WITH_CATEGORIE . ' WHERE p.disponible = 1 AND p.libelle LIKE ? ORDER BY p.libelle');
        $stmt->execute(['%' . $motCle . '%']);
        return $this->hydrate($stmt->fetchAll());
    }

    public function parCategorie(int $categorieId): array
    {
        $stmt = $this->pdo->prepare(self::SELECT_WITH_CATEGORIE . ' WHERE p.disponible = 1 AND c.id = ? ORDER BY p.libelle');
        $stmt->execute([$categorieId]);
        return $this->hydrate($stmt->fetchAll());
    }

    public function similaires(int $categorieId, int $excludeId, int $limite = 3): array
    {
        $stmt = $this->pdo->prepare(self::SELECT_WITH_CATEGORIE .
            ' WHERE p.disponible = 1 AND c.id = ? AND p.id != ? ORDER BY p.libelle LIMIT ?');
        $stmt->bindValue(1, $categorieId, PDO::PARAM_INT);
        $stmt->bindValue(2, $excludeId, PDO::PARAM_INT);
        $stmt->bindValue(3, $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $this->hydrate($stmt->fetchAll());
    }

    public function paginate(int $page, int $perPage = 8, ?int $categorieId = null, ?string $motCle = null): array
    {
        $page = max(1, $page);
        $offset = ($page - 1) * $perPage;

        $sql = self::SELECT_WITH_CATEGORIE . ' WHERE p.disponible = 1';
        $params = [];

        if ($motCle !== null && $motCle !== '') {
            $sql .= ' AND p.libelle LIKE ?';
            $params[] = '%' . $motCle . '%';
        }
        if ($categorieId !== null) {
            $sql .= ' AND c.id = ?';
            $params[] = $categorieId;
        }

        $sql .= ' ORDER BY p.libelle LIMIT ? OFFSET ?';

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

    public function compter(?int $categorieId = null, ?string $motCle = null): int
    {
        $sql = 'SELECT COUNT(*) AS total FROM produits p
                JOIN categories c ON p.categorie_id = c.id
                WHERE p.disponible = 1';
        $params = [];

        if ($motCle !== null && $motCle !== '') {
            $sql .= ' AND p.libelle LIKE ?';
            $params[] = '%' . $motCle . '%';
        }
        if ($categorieId !== null) {
            $sql .= ' AND c.id = ?';
            $params[] = $categorieId;
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetch()->total;
    }

    public function create(array $data): Produit
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO produits (libelle, description, prix, quantite_stock, seuil_alerte, categorie_id, disponible, image)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $data['libelle'],
            $data['description'] ?? null,
            $data['prix'],
            $data['quantite_stock'] ?? 0,
            $data['seuil_alerte'] ?? 5,
            $data['categorie_id'],
            $data['disponible'] ?? true,
            $data['image'] ?? null,
        ]);

        $id = (int) $this->pdo->lastInsertId();
        return $this->findById($id) ?? throw new \RuntimeException('Produit introuvable après création');
    }

    public function update(int $id, array $data): void
    {
        $stmt = $this->pdo->prepare(
            'UPDATE produits SET libelle = ?, description = ?, prix = ?, quantite_stock = ?,
             seuil_alerte = ?, categorie_id = ?, disponible = ?, image = ? WHERE id = ?'
        );
        $stmt->execute([
            $data['libelle'],
            $data['description'] ?? null,
            $data['prix'],
            $data['quantite_stock'] ?? 0,
            $data['seuil_alerte'] ?? 5,
            $data['categorie_id'],
            $data['disponible'] ?? true,
            $data['image'] ?? null,
            $id,
        ]);
    }

    public function delete(int $id): void
    {
        $this->pdo->prepare('DELETE FROM produits WHERE id = ?')->execute([$id]);
    }

    public function decrementStock(int $id, int $quantite = 1): void
    {
        $this->pdo->prepare('UPDATE produits SET quantite_stock = quantite_stock - ? WHERE id = ?')
            ->execute([$quantite, $id]);
    }

    public function incrementStock(int $id, int $quantite = 1): void
    {
        $this->pdo->prepare('UPDATE produits SET quantite_stock = quantite_stock + ? WHERE id = ?')
            ->execute([$quantite, $id]);
    }

    /** @param \stdClass[] $rows */
    private function hydrate(array $rows): array
    {
        return array_map(Produit::fromRow(...), $rows);
    }
}
