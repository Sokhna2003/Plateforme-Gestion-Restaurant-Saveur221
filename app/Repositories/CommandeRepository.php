<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Interfaces\CommandeRepositoryInterface;
use App\Models\Commande;
use App\Models\LigneCommande;
use PDO;
use stdClass;

class CommandeRepository implements CommandeRepositoryInterface
{
    /**
     * Sélection de commande avec résumé des articles (libelles concaténés,
     * quantité totale, prix le plus bas) et informations du client.
     */
    private const SELECT_AVEC_RESUME = <<<'SQL'
SELECT c.id, c.date_commande, c.statut, c.montant_total,
       cl.id AS client_id, cl.nom AS client_nom, cl.prenom AS client_prenom,
       cl.email AS client_email, cl.telephone AS client_telephone, cl.adresse AS client_adresse,
       GROUP_CONCAT(p.libelle ORDER BY lc.id SEPARATOR ', ') AS produits_libelles,
       COUNT(DISTINCT p.id) AS nb_produits,
       SUM(lc.quantite) AS quantite_totale,
       MIN(lc.prix_unitaire) AS prix_unitaire
FROM commandes c
JOIN clients cl ON cl.id = c.client_id
JOIN ligne_commandes lc ON lc.commande_id = c.id
JOIN produits p ON p.id = lc.produit_id
SQL;

    private const GROUP_BY = <<<'SQL'
GROUP BY c.id, c.date_commande, c.statut, c.montant_total, cl.id, cl.nom, cl.prenom,
         cl.email, cl.telephone, cl.adresse
SQL;

    public function __construct(private PDO $pdo) {}

    /** @return Commande[] */
    public function paginer(?string $terme = null, ?string $statut = null, int $page = 1, int $perPage = 8): array
    {
        $page = max(1, $page);
        $offset = ($page - 1) * $perPage;

        [$where, $params] = $this->buildWhere($terme, $statut);

        $sql = self::SELECT_AVEC_RESUME
            . $where
            . ' ' . self::GROUP_BY
            . ' ORDER BY c.date_commande DESC, c.id DESC'
            . ' LIMIT ? OFFSET ?';

        $stmt = $this->pdo->prepare($sql);
        $i = 1;
        foreach ($params as $valeur) {
            $stmt->bindValue($i++, $valeur);
        }
        $stmt->bindValue($i++, $perPage, PDO::PARAM_INT);
        $stmt->bindValue($i++, $offset, PDO::PARAM_INT);

        $stmt->execute();
        return array_map(Commande::fromRow(...), $stmt->fetchAll());
    }

    public function compter(?string $terme = null, ?string $statut = null): int
    {
        [$where, $params] = $this->buildWhere($terme, $statut);

        $sql = 'SELECT COUNT(DISTINCT c.id) FROM commandes c'
            . ' JOIN clients cl ON cl.id = c.client_id'
            . ' JOIN ligne_commandes lc ON lc.commande_id = c.id'
            . ' JOIN produits p ON p.id = lc.produit_id'
            . $where;

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    public function compterParStatut(string $statut): int
    {
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM commandes WHERE statut = ?');
        $stmt->execute([$statut]);
        return (int) $stmt->fetchColumn();
    }

    public function compterTotal(): int
    {
        return (int) $this->pdo->query('SELECT COUNT(*) FROM commandes')->fetchColumn();
    }

    public function chiffreAffairesJour(): float
    {
        return (float) $this->pdo->query(
            'SELECT COALESCE(SUM(montant_total), 0) FROM commandes WHERE DATE(date_commande) = CURDATE()'
        )->fetchColumn();
    }

    public function findById(int $id): ?Commande
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, date_commande, statut, montant_total, client_id FROM commandes WHERE id = ?'
        );
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        if ($row === false) {
            return null;
        }

        $client = $this->pdo->prepare('SELECT * FROM clients WHERE id = ?');
        $client->execute([$row->client_id]);
        $cl = $client->fetch();

        $ligne = $this->pdo->prepare('SELECT lc.produit_id, lc.prix_unitaire FROM ligne_commandes lc WHERE lc.commande_id = ? ORDER BY lc.id LIMIT 1');
        $ligne->execute([$id]);
        $premiere = $ligne->fetch();

        return new Commande(
            id: (int) $row->id,
            dateCommande: $row->date_commande,
            statut: $row->statut,
            montantTotal: (float) $row->montant_total,
            clientId: (int) $row->client_id,
            clientNom: $cl->nom ?? null,
            clientPrenom: $cl->prenom ?? null,
            clientEmail: $cl->email ?? null,
            clientTelephone: $cl->telephone ?? null,
            clientAdresse: $cl->adresse ?? null,
            produitsLibelles: null,
            nbProduits: null,
            quantiteTotale: null,
            prixUnitaire: $premiere !== false ? (float) $premiere->prix_unitaire : null,
        );
    }

    /** @return LigneCommande[] */
    public function lignes(int $commandeId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT lc.id, lc.commande_id, lc.produit_id, lc.quantite, lc.prix_unitaire,
                    p.libelle AS produit_libelle
             FROM ligne_commandes lc
             JOIN produits p ON p.id = lc.produit_id
             WHERE lc.commande_id = ?
             ORDER BY lc.id'
        );
        $stmt->execute([$commandeId]);
        return array_map(LigneCommande::fromRow(...), $stmt->fetchAll());
    }

    public function modifierStatut(int $id, string $statut): void
    {
        $stmt = $this->pdo->prepare('UPDATE commandes SET statut = ? WHERE id = ?');
        $stmt->execute([$statut, $id]);
    }

    /**
     * Crée une commande avec ses lignes (transaction gérée par l'appelant).
     * Le statut initial est EN_ATTENTE (valeur par défaut de la colonne).
     *
     * @param array<int, array{produit_id: int, quantite: int, prix_unitaire: float}> $lignes
     */
    public function creer(int $clientId, array $lignes, float $montantTotal): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO commandes (client_id, montant_total) VALUES (?, ?)'
        );
        $stmt->execute([$clientId, $montantTotal]);
        $commandeId = (int) $this->pdo->lastInsertId();

        $ligneStmt = $this->pdo->prepare(
            'INSERT INTO ligne_commandes (commande_id, produit_id, quantite, prix_unitaire)
             VALUES (?, ?, ?, ?)'
        );
        foreach ($lignes as $ligne) {
            $ligneStmt->execute([
                $commandeId,
                $ligne['produit_id'],
                $ligne['quantite'],
                $ligne['prix_unitaire'],
            ]);
        }

        return $commandeId;
    }

    /**
     * Commandes d'un client, la plus récente en premier.
     *
     * @return Commande[]
     */
    public function listerPourClient(int $clientId): array
    {
        $stmt = $this->pdo->prepare(
            self::SELECT_AVEC_RESUME
            . ' WHERE c.client_id = ?'
            . ' ' . self::GROUP_BY
            . ' ORDER BY c.date_commande DESC, c.id DESC'
        );
        $stmt->execute([$clientId]);
        return array_map(Commande::fromRow(...), $stmt->fetchAll());
    }

    public function trouverPaiement(int $commandeId): ?stdClass
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM paiements WHERE commande_id = ? ORDER BY date_paiement DESC LIMIT 1'
        );
        $stmt->execute([$commandeId]);
        $row = $stmt->fetch();
        return $row === false ? null : $row;
    }

    /**
     * Construit le WHERE des filtres (recherche client/produit + statut).
     *
     * @return array{0: string, 1: array<int, string>}
     */
    private function buildWhere(?string $terme, ?string $statut): array
    {
        $conditions = [];
        $params = [];

        if ($terme !== null && trim($terme) !== '') {
            $conditions[] = '(cl.nom LIKE ? OR cl.prenom LIKE ? OR cl.email LIKE ? OR p.libelle LIKE ?)';
            $like = '%' . trim($terme) . '%';
            $params = [$like, $like, $like, $like];
        }

        if ($statut !== null && in_array($statut, Commande::statuts(), true)) {
            $conditions[] = 'c.statut = ?';
            $params[] = $statut;
        }

        return [$conditions !== [] ? ' WHERE ' . implode(' AND ', $conditions) : '', $params];
    }
}