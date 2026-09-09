<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;

class GerantController extends Controller
{
    public function dashboard(): string
    {
        return View::render('gerant/dashboard', [
            'pageTitle' => 'Dashboard Gérant',
        ], 'admin');
    }
}
