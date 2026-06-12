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

function app_base_path(): string
{
    static $basePath = null;

    if ($basePath !== null) {
        return $basePath;
    }

    $base = trim((string) app_config('base_url', ''));

    if ($base === '') {
        $basePath = '';
        return $basePath;
    }

    $path = '';

    if (str_starts_with($base, 'http://') || str_starts_with($base, 'https://')) {
        $parsed = parse_url($base);
        $path = is_array($parsed) ? (string) ($parsed['path'] ?? '') : '';
    } else {
        $path = $base;
    }

    $path = '/' . trim($path, '/');
    $basePath = $path === '/' ? '' : $path;

    return $basePath;
}

function base_url(string $path = ''): string
{
    $base = app_base_path();
    $path = ltrim($path, '/');

    if ($path === '') {
        return $base !== '' ? $base : '/';
    }

    return ($base !== '' ? $base : '') . '/' . $path;
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
    $relative = 'public/assets/' . ltrim($path, '/');
    $url = base_url($relative);

    // Cache-busting: a URL muda quando o arquivo muda, evitando CSS/JS antigo
    // servido pelo navegador ou pelo service worker.
    $full = BASE_PATH . '/' . $relative;
    if (is_file($full)) {
        $url .= '?v=' . filemtime($full);
    }

    return $url;
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

    if ($referer === '') {
        return $fallback;
    }

    $refererHost = parse_url($referer, PHP_URL_HOST);
    $currentHost = $_SERVER['HTTP_HOST'] ?? '';

    if ($refererHost !== null && $currentHost !== '' && strcasecmp((string) $refererHost, (string) $currentHost) === 0) {
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

function vacancy_status_label(?string $value): string
{
    return match ($value) {
        'draft' => 'Rascunho',
        'active' => 'Ativa',
        'paused' => 'Pausada',
        'expired' => 'Expirada',
        'closed' => 'Encerrada',
        default => 'Ativa',
    };
}

/**
 * Lista oficial das 27 unidades federativas (presentation helper for <select>).
 */
function uf_list(): array
{
    return [
        'AC' => 'Acre', 'AL' => 'Alagoas', 'AP' => 'Amapá', 'AM' => 'Amazonas',
        'BA' => 'Bahia', 'CE' => 'Ceará', 'DF' => 'Distrito Federal', 'ES' => 'Espírito Santo',
        'GO' => 'Goiás', 'MA' => 'Maranhão', 'MT' => 'Mato Grosso', 'MS' => 'Mato Grosso do Sul',
        'MG' => 'Minas Gerais', 'PA' => 'Pará', 'PB' => 'Paraíba', 'PR' => 'Paraná',
        'PE' => 'Pernambuco', 'PI' => 'Piauí', 'RJ' => 'Rio de Janeiro', 'RN' => 'Rio Grande do Norte',
        'RS' => 'Rio Grande do Sul', 'RO' => 'Rondônia', 'RR' => 'Roraima', 'SC' => 'Santa Catarina',
        'SP' => 'São Paulo', 'SE' => 'Sergipe', 'TO' => 'Tocantins',
    ];
}

function uf_options(?string $selected): string
{
    $html = '';
    foreach (uf_list() as $uf => $name) {
        $html .= '<option value="' . e($uf) . '" ' . selected($selected, $uf) . '>'
            . e($uf . ' — ' . $name) . '</option>';
    }

    return $html;
}

/**
 * Inline SVG icon set (stroke-based, currentColor). Presentation helper.
 */
function icon(string $name, string $class = ''): string
{
    $paths = [
        'sparkles' => '<path d="M12 3v4M12 17v4M3 12h4M17 12h4M6.3 6.3l2.4 2.4M15.3 15.3l2.4 2.4M17.7 6.3l-2.4 2.4M8.7 15.3l-2.4 2.4"/>',
        'bolt' => '<path d="M13 2L4 14h7l-1 8 9-12h-7l1-8z"/>',
        'shield' => '<path d="M12 3l8 3v6c0 5-3.5 8-8 9-4.5-1-8-4-8-9V6l8-3z"/><path d="M9 12l2 2 4-4"/>',
        'map-pin' => '<path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0z"/><circle cx="12" cy="10" r="3"/>',
        'star' => '<path d="M12 3l2.9 5.9 6.5.9-4.7 4.6 1.1 6.5L12 17.8 6.2 21l1.1-6.5L2.6 9.8l6.5-.9L12 3z"/>',
        'users' => '<circle cx="9" cy="8" r="3.5"/><path d="M2.5 20a6.5 6.5 0 0 1 13 0"/><path d="M16 5.5a3.5 3.5 0 0 1 0 6.8M22 20a6.5 6.5 0 0 0-5-6.3"/>',
        'check' => '<path d="M20 6L9 17l-5-5"/>',
        'check-circle' => '<circle cx="12" cy="12" r="9"/><path d="M8.5 12.5l2.5 2.5 4.5-5"/>',
        'search' => '<circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/>',
        'briefcase' => '<rect x="3" y="7" width="18" height="13" rx="2"/><path d="M8 7V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M3 13h18"/>',
        'chart' => '<path d="M4 20V10M10 20V4M16 20v-7M22 20H2"/>',
        'accessibility' => '<circle cx="12" cy="4" r="1.6"/><path d="M5 8h14M12 8v6m0 0l-3 6m3-6l3 6"/>',
        'arrow-right' => '<path d="M5 12h14M13 6l6 6-6 6"/>',
        'chevron-left' => '<path d="M15 6l-6 6 6 6"/>',
        'chevron-right' => '<path d="M9 6l6 6-6 6"/>',
        'rocket' => '<path d="M5 15c-1.5 1.5-2 5-2 5s3.5-.5 5-2c.8-.8.8-2 0-3s-2.2-.8-3 0z"/><path d="M9 13l-2-2c1-5 5-9 11-9 0 6-4 10-9 11z"/><circle cx="15" cy="9" r="1.4"/>',
        'heart' => '<path d="M12 21s-7-4.5-9.5-9C1 9 2.5 5.5 6 5.5c2 0 3 1 4 2.5 1-1.5 2-2.5 4-2.5 3.5 0 5 3.5 3.5 6.5C19 16.5 12 21 12 21z"/>',
        'clock' => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',
        'lock' => '<rect x="4" y="10" width="16" height="11" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/>',
        'building' => '<rect x="4" y="3" width="16" height="18" rx="1.5"/><path d="M9 7h2M13 7h2M9 11h2M13 11h2M9 15h2M13 15h2M10 21v-3h4v3"/>',
        'graduation' => '<path d="M22 9L12 5 2 9l10 4 10-4z"/><path d="M6 11v5c0 1.5 2.7 3 6 3s6-1.5 6-3v-5"/>',
        'bell' => '<path d="M18 8a6 6 0 0 0-12 0c0 7-3 8-3 8h18s-3-1-3-8"/><path d="M13.7 21a2 2 0 0 1-3.4 0"/>',
        'hands' => '<path d="M7 11V6.5a1.5 1.5 0 0 1 3 0V10m0 0V5.5a1.5 1.5 0 0 1 3 0V10m0-1.5a1.5 1.5 0 0 1 3 0V14c0 3-2.5 6-6 6s-5-2-6-4l-2.2-3.3a1.5 1.5 0 0 1 2.4-1.7L7 12"/>',
    ];

    $body = $paths[$name] ?? '';
    $cls = $class !== '' ? ' class="' . e($class) . '"' : '';

    return '<svg' . $cls . ' viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" '
        . 'stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $body . '</svg>';
}
