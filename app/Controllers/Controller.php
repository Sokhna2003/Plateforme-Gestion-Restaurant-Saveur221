<?php

declare(strict_types=1);

namespace App\Controllers;

abstract class Controller
{
    /** @return array<string, mixed> */
    protected function input(): array
    {
        return $_POST;
    }

    protected function value(string $key, mixed $default = null): mixed
    {
        return $_POST[$key] ?? $_GET[$key] ?? $default;
    }
}
