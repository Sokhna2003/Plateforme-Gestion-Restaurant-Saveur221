<?php

declare(strict_types=1);

namespace App\Models;

use DateTimeImmutable;
use stdClass;

class Avis
{
    public const NOTE_MIN = 1;
    public const NOTE_MAX = 5;

    public function __construct(
        public readonly int $id,
        public readonly int $clientId,
        public readonly int $commandeId,
        public readonly int $note,
        public readonly ?string $commentaire,
        public readonly string $dateAvis,
        public readonly ?string $clientNom = null,
        public readonly ?string $clientPrenom = null,
        public readonly ?string $clientEmail = null,
        public readonly ?string $clientTelephone = null,
        public readonly ?string $clientAdresse = null,
        public readonly ?string $commandeDate = null,
        public readonly ?string $commandeStatut = null,
        public readonly ?float $commandeMontantTotal = null,
    ) {}

    public static function fromRow(stdClass $row): self
    {
        return new self(
            id: (int) $row->id,
            clientId: (int) $row->client_id,
            commandeId: (int) $row->commande_id,
            note: (int) $row->note,
            commentaire: $row->commentaire ?? null,
            dateAvis: $row->date_avis,
            clientNom: $row->client_nom ?? null,
            clientPrenom: $row->client_prenom ?? null,
            clientEmail: $row->client_email ?? null,
            clientTelephone: $row->client_telephone ?? null,
            clientAdresse: $row->client_adresse ?? null,
            commandeDate: $row->commande_date ?? null,
            commandeStatut: $row->commande_statut ?? null,
            commandeMontantTotal: isset($row->commande_montant_total) ? (float) $row->commande_montant_total : null,
        );
    }

    public function clientNomComplet(): string
    {
        $nom = trim(($this->clientPrenom ?? '') . ' ' . ($this->clientNom ?? ''));
        return $nom !== '' ? $nom : 'Client #' . $this->clientId;
    }

    public function initiales(): string
    {
        $prenom = mb_strtoupper(mb_substr(trim($this->clientPrenom ?? ''), 0, 1));
        $nom = mb_strtoupper(mb_substr(trim($this->clientNom ?? ''), 0, 1));
        return $prenom . $nom;
    }

    public function dateAvisFormatee(): string
    {
        $date = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $this->dateAvis);
        return $date === false ? $this->dateAvis : $date->format('d/m/Y H:i');
    }

    public function commandeDateFormatee(): string
    {
        if ($this->commandeDate === null) {
            return '';
        }
        $date = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $this->commandeDate);
        return $date === false ? $this->commandeDate : $date->format('d/m/Y H:i');
    }

    public function commentaireCourt(int $longueur = 70): string
    {
        if ($this->commentaire === null || $this->commentaire === '') {
            return '';
        }
        if (mb_strlen($this->commentaire) <= $longueur) {
            return $this->commentaire;
        }
        return rtrim(mb_substr($this->commentaire, 0, $longueur)) . '…';
    }
}