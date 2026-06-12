<?php

declare(strict_types=1);

return [
    'name' => 'EstágioMatch',
    'env' => getenv('APP_ENV') ?: 'development',
    'debug' => filter_var(getenv('APP_DEBUG') ?: false, FILTER_VALIDATE_BOOLEAN),
    'base_url' => rtrim((string) (getenv('APP_URL') ?: '/estagio-match'), '/'),
    'session_name' => 'ESTAGIOMATCHSESSID',
    'security' => [
        'csrf_key' => '_csrf_token',
        'login_max_attempts' => 5,
        'login_window_minutes' => 15,
        'lockout_minutes' => 20,
        'min_password_length' => 10,
    ],
];

