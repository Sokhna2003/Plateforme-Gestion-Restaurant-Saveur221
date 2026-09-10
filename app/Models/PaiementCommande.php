<?php

declare(strict_types=1);

namespace App\Models;

use stdClass;

/**
 * Résumé d'une commande avec son état de paiement (montant commandé,
 * montant payé, reste à payer) utilisé par la page de gestion des paiements.
 */
class PaiementCommande
{
    public const STATUT_PAYEE = 'payee';
    public const STATUT_IMPAYEE = 'impayee';
    public const STATUT_PARTIEL = 'partielle';

    public const LIBELLES = [
        self::STATUT_PAYEE => 'Payée',
        self::STATUT_IMPAYEE => 'Impayée',
        self::STATUT_PARTIEL => 'Partiellement payée',
    ];

    public function __construct(
        public readonly int $id,
        public readonly string $dateCommande,
        public readonly float $montantTotal,
        public readonly float $montantPaye,
        public readonly int $clientId,
        public readonly ?string $clientNom = null,
        public readonly ?string $clientPrenom = null,
        public readonly ?string $clientEmail = null,
        public readonly ?string $clientTelephone = null,
    ) {}

    public static function fromRow(stdClass $row): self
    {
        return new self(
            id: (int) $row->id,
            dateCommande: $row->date_commande,
            montantTotal: (float) $row->montant_total,
            montantPaye: (float) ($row->montant_paye ?? 0),
            clientId: (int) $row->client_id,
            clientNom: $row->nom ?? null,
            clientPrenom: $row->prenom ?? null,
            clientEmail: $row->email ?? null,
            clientTelephone: $row->telephone ?? null,
        );
    }

    public function clientNomComplet(): string
    {
        return trim(($this->clientPrenom ?? '') . ' ' . ($this->clientNom ?? ''));
    }

    public function montantRestant(): float
    {
        return max(0.0, $this->montantTotal - $this->montantPaye);
    }

    public function statutPaiement(): string
    {
        if ($this->montantPaye >= $this->montantTotal) {
            return self::STATUT_PAYEE;
        }
        if ($this->montantPaye <= 0) {
            return self::STATUT_IMPAYEE;
        }
        return self::STATUT_PARTIEL;
    }

    public function libelleStatut(): string
    {
        return self::LIBELLES[$this->statutPaiement()] ?? $this->statutPaiement();
    }

    public function dateFormatee(): string
    {
        return date('d/m/Y H:i', strtotime($this->dateCommande));
    }

    public function montantFormate(): string
    {
        return number_format($this->montantTotal, 0, ',', ' ') . ' FCFA';
    }

    public function payeFormate(): string
    {
        return number_format($this->montantPaye, 0, ',', ' ') . ' FCFA';
    }

    public function restantFormate(): string
    {
        return number_format($this->montantRestant(), 0, ',', ' ') . ' FCFA';
    }
}