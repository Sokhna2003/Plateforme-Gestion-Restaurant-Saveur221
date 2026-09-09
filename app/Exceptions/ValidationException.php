<?php

declare(strict_types=1);

namespace App\Exceptions;

class ValidationException extends AppException
{
    /** @var array<string, string> */
    private array $errors;

    /**
     * @param array<string, string> $errors Erreurs par champ (cle = nom du champ)
     */
    public function __construct(string $message, array $errors = [])
    {
        parent::__construct($message);
        $this->errors = $errors;
    }

    /** @return array<string, string> */
    public function getErrors(): array
    {
        return $this->errors;
    }

    public function hasError(string $field): bool
    {
        return isset($this->errors[$field]);
    }

    public function getError(string $field): string
    {
        return $this->errors[$field] ?? '';
    }
}
