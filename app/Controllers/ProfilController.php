<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Exceptions\ValidationException;
use App\Services\ProfilService;

class ProfilController extends Controller
{
    public function __construct(private ProfilService $profilService) {}

    public function modifierForm(): string
    {
        $old = $_SESSION['old'] ?? [];
        $errors = $_SESSION['form_errors'] ?? [];
        unset($_SESSION['old'], $_SESSION['form_errors']);

        $estClient = $this->profilService->estClient();
        $role = $_SESSION['user']['role'] ?? '';
        $retour = $estClient ? '/client' : ($role === 'ADMIN' ? '/admin' : '/gerant');

        return View::render('profil/modifier', [
            'pageTitle' => 'Mon profil',
            'profil' => $this->profilService->profil(),
            'estClient' => $estClient,
            'retour' => $retour,
            'old' => $old,
            'errors' => $errors,
        ], $estClient ? 'client' : 'base');
    }

    public function modifier(): void
    {
        try {
            $this->profilService->modifier($this->input(), $_FILES['photo'] ?? []);

            flash('success', 'Profil mis à jour avec succès.');
            View::redirect('/profil');
        } catch (ValidationException $e) {
            $_SESSION['form_errors'] = $e->getErrors();
            $_SESSION['old'] = $_POST;
            flash('error', $e->getMessage());
            View::redirectBack('/profil');
        }
    }
}