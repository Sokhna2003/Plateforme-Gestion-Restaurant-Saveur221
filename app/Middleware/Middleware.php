<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\View;

class Middleware
{
    public static function auth(): void
    {
        if (empty($_SESSION['user'])) {
            View::redirect('/connexion');
        }
    }

    public static function guest(): void
    {
        if (!empty($_SESSION['user'])) {
            View::redirect('/');
        }
    }

    public static function role(string ...$roles): void
    {
        self::auth();

        $role = $_SESSION['user']['role'] ?? null;
        if (!in_array($role, $roles, true)) {
            http_response_code(403);
            echo View::render('errors/403', ['title' => 'Accès refusé'], null);
            exit;
        }
    }
}
