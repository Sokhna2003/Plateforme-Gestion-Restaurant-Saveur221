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
        $old = $_SESSION['old'] ?? [];
        $errors = $_SESSION['form_errors'] ?? [];
        unset($_SESSION['old'], $_SESSION['form_errors']);

        return View::render('auth/login', [
            'pageTitle' => 'Connexion',
            'old' => $old,
            'errors' => $errors,
        ], 'auth');
    }

    public function login(): void
    {
        try {
            $email = trim($_POST['email'] ?? '');
            $motDePasse = $_POST['mot_de_passe'] ?? '';

            if ($email === '' || $motDePasse === '') {
                throw new ValidationException('Veuillez remplir tous les champs.', [
                    'email' => $email === '' ? 'L\'email est requis.' : '',
                    'mot_de_passe' => $motDePasse === '' ? 'Le mot de passe est requis.' : '',
                ]);
            }

            $resultat = $this->authService->connecter($email, $motDePasse);
            View::redirect($resultat['redirect']);
        } catch (ValidationException $e) {
            $_SESSION['form_errors'] = $e->getErrors();
            $_SESSION['old'] = $_POST;
            flash('error', $e->getMessage());
            View::redirectBack('/connexion');
        }
    }

    public function registerForm(): string
    {
        $old = $_SESSION['old'] ?? [];
        $errors = $_SESSION['form_errors'] ?? [];
        unset($_SESSION['old'], $_SESSION['form_errors']);

        return View::render('auth/register', [
            'pageTitle' => 'Inscription',
            'old' => $old,
            'errors' => $errors,
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
            flash('success', 'Compte créé avec succès. Veuillez vous connecter.');
            View::redirect('/connexion');
        } catch (ValidationException $e) {
            $_SESSION['form_errors'] = $e->getErrors();
            $_SESSION['old'] = $_POST;
            flash('error', $e->getMessage());
            View::redirectBack('/inscription');
        }
    }

    public function logout(): void
    {
        $this->authService->deconnecter();
        View::redirect('/');
    }
}
