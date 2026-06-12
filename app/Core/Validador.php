<?php

declare(strict_types=1);

namespace App\Core;

use RuntimeException;

final class Validador
{
    public static function required(array $data, array $fields, string $message): void
    {
        foreach ($fields as $field) {
            if (trim((string) ($data[$field] ?? '')) === '') {
                throw new RuntimeException($message);
            }
        }
    }

    public static function oneOf(string $value, array $allowed, string $message): void
    {
        if (!in_array($value, $allowed, true)) {
            throw new RuntimeException($message);
        }
    }
}
