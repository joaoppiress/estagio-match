<?php

declare(strict_types=1);

namespace App\Core;

use App\Models\User;

final class Auth
{
    public static function check(): bool
    {
        return isset($_SESSION['user_id']);
    }

    public static function id(): ?int
    {
        return isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null;
    }

    public static function user(): ?array
    {
        if (!self::check()) {
            return null;
        }

        static $user = null;

        if ($user === null || (int) $user['id'] !== self::id()) {
            $user = (new User())->findById((int) self::id());
        }

        return $user;
    }

    public static function login(array $user): void
    {
        session_regenerate_id(true);
        $_SESSION['user_id'] = (int) $user['id'];
        $_SESSION['user_role'] = (string) $user['role'];
        $_SESSION['_fingerprint'] = hash('sha256', (string) ($_SERVER['HTTP_USER_AGENT'] ?? 'cli') . '|EstagioMatch');
        $_SESSION['_last_regenerate'] = time();
    }

    public static function logout(): void
    {
        Security::destroySession();
    }

    public static function requireLogin(): void
    {
        if (!self::check()) {
            flash('error', 'Faça login para acessar esta área.');
            \redirect(\route_url('login'));
        }
    }

    public static function requireRole(array $roles): void
    {
        self::requireLogin();
        $role = (string) ($_SESSION['user_role'] ?? '');

        if (!in_array($role, $roles, true)) {
            http_response_code(403);
            require BASE_PATH . '/app/Views/errors/403.php';
            exit;
        }
    }

    public static function role(): ?string
    {
        return $_SESSION['user_role'] ?? null;
    }

    public static function isStudent(): bool
    {
        return self::role() === 'estudante';
    }

    public static function isCompany(): bool
    {
        return self::role() === 'empresa';
    }
}

