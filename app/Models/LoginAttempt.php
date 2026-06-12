<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

final class LoginAttempt extends Model
{
    public function isLocked(string $email, string $ip): bool
    {
        $max = (int) app_config('security.login_max_attempts', 5);
        $window = (int) app_config('security.login_window_minutes', 15);

        $stmt = $this->db->prepare(
            'SELECT COUNT(*) FROM login_attempts
             WHERE email = :email
               AND ip_address = INET6_ATON(:ip)
               AND successful = 0
               AND attempted_at >= DATE_SUB(NOW(), INTERVAL ' . $window . ' MINUTE)'
        );
        $stmt->bindValue('email', mb_strtolower(trim($email)));
        $stmt->bindValue('ip', $ip);
        $stmt->execute();

        return (int) $stmt->fetchColumn() >= $max;
    }

    public function record(string $email, string $ip, bool $successful, ?int $userId = null): void
    {
        $stmt = $this->db->prepare(
            'INSERT INTO login_attempts (email, ip_address, user_id, successful)
             VALUES (:email, INET6_ATON(:ip), :user_id, :successful)'
        );
        $stmt->execute([
            'email' => mb_strtolower(trim($email)),
            'ip' => $ip,
            'user_id' => $userId,
            'successful' => $successful ? 1 : 0,
        ]);
    }

    public function isRateLimited(string $key, string $ip, int $maxAttempts, int $windowMinutes): bool
    {
        $stmt = $this->db->prepare(
            'SELECT COUNT(*) FROM login_attempts
             WHERE email = :email
               AND ip_address = INET6_ATON(:ip)
               AND attempted_at >= DATE_SUB(NOW(), INTERVAL ' . max(1, $windowMinutes) . ' MINUTE)'
        );
        $stmt->execute(['email' => mb_strtolower(trim($key)), 'ip' => $ip]);

        return (int) $stmt->fetchColumn() >= $maxAttempts;
    }
}
