<?php

declare(strict_types=1);

namespace App\Core;

use App\Exceptions\AppException;
use App\Exceptions\AuthException;
use App\Exceptions\NotFoundException;
use App\Middleware\Middleware;

/**
 * Routeur : associe une methode HTTP + un chemin a une methode d'un
 * Controller. Supporte les parametres dynamiques /produits/{id}, les
 * middlewares (auth, guest, role:...) et la verification CSRF.
 */
class Router
{
    private array $routes = [];

    public function __construct(private Container $container) {}

    public function get(string $path, array $action, array $middleware = []): void
    {
        $this->add('GET', $path, $action, $middleware);
    }

    public function post(string $path, array $action, array $middleware = []): void
    {
        $this->add('POST', $path, $action, $middleware);
    }

    private function add(string $method, string $path, array $action, array $middleware): void
    {
        $this->routes[] = [
            'method'     => $method,
            'path'       => $path,
            'action'     => $action,
            'middleware' => $middleware,
        ];
    }

    public function dispatch(string $method, string $uri): void
    {
        $path = $this->normalizePath($uri);

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            $params = $this->match($route['path'], $path);
            if ($params === null) {
                continue;
            }

            try {
                if ($method === 'POST') {
                    $this->verifyCsrf();
                }

                $this->runMiddleware($route['middleware']);

                [$class, $action] = $route['action'];
                $controller = $this->container->make($class);
                $output = $controller->{$action}(...$params);

                if (is_string($output)) {
                    echo $output;
                }
                return;
            } catch (AppException $e) {
                $this->handleException($e);
                return;
            }
        }

        http_response_code(404);
        echo View::render('errors/404', ['title' => 'Page introuvable'], null);
    }

    private function normalizePath(string $uri): string
    {
        $path = parse_url($uri, PHP_URL_PATH);
        $base = View::baseUrl();

        if ($base !== '' && str_starts_with($path, $base)) {
            $path = substr($path, strlen($base));
        }

        $path = '/' . trim($path, '/');
        return $path === '//' ? '/' : $path;
    }

    private function match(string $pattern, string $path): ?array
    {
        $regex = preg_replace('#\{[a-zA-Z_]+\}#', '(\d+)', $pattern);
        $regex = '#^' . $regex . '$#';

        if (!preg_match($regex, $path, $matches)) {
            return null;
        }

        array_shift($matches);
        return array_map('intval', $matches);
    }

    private function runMiddleware(array $middleware): void
    {
        foreach ($middleware as $entry) {
            if (is_string($entry)) {
                if (str_contains($entry, ':')) {
                    [$name, $args] = array_pad(explode(':', $entry, 2), 2, '');
                    Middleware::$name(...explode(',', $args));
                } else {
                    Middleware::$entry();
                }
            }
        }
    }

    private function verifyCsrf(): void
    {
        if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['_token'] ?? '')) {
            http_response_code(419);
            flash('error', 'Jeton de sécurité invalide, veuillez réessayer.');
            View::redirectBack('/');
        }
    }

    private function handleException(AppException $e): void
    {
        if ($e instanceof NotFoundException) {
            http_response_code(404);
            echo View::render('errors/404', ['title' => 'Page introuvable'], null);
            return;
        }

        if ($e instanceof AuthException) {
            flash('error', $e->getMessage());
            View::redirect('/connexion');
        }

        flash('error', $e->getMessage());
        View::redirectBack('/');
    }
}
