<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

final class Vacancy extends Model
{
    public function active(array $filters = [], ?int $studentId = null, ?int $limit = null): array
    {
        $where = ['v.status = "active"'];
        $params = [];

        if (!empty($filters['q'])) {
            $where[] = '(v.title LIKE :q OR v.area LIKE :q OR c.trade_name LIKE :q OR v.requirements LIKE :q)';
            $params['q'] = '%' . $filters['q'] . '%';
        }
        if (!empty($filters['area'])) {
            $where[] = 'v.area = :area';
            $params['area'] = $filters['area'];
        }
        if (!empty($filters['modality'])) {
            $where[] = 'v.modality = :modality';
            $params['modality'] = $filters['modality'];
        }

        $sql = 'SELECT v.*, c.trade_name, c.logo_initials, c.rating_avg, c.sector
                FROM vacancies v
                INNER JOIN companies c ON c.id = v.company_id
                WHERE ' . implode(' AND ', $where) . '
                ORDER BY v.is_boosted DESC, v.published_at DESC, v.id DESC';

        if ($limit !== null) {
            $sql .= ' LIMIT ' . max(1, $limit);
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $vacancies = $stmt->fetchAll();

        return $this->attachComputedData($vacancies, $studentId);
    }

    public function find(int $id, ?int $studentId = null): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT v.*, c.trade_name, c.logo_initials, c.rating_avg, c.sector, c.description AS company_description
             FROM vacancies v
             INNER JOIN companies c ON c.id = v.company_id
             WHERE v.id = :id
             LIMIT 1'
        );
        $stmt->execute(['id' => $id]);
        $vacancy = $stmt->fetch();

        if (!$vacancy) {
            return null;
        }

        return $this->attachComputedData([$vacancy], $studentId)[0];
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO vacancies
             (company_id, title, area, description, responsibilities, requirements, modality, city, state, address,
              period, duration_months, start_date_label, scholarship, workload, transport_included, status, is_boosted, published_at, expires_at)
             VALUES
             (:company_id, :title, :area, :description, :responsibilities, :requirements, :modality, :city, :state, :address,
              :period, :duration_months, :start_date_label, :scholarship, :workload, :transport_included, "active", 0, NOW(), DATE_ADD(NOW(), INTERVAL 30 DAY))'
        );
        $stmt->execute([
            'company_id' => $data['company_id'],
            'title' => $data['title'],
            'area' => $data['area'],
            'description' => $data['description'],
            'responsibilities' => $data['responsibilities'] ?? null,
            'requirements' => $data['requirements'] ?? null,
            'modality' => $data['modality'],
            'city' => $data['city'] ?? null,
            'state' => $data['state'] ?? null,
            'address' => $data['address'] ?? null,
            'period' => $data['period'] ?? null,
            'duration_months' => $data['duration_months'] ?: null,
            'start_date_label' => $data['start_date_label'] ?? 'Imediato',
            'scholarship' => (float) $data['scholarship'],
            'workload' => $data['workload'] ?? null,
            'transport_included' => !empty($data['transport_included']) ? 1 : 0,
        ]);

        $vacancyId = (int) $this->db->lastInsertId();
        $this->syncSkills($vacancyId, (string) ($data['skills'] ?? ''));

        return $vacancyId;
    }

    public function syncSkills(int $vacancyId, string $skillsCsv): void
    {
        $skills = array_filter(array_map(static fn (string $skill): string => trim($skill), explode(',', $skillsCsv)));
        $skills = array_values(array_unique(array_slice($skills, 0, 20)));

        $this->db->prepare('DELETE FROM vacancy_skills WHERE vacancy_id = :vacancy_id')->execute(['vacancy_id' => $vacancyId]);

        $stmt = $this->db->prepare('INSERT INTO vacancy_skills (vacancy_id, skill, is_required) VALUES (:vacancy_id, :skill, 1)');
        foreach ($skills as $skill) {
            $stmt->execute(['vacancy_id' => $vacancyId, 'skill' => mb_substr($skill, 0, 80)]);
        }
    }

    public function skills(int $vacancyId): array
    {
        $stmt = $this->db->prepare('SELECT skill, is_required FROM vacancy_skills WHERE vacancy_id = :vacancy_id ORDER BY skill');
        $stmt->execute(['vacancy_id' => $vacancyId]);

        return $stmt->fetchAll();
    }

    public function companyStats(int $companyId): array
    {
        $stmt = $this->db->prepare(
            'SELECT
                COUNT(*) AS vagas,
                SUM(status = "active") AS ativas
             FROM vacancies
             WHERE company_id = :company_id'
        );
        $stmt->execute(['company_id' => $companyId]);

        return $stmt->fetch() ?: ['vagas' => 0, 'ativas' => 0];
    }

    private function attachComputedData(array $vacancies, ?int $studentId): array
    {
        $profile = null;
        $studentSkills = [];

        if ($studentId !== null) {
            $profileModel = new StudentProfile();
            $profile = $profileModel->findByUserId($studentId);
            $studentSkills = array_map(
                static fn (array $skill): string => mb_strtolower($skill['skill']),
                $profileModel->skills($studentId)
            );
        }

        foreach ($vacancies as &$vacancy) {
            $vacancy['skills'] = $this->skills((int) $vacancy['id']);
            $vacancy['match_score'] = $profile ? $this->matchScore($vacancy, $profile, $studentSkills) : 70;
            $vacancy['match_reasons'] = $this->matchReasons($vacancy, $profile, $studentSkills);
        }

        usort($vacancies, static fn (array $a, array $b): int => ($b['match_score'] <=> $a['match_score']) ?: ($b['is_boosted'] <=> $a['is_boosted']));

        return $vacancies;
    }

    private function matchScore(array $vacancy, array $profile, array $studentSkills): int
    {
        $score = 35;
        $vacancySkills = array_map(static fn (array $skill): string => mb_strtolower($skill['skill']), $vacancy['skills'] ?? []);
        $matches = array_intersect($vacancySkills, $studentSkills);

        if ($vacancySkills !== []) {
            $score += (int) round((count($matches) / count($vacancySkills)) * 35);
        }

        if (!empty($profile['course']) && str_contains(mb_strtolower((string) $vacancy['area']), $this->courseAreaHint((string) $profile['course']))) {
            $score += 12;
        }
        if (($profile['preferred_modality'] ?? 'qualquer') === 'qualquer' || ($profile['preferred_modality'] ?? '') === $vacancy['modality']) {
            $score += 8;
        }
        if (!empty($profile['city']) && !empty($vacancy['city']) && mb_strtolower((string) $profile['city']) === mb_strtolower((string) $vacancy['city'])) {
            $score += 7;
        }
        if ((float) $vacancy['scholarship'] >= (float) ($profile['min_scholarship'] ?? 0)) {
            $score += 3;
        }

        return max(35, min(99, $score));
    }

    private function matchReasons(array $vacancy, ?array $profile, array $studentSkills): array
    {
        $reasons = [];
        $vacancySkills = array_map(static fn (array $skill): string => $skill['skill'], $vacancy['skills'] ?? []);
        $matches = array_filter($vacancySkills, static fn (string $skill): bool => in_array(mb_strtolower($skill), $studentSkills, true));

        if ($matches !== []) {
            $reasons[] = 'Habilidades em comum: ' . implode(', ', array_slice($matches, 0, 3));
        }
        if ($profile && !empty($profile['city']) && mb_strtolower((string) $profile['city']) === mb_strtolower((string) $vacancy['city'])) {
            $reasons[] = 'Vaga próxima à sua cidade';
        }
        if ($profile && (($profile['preferred_modality'] ?? 'qualquer') === $vacancy['modality'])) {
            $reasons[] = 'Modalidade alinhada à sua preferência';
        }

        return $reasons ?: ['Perfil acadêmico compatível com a área'];
    }

    private function courseAreaHint(string $course): string
    {
        $course = mb_strtolower($course);

        return match (true) {
            str_contains($course, 'comput') || str_contains($course, 'software') || str_contains($course, 'sistemas') => 'tecnologia',
            str_contains($course, 'admin') => 'admin',
            str_contains($course, 'design') => 'design',
            default => '',
        };
    }
}

