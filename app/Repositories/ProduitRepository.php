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
               p.seuil_alerte, p.disponible, p.image, p.date_ajout, p.supprime_le,
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

    /**
     * Pagination de la liste d'administration (tous les produits,
     * y compris indisponibles) avec filtres optionnels.
     *
     * @return Produit[]
     */
    public function paginerAdministration(
        int $page,
        int $perPage = 8,
        ?int $categorieId = null,
        ?string $disponible = null,
        ?string $motCle = null
    ): array {
        $page = max(1, $page);
        $offset = ($page - 1) * $perPage;

        [$sql, $params] = $this->buildAdministrationWhere($categorieId, $disponible, $motCle);

        $sql .= ' ORDER BY p.date_ajout DESC, p.id DESC LIMIT ? OFFSET ?';

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

    public function compterAdministration(
        ?int $categorieId = null,
        ?string $disponible = null,
        ?string $motCle = null
    ): int {
        [$sql, $params] = $this->buildAdministrationWhere($categorieId, $disponible, $motCle, true);
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    public function definirDisponibilite(int $id, bool $disponible): void
    {
        $this->pdo->prepare('UPDATE produits SET disponible = ? WHERE id = ?')
            ->execute([$disponible ? 1 : 0, $id]);
    }

    /**
     * Construit le WHERE de la liste d'administration (hors corbeille).
     *
     * @return array{0: string, 1: array<int, int|string>}
     */
    private function buildAdministrationWhere(
        ?int $categorieId = null,
        ?string $disponible = null,
        ?string $motCle = null,
        bool $countOnly = false
    ): array {
        $sql = $countOnly
            ? 'SELECT COUNT(*) FROM produits p JOIN categories c ON p.categorie_id = c.id'
            : self::SELECT_WITH_CATEGORIE;
        $sql .= ' WHERE p.supprime_le IS NULL';
        $params = [];

        if ($disponible !== null && $disponible !== '') {
            $sql .= ' AND p.disponible = ?';
            $params[] = $disponible === '1' ? 1 : 0;
        }
        if ($categorieId !== null) {
            $sql .= ' AND p.categorie_id = ?';
            $params[] = $categorieId;
        }
        if ($motCle !== null && $motCle !== '') {
            $sql .= ' AND (p.libelle LIKE ? OR p.description LIKE ?)';
            $like = '%' . $motCle . '%';
            $params[] = $like;
            $params[] = $like;
        }

        return [$sql, $params];
    }

    public function create(array $data): Produit
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO produits (libelle, description, prix, quantite_stock, seuil_alerte, categorie_id, disponible, image, date_ajout)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())'
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
        $this->pdo->prepare('UPDATE produits SET supprime_le = NOW() WHERE id = ?')->execute([$id]);
    }

    /** @return Produit[] */
    public function trashed(string $terme = ''): array
    {
        $sql = self::SELECT_WITH_CATEGORIE . ' WHERE p.supprime_le IS NOT NULL';
        $params = [];
        if ($terme !== '') {
            $sql .= ' AND p.libelle LIKE ?';
            $params[] = '%' . $terme . '%';
        }
        $sql .= ' ORDER BY p.supprime_le DESC';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $this->hydrate($stmt->fetchAll());
    }

    public function findTrashedById(int $id): ?Produit
    {
        $stmt = $this->pdo->prepare(self::SELECT_WITH_CATEGORIE . ' WHERE p.id = ? AND p.supprime_le IS NOT NULL');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row === false ? null : Produit::fromRow($row);
    }

    public function restore(int $id): void
    {
        $this->pdo->prepare('UPDATE produits SET supprime_le = NULL WHERE id = ?')->execute([$id]);
    }

    public function forceDelete(int $id): void
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
