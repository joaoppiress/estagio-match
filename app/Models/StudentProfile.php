<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

final class StudentProfile extends Model
{
    public function createForUser(int $userId, array $data): void
    {
        $stmt = $this->db->prepare(
            'INSERT INTO student_profiles
             (user_id, course, institution, current_period, city, state, latitude, longitude, interests, preferred_modality, profile_completeness)
             VALUES (:user_id, :course, :institution, :current_period, :city, :state, :latitude, :longitude, :interests, :preferred_modality, :profile_completeness)'
        );
        $stmt->execute([
            'user_id' => $userId,
            'course' => $data['course'] ?? null,
            'institution' => $data['institution'] ?? null,
            'current_period' => $data['current_period'] ?? null,
            'city' => $data['city'] ?? null,
            'state' => $data['state'] ?? null,
            'latitude' => $data['latitude'] ?? null,
            'longitude' => $data['longitude'] ?? null,
            'interests' => $data['interests'] ?? null,
            'preferred_modality' => $data['preferred_modality'] ?? 'qualquer',
            'profile_completeness' => $this->calculateCompleteness($data),
        ]);
    }

    public function findByUserId(int $userId): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM student_profiles WHERE user_id = :user_id LIMIT 1');
        $stmt->execute(['user_id' => $userId]);

        return $stmt->fetch() ?: null;
    }

    public function update(int $userId, array $data): void
    {
        $data['profile_completeness'] = $this->calculateCompleteness($data);
        $stmt = $this->db->prepare(
            'UPDATE student_profiles SET
                course = :course,
                institution = :institution,
                current_period = :current_period,
                graduation_forecast = :graduation_forecast,
                performance_index = :performance_index,
                city = :city,
                state = :state,
                neighborhood = :neighborhood,
                cep = :cep,
                latitude = :latitude,
                longitude = :longitude,
                interests = :interests,
                availability = :availability,
                preferred_modality = :preferred_modality,
                max_distance_km = :max_distance_km,
                min_scholarship = :min_scholarship,
                bio = :bio,
                portfolio_url = :portfolio_url,
                accessibility_libras = :accessibility_libras,
                profile_completeness = :profile_completeness
             WHERE user_id = :user_id'
        );
        $stmt->execute([
            'user_id' => $userId,
            'course' => $data['course'] ?? null,
            'institution' => $data['institution'] ?? null,
            'current_period' => $data['current_period'] ?: null,
            'graduation_forecast' => $data['graduation_forecast'] ?? null,
            'performance_index' => $data['performance_index'] ?: null,
            'city' => $data['city'] ?? null,
            'state' => $data['state'] ?? null,
            'neighborhood' => $data['neighborhood'] ?? null,
            'cep' => $data['cep'] ?? null,
            'latitude' => $data['latitude'] ?? null,
            'longitude' => $data['longitude'] ?? null,
            'interests' => $data['interests'] ?? null,
            'availability' => $data['availability'] ?? null,
            'preferred_modality' => $data['preferred_modality'] ?? 'qualquer',
            'max_distance_km' => max(1, (int) ($data['max_distance_km'] ?? 15)),
            'min_scholarship' => max(0, (float) ($data['min_scholarship'] ?? 800)),
            'bio' => $data['bio'] ?? null,
            'portfolio_url' => $data['portfolio_url'] ?? null,
            'accessibility_libras' => !empty($data['accessibility_libras']) ? 1 : 0,
            'profile_completeness' => $data['profile_completeness'],
        ]);
    }

    public function skills(int $userId): array
    {
        $stmt = $this->db->prepare('SELECT * FROM student_skills WHERE user_id = :user_id ORDER BY skill');
        $stmt->execute(['user_id' => $userId]);

        return $stmt->fetchAll();
    }

    public function syncSkills(int $userId, string $skillsCsv): void
    {
        $skills = array_filter(array_map(static fn (string $skill): string => trim($skill), explode(',', $skillsCsv)));
        $skills = array_values(array_unique(array_slice($skills, 0, 20)));

        $this->db->prepare('DELETE FROM student_skills WHERE user_id = :user_id')->execute(['user_id' => $userId]);

        $stmt = $this->db->prepare('INSERT INTO student_skills (user_id, skill, level) VALUES (:user_id, :skill, :level)');
        foreach ($skills as $skill) {
            $stmt->execute([
                'user_id' => $userId,
                'skill' => mb_substr($skill, 0, 80),
                'level' => 'intermediario',
            ]);
        }
    }

    public function calculateCompleteness(array $data): int
    {
        $fields = ['course', 'institution', 'current_period', 'city', 'state', 'interests', 'availability', 'bio', 'portfolio_url'];
        $filled = 0;

        foreach ($fields as $field) {
            if (!empty($data[$field])) {
                $filled++;
            }
        }

        return max(25, min(100, (int) round(($filled / count($fields)) * 100)));
    }

    public function activeStudentIds(): array
    {
        $stmt = $this->db->query(
            'SELECT sp.user_id
             FROM student_profiles sp
             INNER JOIN users u ON u.id = sp.user_id
             WHERE u.status = "active"'
        );

        return array_map('intval', array_column($stmt->fetchAll(), 'user_id'));
    }
}

