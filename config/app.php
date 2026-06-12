<?php

declare(strict_types=1);

function app_env_value(string $key, mixed $default = null): mixed
{
    $value = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);

    if ($value === false || $value === null || $value === '') {
        return $default;
    }

    return $value;
}

return [
    'name' => 'EstágioMatch',
    'env' => app_env_value('APP_ENV', 'development'),
    'debug' => filter_var(app_env_value('APP_DEBUG', false), FILTER_VALIDATE_BOOLEAN),
    'base_url' => rtrim((string) app_env_value('APP_URL', ''), '/'),
    'session_name' => 'ESTAGIOMATCHSESSID',
    'security' => [
        'csrf_key' => '_csrf_token',
        'login_max_attempts' => 5,
        'login_window_minutes' => 15,
        'lockout_minutes' => 20,
        'min_password_length' => 10,
    ],
];
