<?php

declare(strict_types=1);

if (!function_exists('dd')) {
    /**
     * Debug rapide : affiche une variable proprement puis arrete l'execution.
     * A ne jamais laisser dans du code commite en l'etat final.
     */
    function dd(mixed $valeur): void
    {
        echo '<pre style="background:#1f1f1f;color:#fff;padding:1rem;border-radius:8px;">';
        var_dump($valeur);
        echo '</pre>';
        die();
    }
}

if (!function_exists('flash')) {
    /**
     * Message flash a usage unique, stocke en session puis lu/efface
     * dans le layout via flash_messages().
     *
     * @param string $type success | error
     */
    function flash(string $type, string $message): void
    {
        $_SESSION['flash'] = ['type' => $type, 'message' => $message];
    }
}

if (!function_exists('flash_messages')) {
    /**
     * Retourne et efface les messages flash en attente.
     *
     * @return array{type: string, message: string}|null
     */
    function flash_messages(): ?array
    {
        if (empty($_SESSION['flash'])) {
            return null;
        }

        $message = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $message;
    }
}

if (!function_exists('csrf')) {
    /**
     * Retourne le jeton CSRF de session, en genere un au besoin.
     */
    function csrf(): string
    {
        if (empty($_SESSION['csrf'])) {
            $_SESSION['csrf'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf'];
    }
}

if (!function_exists('e')) {
    /**
     * Echappe une chaine pour un affichage HTML sur (XSS).
     */
    function e(?string $valeur): string
    {
        return htmlspecialchars((string) $valeur, ENT_QUOTES, 'UTF-8');
    }
}
