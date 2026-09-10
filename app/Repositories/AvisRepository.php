<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Interfaces\AvisRepositoryInterface;
use App\Models\Avis;
use PDO;

class AvisRepository implements AvisRepositoryInterface
{
    private const SELECT_BASE = "
        SELECT a.*,
               c.nom AS client_nom,
               c.prenom AS client_prenom,
               c.email AS client_email,
               c.telephone AS client_telephone,
               c.adresse AS client_adresse,
               co.date_commande AS commande_date,
               co.statut AS commande_statut,
               co.montant_total AS commande_montant_total
        FROM avis a
        JOIN clients c ON c.id = a.client_id
        JOIN commandes co ON co.id = a.commande_id
    ";

    public function __construct(private PDO $pdo) {}

    public function findById(int $id): ?Avis
    {
        $stmt = $this->pdo->prepare(self::SELECT_BASE . ' WHERE a.id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row === false ? null : Avis::fromRow($row);
    }

    /** @return Avis[] */
    public function paginer(?string $terme = null, ?int $note = null, int $page = 1, int $perPage = 8): array
    {
        [$where, $params] = $this->buildWhere($terme, $note);
        $offset = ($page - 1) * $perPage;
        $stmt = $this->pdo->prepare(
            self::SELECT_BASE . $where
            . ' ORDER BY a.date_avis DESC, a.id DESC LIMIT ' . $perPage . ' OFFSET ' . $offset
        );
        $stmt->execute($params);
        return array_map(Avis::fromRow(...), $stmt->fetchAll());
    }

    public function compterGestion(?string $terme = null, ?int $note = null): int
    {
        [$where, $params] = $this->buildWhere($terme, $note);
        $stmt = $this->pdo->prepare(
            'SELECT COUNT(*) FROM avis a JOIN clients c ON c.id = a.client_id ' . $where
        );
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    public function count(): int
    {
        return (int) $this->pdo->query('SELECT COUNT(*) FROM avis')->fetchColumn();
    }

    public function noteMoyenne(): float
    {
        return (float) $this->pdo->query('SELECT COALESCE(AVG(note), 0) FROM avis')->fetchColumn();
    }

    public function compterNote(int $note): int
    {
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM avis WHERE note = ?');
        $stmt->execute([$note]);
        return (int) $stmt->fetchColumn();
    }

    public function compterAvecCommentaire(): int
    {
        return (int) $this->pdo->query(
            'SELECT COUNT(*) FROM avis WHERE commentaire IS NOT NULL AND commentaire <> \'\''
        )->fetchColumn();
    }

    public function supprimer(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM avis WHERE id = ?');
        $stmt->execute([$id]);
    }

    /**
     * @return array{string, array<int, int|string>}
     */
    private function buildWhere(?string $terme, ?int $note): array
    {
        $conditions = [];
        $params = [];

        if ($terme !== null && trim($terme) !== '') {
            $conditions[] = '(c.nom LIKE ? OR c.prenom LIKE ? OR c.email LIKE ? OR a.commentaire LIKE ?)';
            $like = '%' . trim($terme) . '%';
            array_push($params, $like, $like, $like, $like);
        }

        if ($note !== null) {
            $conditions[] = 'a.note = ?';
            $params[] = $note;
        }

        return [($conditions === [] ? '' : 'WHERE ' . implode(' AND ', $conditions)), $params];
    }
}