<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;

class ClientController extends Controller
{
    public function dashboard(): string
    {
        return View::render('client/dashboard', [
            'pageTitle' => 'Mon espace',
        ], 'client');
    }
}
