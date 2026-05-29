<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

final class Application extends Model
{
    public function exists(int $vacancyId, int $studentId): bool
    {
        $stmt = $this->db->prepare('SELECT 1 FROM applications WHERE vacancy_id = :vacancy_id AND student_id = :student_id LIMIT 1');
        $stmt->execute(['vacancy_id' => $vacancyId, 'student_id' => $studentId]);

        return (bool) $stmt->fetchColumn();
    }

    public function create(int $vacancyId, int $studentId, string $coverLetter, int $matchScore): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO applications (vacancy_id, student_id, cover_letter, match_score)
             VALUES (:vacancy_id, :student_id, :cover_letter, :match_score)'
        );
        $stmt->execute([
            'vacancy_id' => $vacancyId,
            'student_id' => $studentId,
            'cover_letter' => $coverLetter ?: null,
            'match_score' => $matchScore,
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function forStudent(int $studentId, int $limit = 10): array
    {
        $stmt = $this->db->prepare(
            'SELECT a.*, v.title, v.scholarship, c.trade_name, c.logo_initials
             FROM applications a
             INNER JOIN vacancies v ON v.id = a.vacancy_id
             INNER JOIN companies c ON c.id = v.company_id
             WHERE a.student_id = :student_id
             ORDER BY a.created_at DESC
             LIMIT ' . max(1, $limit)
        );
        $stmt->execute(['student_id' => $studentId]);

        return $stmt->fetchAll();
    }

    public function countByStudent(int $studentId): int
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM applications WHERE student_id = :student_id');
        $stmt->execute(['student_id' => $studentId]);

        return (int) $stmt->fetchColumn();
    }

    public function companyApplications(int $companyId): array
    {
        $stmt = $this->db->prepare(
            'SELECT a.*, v.title, u.name AS student_name, sp.course, sp.city
             FROM applications a
             INNER JOIN vacancies v ON v.id = a.vacancy_id
             INNER JOIN users u ON u.id = a.student_id
             LEFT JOIN student_profiles sp ON sp.user_id = u.id
             WHERE v.company_id = :company_id
             ORDER BY a.created_at DESC
             LIMIT 20'
        );
        $stmt->execute(['company_id' => $companyId]);

        return $stmt->fetchAll();
    }
}

