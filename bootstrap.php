<?php

declare(strict_types=1);

define('BASE_PATH', __DIR__);

function load_env_file(string $path): void
{
    if (!is_file($path) || !is_readable($path)) {
        return;
    }

    foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [] as $line) {
        $line = trim($line);

        if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
            continue;
        }

        [$key, $value] = array_map('trim', explode('=', $line, 2));

        if ($key === '') {
            continue;
        }

        $value = trim($value, "\"'");

        putenv($key . '=' . $value);
        $_ENV[$key] = $value;
        $_SERVER[$key] = $value;
    }
}

load_env_file(BASE_PATH . '/.env');

$composerAutoload = BASE_PATH . '/vendor/autoload.php';
if (is_file($composerAutoload)) {
    require $composerAutoload;
}

require BASE_PATH . '/app/Core/Autoloader.php';

App\Core\Autoloader::register(BASE_PATH . '/app');

require BASE_PATH . '/app/Core/helpers.php';

if (PHP_SAPI !== 'cli') {
    App\Core\Security::startSession();
}
