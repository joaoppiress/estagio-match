<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

final class Rating extends Model
{
    public function companySummary(int $companyId): array
    {
        $stmt = $this->db->prepare(
            'SELECT
                COALESCE(ROUND(AVG(score), 1), 0) AS avg_score,
                COUNT(*) AS total,
                COALESCE(ROUND(AVG(score_learning), 1), 0) AS learning,
                COALESCE(ROUND(AVG(score_mentorship), 1), 0) AS mentorship,
                COALESCE(ROUND(AVG(score_environment), 1), 0) AS environment
             FROM ratings
             WHERE rated_company_id = :company_id
               AND rating_type = "estudante_para_empresa"'
        );
        $stmt->execute(['company_id' => $companyId]);

        return $stmt->fetch() ?: ['avg_score' => 0, 'total' => 0, 'learning' => 0, 'mentorship' => 0, 'environment' => 0];
    }

    public function studentAverage(int $studentId): float
    {
        $stmt = $this->db->prepare(
            'SELECT COALESCE(ROUND(AVG(score), 1), 0)
             FROM ratings
             WHERE rated_user_id = :student_id
               AND rating_type = "empresa_para_estudante"'
        );
        $stmt->execute(['student_id' => $studentId]);

        return (float) $stmt->fetchColumn();
    }
}

