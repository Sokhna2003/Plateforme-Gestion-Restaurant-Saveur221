<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Exceptions\ValidationException;
use App\Services\AuthService;

class AuthController extends Controller
{
    public function __construct(private AuthService $authService) {}

    public function loginForm(): string
    {
        return View::render('auth/login', [
            'pageTitle' => 'Connexion',
        ], 'auth');
    }

    public function login(): void
    {
        try {
            $this->authService->connecter(
                email: trim($_POST['email'] ?? ''),
                motDePasse: $_POST['mot_de_passe'] ?? '',
            );
            View::redirect('/client');
        } catch (ValidationException $e) {
            flash('error', $e->getMessage());
            View::redirectBack('/connexion');
        }
    }

    public function registerForm(): string
    {
        return View::render('auth/register', [
            'pageTitle' => 'Inscription',
        ], 'auth');
    }

    public function register(): void
    {
        try {
            $this->authService->inscrire([
                'nom' => trim($_POST['nom'] ?? ''),
                'prenom' => trim($_POST['prenom'] ?? ''),
                'email' => trim($_POST['email'] ?? ''),
                'telephone' => trim($_POST['telephone'] ?? ''),
                'adresse' => trim($_POST['adresse'] ?? ''),
                'mot_de_passe' => $_POST['mot_de_passe'] ?? '',
                'mot_de_passe_confirmation' => $_POST['mot_de_passe_confirmation'] ?? '',
            ]);
            View::redirect('/client');
        } catch (ValidationException $e) {
            flash('error', $e->getMessage());
            flash('errors', json_encode($e->getErrors()));
            View::redirectBack('/inscription');
        }
    }

    public function logout(): void
    {
        $this->authService->deconnecter();
        View::redirect('/');
    }
}
