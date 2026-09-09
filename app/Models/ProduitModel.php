<?php

namespace App\Models;

use App\Core\Model;
use PDO;

/**
 * Accès aux données de la table "produits". Equivalent PHP du
 * ProduitRepository.java (memes tables, meme base partagee).
 *
 * all() et find() sont redefinies (surchargees) par rapport a la version
 * generique du Model parent, car un produit a toujours besoin d'etre
 * jointe a sa categorie pour l'affichage (categorie_nom).
 */
class ProduitModel extends Model
{
    protected $table = 'produits';

    private const SELECT_BASE = "
        SELECT p.id, p.libelle, p.description, p.prix, p.quantite_stock,
               p.seuil_alerte, p.disponible, p.image,
               c.id AS categorie_id, c.nom AS categorie_nom
        FROM produits p
        JOIN categories c ON p.categorie_id = c.id
    ";

    public function all()
    {
        $stmt = $this->db->query(self::SELECT_BASE . " ORDER BY p.libelle");
        return $stmt->fetchAll();
    }

    public function find($id)
    {
        $stmt = $this->db->prepare(self::SELECT_BASE . " WHERE p.id = ?");
        $stmt->execute([$id]);
        $produit = $stmt->fetch();
        return $produit ?: null;
    }

    public function disponibles()
    {
        $stmt = $this->db->query(self::SELECT_BASE . " WHERE p.disponible = 1 ORDER BY p.libelle");
        return $stmt->fetchAll();
    }

    public function search(string $motCle)
    {
        $stmt = $this->db->prepare(self::SELECT_BASE . " WHERE p.disponible = 1 AND p.libelle LIKE ? ORDER BY p.libelle");
        $stmt->execute(['%' . $motCle . '%']);
        return $stmt->fetchAll();
    }

    public function parCategorie(int $categorieId)
    {
        $stmt = $this->db->prepare(self::SELECT_BASE . " WHERE p.disponible = 1 AND c.id = ? ORDER BY p.libelle");
        $stmt->execute([$categorieId]);
        return $stmt->fetchAll();
    }

    /**
     * Produits de la meme categorie qu'un produit donne, pour la section
     * "Vous pourriez aussi aimer" de la page detail.
     */
    public function similaires(int $categorieId, int $excludeId, int $limite = 3)
    {
        $stmt = $this->db->prepare(self::SELECT_BASE .
            " WHERE p.disponible = 1 AND c.id = ? AND p.id != ? ORDER BY p.libelle LIMIT ?");
        $stmt->bindValue(1, $categorieId, PDO::PARAM_INT);
        $stmt->bindValue(2, $excludeId, PDO::PARAM_INT);
        $stmt->bindValue(3, $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Pagination du catalogue, avec recherche et filtre par categorie
     * optionnels. Adaptee de la fonction getByPage() vue en cours
     * (meme principe LIMIT/OFFSET), avec en plus la jointure categorie
     * et les filtres necessaires au catalogue.
     */
    public function paginate(int $page, int $perPage = 8, ?int $categorieId = null, ?string $motCle = null): array
    {
        $page = max(1, $page);
        $offset = ($page - 1) * $perPage;

        $sql = self::SELECT_BASE . " WHERE p.disponible = 1";
        $params = [];

        if ($motCle !== null && $motCle !== '') {
            $sql .= " AND p.libelle LIKE ?";
            $params[] = '%' . $motCle . '%';
        }
        if ($categorieId !== null) {
            $sql .= " AND c.id = ?";
            $params[] = $categorieId;
        }

        $sql .= " ORDER BY p.libelle LIMIT ? OFFSET ?";

        $stmt = $this->db->prepare($sql);

        $i = 1;
        foreach ($params as $valeur) {
            $stmt->bindValue($i++, $valeur);
        }
        $stmt->bindValue($i++, $perPage, PDO::PARAM_INT);
        $stmt->bindValue($i++, $offset, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Nombre total de produits correspondant aux memes filtres que
     * paginate(), pour calculer le nombre de pages.
     */
    public function compter(?int $categorieId = null, ?string $motCle = null): int
    {
        $sql = "SELECT COUNT(*) AS total FROM produits p
                JOIN categories c ON p.categorie_id = c.id
                WHERE p.disponible = 1";
        $params = [];

        if ($motCle !== null && $motCle !== '') {
            $sql .= " AND p.libelle LIKE ?";
            $params[] = '%' . $motCle . '%';
        }
        if ($categorieId !== null) {
            $sql .= " AND c.id = ?";
            $params[] = $categorieId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetch()->total;
    }
}
