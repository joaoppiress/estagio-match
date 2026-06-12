<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

final class Company extends Model
{
    public function createForUser(int $userId, array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO companies (user_id, trade_name, sector, city, state, description, logo_initials, cnpj_hash)
             VALUES (:user_id, :trade_name, :sector, :city, :state, :description, :logo_initials, :cnpj_hash)'
        );
        $stmt->execute([
            'user_id' => $userId,
            'trade_name' => $data['trade_name'],
            'sector' => $data['sector'] ?? null,
            'city' => $data['city'] ?? null,
            'state' => $data['state'] ?? null,
            'description' => $data['description'] ?? null,
            'logo_initials' => $this->initials((string) $data['trade_name']),
            'cnpj_hash' => $data['cnpj_hash'] ?? null,
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function setPremium(int $companyId, bool $premium): void
    {
        $stmt = $this->db->prepare('UPDATE companies SET is_premium = :premium WHERE id = :id');
        $stmt->execute(['premium' => $premium ? 1 : 0, 'id' => $companyId]);
    }

    public function refreshRatingAverage(int $companyId): void
    {
        $stmt = $this->db->prepare(
            'UPDATE companies c
             SET rating_avg = (
                SELECT COALESCE(ROUND(AVG(r.score), 1), 0)
                FROM ratings r
                WHERE r.rated_company_id = c.id
                  AND r.rating_type = "estudante_para_empresa"
             )
             WHERE c.id = :id'
        );
        $stmt->execute(['id' => $companyId]);
    }

    public function findByUserId(int $userId): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM companies WHERE user_id = :user_id LIMIT 1');
        $stmt->execute(['user_id' => $userId]);

        return $stmt->fetch() ?: null;
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM companies WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);

        return $stmt->fetch() ?: null;
    }

    private function initials(string $name): string
    {
        $parts = preg_split('/\s+/', trim($name)) ?: [];
        $letters = array_map(static fn (string $part): string => mb_substr($part, 0, 1), array_slice($parts, 0, 2));

        return mb_strtoupper(implode('', $letters) ?: 'EM');
    }
}

