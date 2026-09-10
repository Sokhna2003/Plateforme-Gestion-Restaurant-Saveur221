<?php

declare(strict_types=1);

namespace App\Models;

use DateTimeImmutable;
use stdClass;

class Commande
{
    public const STATUT_EN_ATTENTE = 'EN_ATTENTE';
    public const STATUT_EN_PREPARATION = 'EN_PREPARATION';
    public const STATUT_PRETE = 'PRETE';
    public const STATUT_RETIREE = 'RETIREE';
    public const STATUT_ANNULEE = 'ANNULEE';

    /** @var array<string, string> */
    public const LIBELLES = [
        self::STATUT_EN_ATTENTE => 'En attente',
        self::STATUT_EN_PREPARATION => 'En préparation',
        self::STATUT_PRETE => 'Prête',
        self::STATUT_RETIREE => 'Retirée',
        self::STATUT_ANNULEE => 'Annulée',
    ];

    /** @var array<string, string> */
    public const SUIVANTS = [
        self::STATUT_EN_ATTENTE => self::STATUT_EN_PREPARATION,
        self::STATUT_EN_PREPARATION => self::STATUT_PRETE,
        self::STATUT_PRETE => self::STATUT_RETIREE,
    ];

    /** @return string[] */
    public static function statuts(): array
    {
        return array_keys(self::LIBELLES);
    }

    public function __construct(
        public readonly int $id,
        public readonly string $dateCommande,
        public readonly string $statut,
        public readonly float $montantTotal,
        public readonly int $clientId,
        public readonly ?string $clientNom = null,
        public readonly ?string $clientPrenom = null,
        public readonly ?string $clientEmail = null,
        public readonly ?string $clientTelephone = null,
        public readonly ?string $clientAdresse = null,
        public readonly ?string $produitsLibelles = null,
        public readonly ?int $nbProduits = null,
        public readonly ?int $quantiteTotale = null,
        public readonly ?float $prixUnitaire = null,
    ) {}

    public static function fromRow(stdClass $row): self
    {
        return new self(
            id: (int) $row->id,
            dateCommande: $row->date_commande,
            statut: $row->statut,
            montantTotal: (float) $row->montant_total,
            clientId: (int) $row->client_id,
            clientNom: $row->client_nom ?? null,
            clientPrenom: $row->client_prenom ?? null,
            clientEmail: $row->client_email ?? null,
            clientTelephone: $row->client_telephone ?? null,
            clientAdresse: $row->client_adresse ?? null,
            produitsLibelles: $row->produits_libelles ?? null,
            nbProduits: isset($row->nb_produits) ? (int) $row->nb_produits : null,
            quantiteTotale: isset($row->quantite_totale) ? (int) $row->quantite_totale : null,
            prixUnitaire: isset($row->prix_unitaire) ? (float) $row->prix_unitaire : null,
        );
    }

    public function clientNomComplet(): string
    {
        $nom = trim(($this->clientPrenom ?? '') . ' ' . ($this->clientNom ?? ''));
        return $nom !== '' ? $nom : 'Client #' . $this->clientId;
    }

    public function dateFormatee(): string
    {
        $date = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $this->dateCommande);
        return $date === false ? $this->dateCommande : $date->format('d/m/Y H:i');
    }

    public function montantFormate(): string
    {
        return number_format($this->montantTotal, 0, ',', ' ') . ' FCFA';
    }

    public function libelleStatut(): string
    {
        return self::LIBELLES[$this->statut] ?? $this->statut;
    }

    public function statutSuivant(): ?string
    {
        return self::SUIVANTS[$this->statut] ?? null;
    }

    public function libelleStatutSuivant(): ?string
    {
        $suivant = $this->statutSuivant();
        return $suivant === null ? null : (self::LIBELLES[$suivant] ?? $suivant);
    }

    public function peutChangerStatut(): bool
    {
        return $this->statutSuivant() !== null;
    }

    public function peutAnnuler(): bool
    {
        return in_array($this->statut, [self::STATUT_EN_ATTENTE, self::STATUT_EN_PREPARATION], true);
    }

    public function peutGenererRecu(): bool
    {
        return in_array($this->statut, [
            self::STATUT_EN_PREPARATION,
            self::STATUT_PRETE,
            self::STATUT_RETIREE,
        ], true);
    }
}