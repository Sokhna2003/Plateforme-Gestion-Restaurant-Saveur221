<?php

/**
 * Debug rapide : affiche une variable proprement puis arrete l'execution.
 * A ne jamais laisser dans du code commite en l'etat final.
 * Fonction globale (chargee via composer.json cle "files") pour pouvoir
 * l'appeler n'importe ou sans import, comme dd() dans les frameworks courants.
 */
if (!function_exists('dd')) {
    function dd(mixed $valeur): void
    {
        echo '<pre style="background:#1f1f1f;color:#fff;padding:1rem;border-radius:8px;">';
        var_dump($valeur);
        echo '</pre>';
        die();
    }
}
