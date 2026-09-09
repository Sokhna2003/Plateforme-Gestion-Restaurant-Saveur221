<?php

namespace App\Core;

/**
 * Classe mere de tous les Controllers : view() et redirect() en methodes
 * d'instance, disponibles via $this-> dans n'importe quel controller qui
 * l'etend (comme vu en cours).
 */
abstract class Controller
{
    /**
     * Affiche une vue (app/Views/{$view}.php) enveloppee dans un layout
     * (app/Views/layouts/{$layout}.php). Le layout recoit le contenu de
     * la vue via la variable $content.
     */
    protected function view(string $view, array $data = [], string $layout = 'main'): void
    {
        extract($data);

        $cheminVue = __DIR__ . '/../Views/' . str_replace('.', '/', $view) . '.php';
        if (!file_exists($cheminVue)) {
            die("Vue '{$view}' introuvable.");
        }

        ob_start();
        require $cheminVue;
        $content = ob_get_clean();

        $cheminLayout = __DIR__ . '/../Views/layouts/' . $layout . '.php';
        if (!file_exists($cheminLayout)) {
            die("Layout '{$layout}' introuvable.");
        }

        require $cheminLayout;
    }

    protected function redirect(string $url): void
    {
        header('Location: ' . BASE_URL . $url);
        exit;
    }
}
