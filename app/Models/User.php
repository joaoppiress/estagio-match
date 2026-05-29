<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use App\Core\Security;
use PDO;

final class User extends Model
{
    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE id = :id AND deleted_at IS NULL LIMIT 1');
        $stmt->execute(['id' => $id]);

        return $stmt->fetch() ?: null;
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE email = :email AND deleted_at IS NULL LIMIT 1');
        $stmt->execute(['email' => mb_strtolower(trim($email))]);

        return $stmt->fetch() ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO users (role, name, email, password_hash, lgpd_accepted_at, email_verified_at)
             VALUES (:role, :name, :email, :password_hash, NOW(), NULL)'
        );
        $stmt->execute([
            'role' => $data['role'],
            'name' => $data['name'],
            'email' => mb_strtolower(trim($data['email'])),
            'password_hash' => Security::passwordHash($data['password']),
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function touchLogin(int $id): void
    {
        $stmt = $this->db->prepare('UPDATE users SET last_login_at = NOW() WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    public function updatePasswordHash(int $id, string $hash): void
    {
        $stmt = $this->db->prepare('UPDATE users SET password_hash = :hash WHERE id = :id');
        $stmt->execute(['hash' => $hash, 'id' => $id]);
    }

    public function initials(array $user): string
    {
        $parts = preg_split('/\s+/', trim((string) $user['name'])) ?: [];
        $letters = array_map(static fn (string $part): string => mb_substr($part, 0, 1), array_slice($parts, 0, 2));

        return mb_strtoupper(implode('', $letters) ?: 'EM');
    }

    public function countByRole(string $role): int
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM users WHERE role = :role AND status = "active"');
        $stmt->execute(['role' => $role]);

        return (int) $stmt->fetchColumn();
    }
}

