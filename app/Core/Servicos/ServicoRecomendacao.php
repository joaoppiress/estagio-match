<?php

declare(strict_types=1);

namespace App\Core\Servicos;

final class ServicoRecomendacao
{
    public function score(array $vacancy, array $profile, array $studentSkills): int
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

        $distance = $this->distanceKm($profile, $vacancy);
        if ($distance !== null) {
            if ($distance <= (int) ($profile['max_distance_km'] ?? 15)) {
                $score += 10;
            }
        } elseif (!empty($profile['city']) && !empty($vacancy['city']) && mb_strtolower((string) $profile['city']) === mb_strtolower((string) $vacancy['city'])) {
            $score += 7;
        }

        if ((float) $vacancy['scholarship'] >= (float) ($profile['min_scholarship'] ?? 0)) {
            $score += 3;
        }

        return max(35, min(99, $score));
    }

    public function reasons(array $vacancy, ?array $profile, array $studentSkills): array
    {
        $reasons = [];
        $vacancySkills = array_map(static fn (array $skill): string => $skill['skill'], $vacancy['skills'] ?? []);
        $matches = array_filter($vacancySkills, static fn (string $skill): bool => in_array(mb_strtolower($skill), $studentSkills, true));

        if ($matches !== []) {
            $reasons[] = 'Habilidades em comum: ' . implode(', ', array_slice($matches, 0, 3));
        }

        $distance = $profile ? $this->distanceKm($profile, $vacancy) : null;
        if ($profile && $distance !== null && $distance <= (int) ($profile['max_distance_km'] ?? 15)) {
            $reasons[] = 'Vaga dentro do seu raio de distancia';
        } elseif ($profile && !empty($profile['city']) && mb_strtolower((string) $profile['city']) === mb_strtolower((string) $vacancy['city'])) {
            $reasons[] = 'Vaga proxima a sua cidade';
        }

        if ($profile && (($profile['preferred_modality'] ?? 'qualquer') === $vacancy['modality'])) {
            $reasons[] = 'Modalidade alinhada a sua preferencia';
        }

        return $reasons ?: ['Perfil academico compativel com a area'];
    }

    public function distanceKm(array $profile, array $vacancy): ?float
    {
        foreach (['latitude', 'longitude'] as $field) {
            if ($profile[$field] === null || $profile[$field] === '' || $vacancy[$field] === null || $vacancy[$field] === '') {
                return null;
            }
        }

        $lat1 = deg2rad((float) $profile['latitude']);
        $lon1 = deg2rad((float) $profile['longitude']);
        $lat2 = deg2rad((float) $vacancy['latitude']);
        $lon2 = deg2rad((float) $vacancy['longitude']);

        $deltaLat = $lat2 - $lat1;
        $deltaLon = $lon2 - $lon1;
        $a = sin($deltaLat / 2) ** 2 + cos($lat1) * cos($lat2) * sin($deltaLon / 2) ** 2;

        return 6371 * (2 * atan2(sqrt($a), sqrt(1 - $a)));
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
