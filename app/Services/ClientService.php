<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\ValidationException;
use App\Interfaces\ClientRepositoryInterface;
use App\Models\Client;
use App\Models\Commande;

class ClientService
{
    public function __construct(private ClientRepositoryInterface $clients) {}

    /**
     * @return array{clients: Client[], total: int, page: int, totalPages: int}
     */
    public function lister(?string $terme = null, ?string $avecCommandes = null, int $page = 1, int $perPage = 8): array
    {
        $total = $this->clients->compterGestion($terme, $avecCommandes);

        return [
            'clients' => $this->clients->paginer($terme, $avecCommandes, $page, $perPage),
            'total' => $total,
            'page' => $page,
            'totalPages' => max(1, (int) ceil($total / $perPage)),
        ];
    }

    /** @return array<string, mixed> */
    public function statistiques(): array
    {
        return [
            'totalClients' => $this->clients->count(),
            'avecCommandes' => $this->clients->compterAvecCommandes(),
            'commandes' => $this->clients->compterCommandes(),
            'chiffreAffaires' => $this->clients->chiffreAffaires(),
        ];
    }

    public function trouver(int $id): ?Client
    {
        return $this->clients->findById($id);
    }

    /** @return Commande[] */
    public function commandesPour(int $clientId): array
    {
        return $this->clients->commandesPour($clientId);
    }

    public function supprimer(int $id): void
    {
        if ($this->clients->aDesCommandesOuAvis($id)) {
            throw new ValidationException(
                'Impossible de supprimer ce client : il possède des commandes ou des avis.'
            );
        }
        $this->clients->supprimer($id);
    }
}