<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Interfaces\PaiementRepositoryInterface;
use App\Models\PaiementCommande;
use PDO;

class PaiementRepository implements PaiementRepositoryInterface
{
    public function __construct(private PDO $pdo) {}

    public function paginer(?string $statut, int $page, int $perPage): array
    {
        $where = $this->whereStatutPaiement($statut);

        $sqlBase = 'SELECT c.id, c.client_id, c.date_commande, c.montant_total,
                           cl.nom, cl.prenom, cl.email, cl.telephone,
                           COALESCE(SUM(p.montant), 0) AS montant_paye
                    FROM commandes c
                    JOIN clients cl ON cl.id = c.client_id
                    LEFT JOIN paiements p ON p.commande_id = c.id
                    GROUP BY c.id, c.client_id, c.date_commande, c.montant_total,
                             cl.nom, cl.prenom, cl.email, cl.telephone'
            . $where['having'];

        $total = (int) $this->pdo->query('SELECT COUNT(*) AS total FROM (' . $sqlBase . ') AS sous')->fetch()->total;

        $offset = ($page - 1) * $perPage;
        $stmt = $this->pdo->prepare($sqlBase . ' ORDER BY c.date_commande DESC, c.id DESC LIMIT ? OFFSET ?');
        $stmt->bindValue(1, $perPage, PDO::PARAM_INT);
        $stmt->bindValue(2, $offset, PDO::PARAM_INT);
        $stmt->execute();

        $commandes = array_map(PaiementCommande::fromRow(...), $stmt->fetchAll());

        return [
            'commandes' => $commandes,
            'page' => $page,
            'totalPages' => (int) max(1, ceil($total / $perPage)),
            'total' => $total,
        ];
    }

    public function statistiques(): array
    {
        $totalEncaise = (float) $this->pdo->query('SELECT COALESCE(SUM(montant), 0) AS total FROM paiements')->fetch()->total;

        $impayees = (int) $this->pdo->query(
            'SELECT COUNT(*) AS total FROM commandes c
             WHERE NOT EXISTS (SELECT 1 FROM paiements p WHERE p.commande_id = c.id)'
        )->fetch()->total;

        $partiellement = (int) $this->pdo->query(
            'SELECT COUNT(*) AS total
             FROM commandes c
             JOIN (SELECT commande_id, SUM(montant) AS montant_paye
                   FROM paiements GROUP BY commande_id) sp ON sp.commande_id = c.id
             WHERE sp.montant_paye < c.montant_total'
        )->fetch()->total;

        return [
            'totalEncaise' => $totalEncaise,
            'impayees' => $impayees,
            'partiellement' => $partiellement,
        ];
    }

    public function commandesPayables(): array
    {
        $stmt = $this->pdo->query(
            'SELECT c.id, c.client_id, c.date_commande, c.montant_total,
                    cl.nom, cl.prenom, cl.email, cl.telephone,
                    COALESCE(SUM(p.montant), 0) AS montant_paye
             FROM commandes c
             JOIN clients cl ON cl.id = c.client_id
             LEFT JOIN paiements p ON p.commande_id = c.id
             GROUP BY c.id, c.client_id, c.date_commande, c.montant_total,
                      cl.nom, cl.prenom, cl.email, cl.telephone
             HAVING COALESCE(SUM(p.montant), 0) < c.montant_total
             ORDER BY c.date_commande DESC, c.id DESC'
        );

        return array_map(PaiementCommande::fromRow(...), $stmt->fetchAll());
    }

    public function resume(int $commandeId): ?PaiementCommande
    {
        $stmt = $this->pdo->prepare(
            'SELECT c.id, c.client_id, c.date_commande, c.montant_total,
                    cl.nom, cl.prenom, cl.email, cl.telephone,
                    COALESCE(SUM(p.montant), 0) AS montant_paye
             FROM commandes c
             JOIN clients cl ON cl.id = c.client_id
             LEFT JOIN paiements p ON p.commande_id = c.id
             WHERE c.id = ?
             GROUP BY c.id, c.client_id, c.date_commande, c.montant_total,
                      cl.nom, cl.prenom, cl.email, cl.telephone'
        );
        $stmt->execute([$commandeId]);
        $row = $stmt->fetch();

        return $row === false ? null : PaiementCommande::fromRow($row);
    }

    public function payer(int $commandeId, float $montant, string $mode): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO paiements (commande_id, montant, mode_paiement) VALUES (?, ?, ?)'
        );
        $stmt->execute([$commandeId, $montant, $mode]);
    }

    /**
     * Clause HAVING associée au statut de paiement demandé.
     *
     * @return array{having: string}
     */
    private function whereStatutPaiement(?string $statut): array
    {
        return match ($statut) {
            PaiementCommande::STATUT_PAYEE => ['having' => ' HAVING COALESCE(SUM(p.montant), 0) >= c.montant_total'],
            PaiementCommande::STATUT_IMPAYEE => ['having' => ' HAVING COALESCE(SUM(p.montant), 0) <= 0'],
            PaiementCommande::STATUT_PARTIEL => [
                'having' => ' HAVING COALESCE(SUM(p.montant), 0) > 0 AND COALESCE(SUM(p.montant), 0) < c.montant_total',
            ],
            default => ['having' => ''],
        };
    }
}