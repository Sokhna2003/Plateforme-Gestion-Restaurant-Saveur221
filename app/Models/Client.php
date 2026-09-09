<?php

declare(strict_types=1);

namespace App\Models;

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
        );
    }

    public function nomComplet(): string
    {
        return trim($this->prenom . ' ' . $this->nom);
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
        ];
    }
}
