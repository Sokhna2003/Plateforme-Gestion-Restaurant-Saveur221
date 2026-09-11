<?php

declare(strict_types=1);

namespace App\Models;

use DateTimeImmutable;
use stdClass;

class Client
{
    public function __construct(
        public readonly int $id,
        public readonly string $nom,
        public readonly string $prenom,
        public readonly string $email,
        public readonly ?string $telephone,
        public readonly ?string $adresse,
        public readonly string $motDePasse,
        public readonly string $dateInscription,
        public readonly ?int $nbCommandes = null,
        public readonly ?float $totalDepense = null,
        public readonly ?string $photo = null,
    ) {}

    public static function fromRow(stdClass $row): self
    {
        return new self(
            id: (int) $row->id,
            nom: $row->nom,
            prenom: $row->prenom,
            email: $row->email,
            telephone: $row->telephone ?? null,
            adresse: $row->adresse ?? null,
            motDePasse: $row->mot_de_passe,
            dateInscription: $row->date_inscription,
            nbCommandes: isset($row->nb_commandes) ? (int) $row->nb_commandes : null,
            totalDepense: isset($row->total_depense) ? (float) $row->total_depense : null,
            photo: $row->photo ?? null,
        );
    }

    public function nomComplet(): string
    {
        return trim($this->prenom . ' ' . $this->nom);
    }

    public function initiales(): string
    {
        $prenom = mb_strtoupper(mb_substr(trim($this->prenom), 0, 1));
        $nom = mb_strtoupper(mb_substr(trim($this->nom), 0, 1));
        return $prenom . $nom;
    }

    public function dateInscriptionFormatee(): string
    {
        $date = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $this->dateInscription);
        return $date === false ? $this->dateInscription : $date->format('d/m/Y');
    }

    public function totalDepenseFormate(): string
    {
        return number_format($this->totalDepense ?? 0, 0, ',', ' ') . ' FCFA';
    }

    /**
     * Retourne les donnees de session (sans le mot de passe).
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'nom' => $this->nom,
            'prenom' => $this->prenom,
            'email' => $this->email,
            'telephone' => $this->telephone,
            'adresse' => $this->adresse,
            'photo' => $this->photo,
        ];
    }
}
