<?php

declare(strict_types=1);

namespace App\Models;

use stdClass;

class Utilisateur
{
    public function __construct(
        public readonly int $id,
        public readonly string $nom,
        public readonly string $prenom,
        public readonly string $email,
        public readonly string $motDePasse,
        public readonly int $roleId,
        public readonly string $role,
        public readonly bool $actif,
        public readonly string $dateCreation,
        public readonly ?string $photo = null,
    ) {}

    public static function fromRow(stdClass $row): self
    {
        return new self(
            id: (int) $row->id,
            nom: $row->nom,
            prenom: $row->prenom,
            email: $row->email,
            motDePasse: $row->mot_de_passe,
            roleId: (int) $row->role_id,
            role: $row->role_libelle ?? 'UNKNOWN',
            actif: (bool) $row->actif,
            dateCreation: $row->date_creation,
            photo: $row->photo ?? null,
        );
    }

    public function nomComplet(): string
    {
        return trim($this->prenom . ' ' . $this->nom);
    }

    public function initiales(): string
    {
        $initiale = static function (string $valeur): string {
            return $valeur === '' ? '' : mb_strtoupper(mb_substr($valeur, 0, 1));
        };

        $initiales = $initiale($this->prenom) . $initiale($this->nom);

        return $initiales !== '' ? $initiales : '?';
    }

    public function dateCreationFormatee(): string
    {
        $temps = strtotime($this->dateCreation);

        return $temps === false ? $this->dateCreation : date('d/m/Y H:i', $temps);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'nom' => $this->nom,
            'prenom' => $this->prenom,
            'email' => $this->email,
            'role' => $this->role,
            'photo' => $this->photo,
        ];
    }
}
