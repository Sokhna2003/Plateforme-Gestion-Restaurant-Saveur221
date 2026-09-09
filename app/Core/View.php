<?php

declare(strict_types=1);

namespace App\Core;

class View
{
    public static function render(string $template, array $data = [], ?string $layout = 'main'): string
    {
        $data['base'] = self::baseUrl();
        extract($data, EXTR_SKIP);

        ob_start();
        include __DIR__ . '/../Views/' . $template . '.php';
        $content = (string) ob_get_clean();

        if ($layout === null) {
            return $content;
        }

        $data['content'] = $content;
        extract($data, EXTR_SKIP);

        ob_start();
        include __DIR__ . '/../Views/layouts/' . $layout . '.php';
        return (string) ob_get_clean();
    }

    public static function baseUrl(): string
    {
        $base = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/'));
        return $base === '/' ? '' : rtrim($base, '/');
    }

    public static function redirect(string $path): never
    {
        header('Location: ' . self::baseUrl() . $path);
        exit;
    }

    public static function redirectBack(string $fallback = '/'): never
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? '';
        if ($referer !== '') {
            header('Location: ' . $referer);
            exit;
        }
        header('Location: ' . self::baseUrl() . $fallback);
        exit;
    }
}
