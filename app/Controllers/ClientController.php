<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;

class ClientController extends Controller
{
    public function dashboard(): string
    {
        return View::render('dashboard/dashboard', [
            'pageTitle' => 'Mon espace',
            'estAdmin' => false,
            'estGerant' => false,
            'estClient' => true,
        ], 'client');
    }
}
