<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

final class Rating extends Model
{
    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO ratings
             (application_id, rater_user_id, rated_user_id, rated_company_id, rating_type,
              score, score_learning, score_mentorship, score_environment, comment)
             VALUES
             (:application_id, :rater_user_id, :rated_user_id, :rated_company_id, :rating_type,
              :score, :score_learning, :score_mentorship, :score_environment, :comment)'
        );
        $stmt->execute([
            'application_id' => $data['application_id'],
            'rater_user_id' => $data['rater_user_id'],
            'rated_user_id' => $data['rated_user_id'] ?? null,
            'rated_company_id' => $data['rated_company_id'] ?? null,
            'rating_type' => $data['rating_type'],
            'score' => $data['score'],
            'score_learning' => $data['score_learning'] ?? null,
            'score_mentorship' => $data['score_mentorship'] ?? null,
            'score_environment' => $data['score_environment'] ?? null,
            'comment' => $data['comment'] ?? null,
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function alreadyRated(int $applicationId, int $raterUserId, string $type): bool
    {
        $stmt = $this->db->prepare(
            'SELECT 1 FROM ratings
             WHERE application_id = :application_id
               AND rater_user_id = :rater_user_id
               AND rating_type = :rating_type
             LIMIT 1'
        );
        $stmt->execute([
            'application_id' => $applicationId,
            'rater_user_id' => $raterUserId,
            'rating_type' => $type,
        ]);

        return (bool) $stmt->fetchColumn();
    }

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

