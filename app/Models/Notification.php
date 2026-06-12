<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

final class Notification extends Model
{
    public function create(int $userId, string $type, string $title, string $message): void
    {
        $stmt = $this->db->prepare(
            'INSERT INTO notificacoes (usuario_id, tipo, titulo, mensagem)
             VALUES (:usuario_id, :tipo, :titulo, :mensagem)'
        );
        $stmt->execute([
            'usuario_id' => $userId,
            'tipo' => mb_substr($type, 0, 60),
            'titulo' => mb_substr($title, 0, 160),
            'mensagem' => mb_substr($message, 0, 500),
        ]);
    }

    public function unreadForUser(int $userId, int $limit = 8): array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM notificacoes
             WHERE usuario_id = :usuario_id AND lida_em IS NULL
             ORDER BY criado_em DESC
             LIMIT ' . max(1, $limit)
        );
        $stmt->execute(['usuario_id' => $userId]);

        return $stmt->fetchAll();
    }

    public function unreadCount(int $userId): int
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM notificacoes WHERE usuario_id = :usuario_id AND lida_em IS NULL');
        $stmt->execute(['usuario_id' => $userId]);

        return (int) $stmt->fetchColumn();
    }

    public function markRead(int $id, int $userId): void
    {
        $stmt = $this->db->prepare(
            'UPDATE notificacoes SET lida_em = NOW()
             WHERE id = :id AND usuario_id = :usuario_id'
        );
        $stmt->execute(['id' => $id, 'usuario_id' => $userId]);
    }
}
