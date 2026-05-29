<?php

declare(strict_types=1);

use App\Core\Security;

function app_config(?string $key = null, mixed $default = null): mixed
{
    static $config = null;

    if ($config === null) {
        $config = require BASE_PATH . '/config/app.php';
    }

    if ($key === null) {
        return $config;
    }

    $value = $config;
    foreach (explode('.', $key) as $segment) {
        if (!is_array($value) || !array_key_exists($segment, $value)) {
            return $default;
        }
        $value = $value[$segment];
    }

    return $value;
}

function base_url(string $path = ''): string
{
    $base = (string) app_config('base_url', '');
    $path = ltrim($path, '/');

    return $path === '' ? $base : $base . '/' . $path;
}

function route_url(string $route, array $params = []): string
{
    $query = array_merge(['rota' => $route], $params);

    return base_url('controlador.php?' . http_build_query($query));
}

function action_url(): string
{
    return base_url('processamento.php');
}

function asset_url(string $path): string
{
    return base_url('public/assets/' . ltrim($path, '/'));
}

function redirect(string $url): never
{
    header('Location: ' . $url);
    exit;
}

function back_url(): string
{
    $fallback = route_url('home');
    $referer = (string) ($_SERVER['HTTP_REFERER'] ?? '');
    $base = (string) app_config('base_url', '');

    if ($referer !== '' && str_contains($referer, $base)) {
        return $referer;
    }

    return $fallback;
}

function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function csrf_field(): string
{
    return '<input type="hidden" name="_csrf" value="' . e(Security::csrfToken()) . '">';
}

function flash(string $key, ?string $message = null): ?string
{
    if ($message !== null) {
        $_SESSION['_flash'][$key] = $message;
        return null;
    }

    $message = $_SESSION['_flash'][$key] ?? null;
    unset($_SESSION['_flash'][$key]);

    return $message;
}

function old(string $key, mixed $default = ''): mixed
{
    $value = $_SESSION['_old'][$key] ?? $default;
    unset($_SESSION['_old'][$key]);

    return $value;
}

function keep_old(array $data): void
{
    $_SESSION['_old'] = $data;
}

function current_route(): string
{
    return trim((string) ($_GET['rota'] ?? 'home'), '/') ?: 'home';
}

function brl(mixed $value): string
{
    return 'R$ ' . number_format((float) $value, 2, ',', '.');
}

function selected(mixed $value, mixed $expected): string
{
    return (string) $value === (string) $expected ? 'selected' : '';
}

function checked(bool $condition): string
{
    return $condition ? 'checked' : '';
}

function modality_label(?string $value): string
{
    return match ($value) {
        'remoto' => 'Remoto',
        'hibrido' => 'Híbrido',
        'presencial' => 'Presencial',
        default => 'Qualquer',
    };
}

function status_label(?string $value): string
{
    return match ($value) {
        'visualizada' => 'Visualizada',
        'em_analise' => 'Em análise',
        'entrevista' => 'Entrevista',
        'aprovada' => 'Aprovada',
        'reprovada' => 'Reprovada',
        'cancelada' => 'Cancelada',
        default => 'Enviada',
    };
}
