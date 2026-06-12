<?php

declare(strict_types=1);

namespace App\Core;

use RuntimeException;

final class Security
{
    public static function startSession(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || (($_SERVER['SERVER_PORT'] ?? null) === '443');

        ini_set('session.use_strict_mode', '1');
        ini_set('session.use_only_cookies', '1');
        ini_set('session.cookie_httponly', '1');
        ini_set('session.cookie_samesite', 'Strict');

        session_name((string) app_config('session_name', 'ESTAGIOMATCHSESSID'));
        session_set_cookie_params([
            'lifetime' => 0,
            'path' => (string) app_config('base_url', '/'),
            'domain' => '',
            'secure' => $isHttps,
            'httponly' => true,
            'samesite' => 'Strict',
        ]);

        session_start();

        $_SESSION['_created_at'] ??= time();
        $_SESSION['_fingerprint'] ??= self::fingerprint();

        if (!hash_equals((string) $_SESSION['_fingerprint'], self::fingerprint())) {
            self::destroySession();
            throw new RuntimeException('Sessão encerrada por segurança. Faça login novamente.');
        }

        if (time() - (int) ($_SESSION['_last_regenerate'] ?? 0) > 600) {
            session_regenerate_id(true);
            $_SESSION['_last_regenerate'] = time();
        }
    }

    public static function sendHeaders(): void
    {
        header('X-Content-Type-Options: nosniff');
        header('Referrer-Policy: strict-origin-when-cross-origin');
        header('Permissions-Policy: camera=(), microphone=(), geolocation=(self)');

        // VLibras (gov.br) runs a Unity WebGL player: it needs 'unsafe-eval' / 'wasm-unsafe-eval',
        // blob: workers and a frame from vlibras.gov.br. 'unsafe-inline' is still required while
        // views bind dynamic widths/--score inline. Everything else stays restricted to 'self'.
        // VLibras' plugin.js 302-redirects to jsDelivr, and the Unity player streams its
        // dictionary/assets from both hosts — so both origins must be allowed.
        $vlibras = 'https://vlibras.gov.br https://www.vlibras.gov.br https://cdn.jsdelivr.net';
        header(
            "Content-Security-Policy: default-src 'self'; "
            . "img-src 'self' data: blob: {$vlibras}; "
            . "style-src 'self' 'unsafe-inline' {$vlibras}; "
            . "script-src 'self' 'unsafe-inline' 'unsafe-eval' blob: {$vlibras}; "
            . "worker-src 'self' blob:; "
            . "connect-src 'self' blob: data: {$vlibras}; "
            . "frame-src 'self' {$vlibras}; "
            . "font-src 'self' data: {$vlibras}; "
            . "form-action 'self'; base-uri 'self'; frame-ancestors 'self'"
        );
    }

    public static function csrfToken(): string
    {
        $key = (string) app_config('security.csrf_key', '_csrf_token');

        if (empty($_SESSION[$key])) {
            $_SESSION[$key] = bin2hex(random_bytes(32));
        }

        return (string) $_SESSION[$key];
    }

    public static function verifyCsrf(string $token): bool
    {
        $key = (string) app_config('security.csrf_key', '_csrf_token');
        $stored = (string) ($_SESSION[$key] ?? '');

        return $stored !== '' && $token !== '' && hash_equals($stored, $token);
    }

    public static function requirePost(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            http_response_code(405);
            throw new RuntimeException('Método não permitido.');
        }
    }

    public static function destroySession(): void
    {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], (bool) $params['secure'], (bool) $params['httponly']);
        }

        session_destroy();
    }

    public static function strongPasswordErrors(string $password): array
    {
        $min = (int) app_config('security.min_password_length', 10);
        $errors = [];

        if (mb_strlen($password) < $min) {
            $errors[] = "A senha deve ter pelo menos {$min} caracteres.";
        }
        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] = 'Inclua ao menos uma letra maiúscula.';
        }
        if (!preg_match('/[a-z]/', $password)) {
            $errors[] = 'Inclua ao menos uma letra minúscula.';
        }
        if (!preg_match('/\d/', $password)) {
            $errors[] = 'Inclua ao menos um número.';
        }
        if (!preg_match('/[^A-Za-z0-9]/', $password)) {
            $errors[] = 'Inclua ao menos um caractere especial.';
        }

        return $errors;
    }

    public static function passwordHash(string $password): string
    {
        if (defined('PASSWORD_ARGON2ID')) {
            return password_hash($password, PASSWORD_ARGON2ID, [
                'memory_cost' => 1 << 16,
                'time_cost' => 4,
                'threads' => 2,
            ]);
        }

        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    }

    private static function fingerprint(): string
    {
        $userAgent = (string) ($_SERVER['HTTP_USER_AGENT'] ?? 'cli');

        return hash('sha256', $userAgent . '|EstagioMatch');
    }
}

