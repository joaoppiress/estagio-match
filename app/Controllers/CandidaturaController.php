<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Application;
use App\Models\AuditLog;
use App\Models\Vacancy;
use RuntimeException;

final class CandidaturaController extends Controller
{
    public function store(): void
    {
        Auth::requireRole(['estudante']);

        $vacancyId = (int) ($_POST['vaga_id'] ?? 0);
        $coverLetter = trim((string) ($_POST['carta'] ?? ''));
        $studentId = (int) Auth::id();

        $vacancy = (new Vacancy())->find($vacancyId, $studentId);
        if (!$vacancy || $vacancy['status'] !== 'active') {
            throw new RuntimeException('Vaga indisponível.');
        }

        $applicationModel = new Application();
        if ($applicationModel->exists($vacancyId, $studentId)) {
            throw new RuntimeException('Você já se candidatou a esta vaga.');
        }

        $applicationModel->create($vacancyId, $studentId, $coverLetter, (int) $vacancy['match_score']);
        (new AuditLog())->record('application_created', ['vacancy_id' => $vacancyId, 'match_score' => $vacancy['match_score']]);

        flash('success', 'Candidatura enviada com sucesso.');
        redirect(route_url('vaga', ['id' => $vacancyId]));
    }
}

