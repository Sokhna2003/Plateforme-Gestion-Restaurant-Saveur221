<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;

class AdminController extends Controller
{
    public function dashboard(): string
    {
        return View::render('admin/dashboard', [
            'pageTitle' => 'Dashboard Admin',
        ], 'admin');
    }
}
