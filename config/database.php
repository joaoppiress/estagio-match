<?php

declare(strict_types=1);

function env_value(string $key, mixed $default = null): mixed
{
    return $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key) ?: $default;
}

return [
    'driver' => 'mysql',
    'host' => env_value('DB_HOST', '127.0.0.1'),
    'port' => env_value('DB_PORT', '3306'),
    'database' => env_value('DB_DATABASE', 'estagiomatch'),
    'username' => env_value('DB_USERNAME', 'root'),
    'password' => env_value('DB_PASSWORD', ''),
    'charset' => 'utf8mb4',
];
