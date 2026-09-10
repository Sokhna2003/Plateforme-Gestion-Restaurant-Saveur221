<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;

class StatistiqueController extends Controller
{
    private const PERIODES = ['jour', 'semaine', 'mois'];

    public function index(): string
    {
        $periode = isset($_GET['periode']) && in_array((string) $_GET['periode'], self::PERIODES, true)
            ? (string) $_GET['periode']
            : 'jour';

        return View::render('statistiques/index', [
            'periode' => $periode,
            'pageTitle' => 'Statistiques',
            'baseRoute' => $this->baseRoute(),
        ], 'base');
    }

    private function baseRoute(): string
    {
        return ($_SESSION['user']['role'] ?? '') === 'ADMIN' ? '/admin' : '/gerant';
    }
}