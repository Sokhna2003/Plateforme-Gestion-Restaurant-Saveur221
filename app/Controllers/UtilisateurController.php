<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;

class UtilisateurController extends Controller
{
    public function dashboard(): string
    {
        $estAdmin = ($_SESSION['user']['role'] ?? '') === 'ADMIN';
        $estGerant = in_array($_SESSION['user']['role'] ?? '', ['GERANT', 'ADMIN'], true);

        return View::render('dashboard/dashboard', [
            'pageTitle' => 'Dashboard',
            'estAdmin' => $estAdmin,
            'estGerant' => $estGerant,
        ], 'base');
    }
}
