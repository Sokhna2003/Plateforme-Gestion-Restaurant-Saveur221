<?php

namespace App\Core;

/**
 * Classe mere de tous les Models : connexion PDO + operations generiques
 * (all, find, delete, paginate, count). Chaque Model enfant definit juste
 * $table et ajoute ses methodes propres (recherche, jointures, filtres...).
 *
 * paginate()/count() ici sont les versions simples (une seule table, pas
 * de jointure). Un Model qui a besoin de filtres ou de jointures (comme
 * ProduitModel) les redefinit lui-meme sur le meme principe.
 */
class Model
{
    protected $db;
    protected $table;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function all()
    {
        $stmt = $this->db->query("SELECT * FROM {$this->table}");
        return $stmt->fetchAll();
    }

    public function find($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Pagination simple (sans jointure ni filtre) : LIMIT/OFFSET classique.
     */
    public function paginate(int $page, int $perPage = 8): array
    {
        $page = max(1, $page);
        $offset = ($page - 1) * $perPage;

        $stmt = $this->db->prepare("SELECT * FROM {$this->table} ORDER BY id DESC LIMIT ? OFFSET ?");
        $stmt->bindValue(1, $perPage, \PDO::PARAM_INT);
        $stmt->bindValue(2, $offset, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function count(): int
    {
        $stmt = $this->db->query("SELECT COUNT(*) AS total FROM {$this->table}");
        return (int) $stmt->fetch()->total;
    }
}
