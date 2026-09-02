<?php

namespace App\Core;

/**
 * Routeur minimaliste (pas de dependance externe) : associe une methode
 * HTTP + un chemin a une methode d'un Controller. Supporte des parametres
 * dynamiques du style /produits/{id}.
 */
class Router
{
    private array $routes = [
        'GET' => [],
        'POST' => [],
    ];

    public function get(string $path, string $controllerAction): void
    {
        $this->routes['GET'][$path] = $controllerAction;
    }

    public function post(string $path, string $controllerAction): void
    {
        $this->routes['POST'][$path] = $controllerAction;
    }

    public function dispatch(string $uri, string $method): void
    {
        $method = strtoupper($method);
        $path = $this->normalizePath($uri);

        foreach ($this->routes[$method] ?? [] as $routePath => $controllerAction) {
            $params = $this->match($routePath, $path);
            if ($params !== null) {
                $this->callAction($controllerAction, $params);
                return;
            }
        }

        http_response_code(404);
        echo '404 - Page introuvable';
    }

    /**
     * Retire le base_path configure et le slash final, pour comparer
     * proprement le chemin demande aux routes declarees.
     */
    private function normalizePath(string $uri): string
    {
        $config = require __DIR__ . '/../../config/config.php';
        $basePath = rtrim($config['app']['base_path'] ?? '', '/');

        $path = parse_url($uri, PHP_URL_PATH);
        if ($basePath !== '' && str_starts_with($path, $basePath)) {
            $path = substr($path, strlen($basePath));
        }

        $path = '/' . trim($path, '/');
        return $path === '//' ? '/' : $path;
    }

    /**
     * Compare une route declaree (ex: /produits/{id}) au chemin demande.
     * Retourne un tableau de parametres si ca matche, sinon null.
     */
    private function match(string $routePath, string $path): ?array
    {
        $routeParts = explode('/', trim($routePath, '/'));
        $pathParts = explode('/', trim($path, '/'));

        if (count($routeParts) !== count($pathParts)) {
            return null;
        }

        $params = [];
        foreach ($routeParts as $i => $part) {
            if (str_starts_with($part, '{') && str_ends_with($part, '}')) {
                $params[] = $pathParts[$i];
            } elseif ($part !== $pathParts[$i]) {
                return null;
            }
        }

        return $params;
    }

    private function callAction(string $controllerAction, array $params): void
    {
        [$controllerClass, $action] = explode('@', $controllerAction);
        $controllerClass = 'App\\Controllers\\' . $controllerClass;

        if (!class_exists($controllerClass)) {
            http_response_code(500);
            echo "Erreur serveur : contrôleur {$controllerClass} introuvable.";
            return;
        }

        $controller = new $controllerClass();

        if (!method_exists($controller, $action)) {
            http_response_code(500);
            echo "Erreur serveur : action {$action} introuvable sur {$controllerClass}.";
            return;
        }

        call_user_func_array([$controller, $action], $params);
    }
}
