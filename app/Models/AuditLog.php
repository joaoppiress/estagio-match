<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Auth;
use App\Core\Model;

final class AuditLog extends Model
{
    public function record(string $event, array $context = [], ?int $userId = null): void
    {
        $stmt = $this->db->prepare(
            'INSERT INTO audit_logs (user_id, event, context, ip_address)
             VALUES (:user_id, :event, :context, INET6_ATON(:ip))'
        );
        $stmt->execute([
            'user_id' => $userId ?? Auth::id(),
            'event' => $event,
            'context' => json_encode($context, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
        ]);
    }
}

