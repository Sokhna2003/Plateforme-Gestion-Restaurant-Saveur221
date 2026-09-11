<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\View;

class Middleware
{
    /**
     * Verifie qu'un client est connecte (session 'client').
     */
    public static function auth(): void
    {
        if (empty($_SESSION['client'])) {
            View::redirect('/connexion');
        }
    }

    /**
     * Verifie qu'un compte (client OU utilisateur interne) est connecte.
     */
    public static function authentifie(): void
    {
        if (empty($_SESSION['client']) && empty($_SESSION['user'])) {
            View::redirect('/connexion');
        }
    }

    /**
     * Verifie qu'aucun client n'est connecte (pages publiques uniquement).
     */
    public static function guest(): void
    {
        if (!empty($_SESSION['client'])) {
            View::redirect('/client');
        }
    }

    /**
     * Verifie que l'utilisateur interne (admin/gerant) a le bon role (session 'user').
     * Les roles sont separes par des virgules : 'role:GERANT,ADMIN'.
     */
    public static function role(string ...$roles): void
    {
        if (empty($_SESSION['user'])) {
            View::redirect('/connexion');
        }

        $role = $_SESSION['user']['role'] ?? null;
        if (!in_array($role, $roles, true)) {
            http_response_code(403);
            echo View::render('errors/403', ['title' => 'Accès refusé'], null);
            exit;
        }
    }
}
