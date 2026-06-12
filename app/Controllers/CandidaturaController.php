<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Application;
use App\Models\AuditLog;
use App\Models\Company;
use App\Models\LoginAttempt;
use App\Models\Notification;
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
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';

        $attempts = new LoginAttempt();
        $rateKey = 'candidatura:' . $studentId;
        if ($attempts->isRateLimited($rateKey, $ip, 12, 15)) {
            throw new RuntimeException('Muitas candidaturas em pouco tempo. Aguarde alguns minutos.');
        }
        $attempts->record($rateKey, $ip, true);

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

    public function updateStatus(): void
    {
        Auth::requireRole(['empresa', 'admin']);

        $applicationId = (int) ($_POST['candidatura_id'] ?? 0);
        $status = (string) ($_POST['status'] ?? '');
        $allowed = ['visualizada', 'em_analise', 'entrevista', 'aprovada', 'reprovada', 'cancelada'];

        if (!in_array($status, $allowed, true)) {
            throw new RuntimeException('Status de candidatura invalido.');
        }

        $company = (new Company())->findByUserId((int) Auth::id());
        if (!$company) {
            throw new RuntimeException('Empresa nao encontrada.');
        }

        $applicationModel = new Application();
        $application = $applicationModel->findForCompany($applicationId, (int) $company['id']);
        if (!$application) {
            throw new RuntimeException('Candidatura nao encontrada para esta empresa.');
        }

        $applicationModel->updateStatus($applicationId, $status);
        (new AuditLog())->record('application_status_updated', [
            'application_id' => $applicationId,
            'status' => $status,
        ]);
        (new Notification())->create(
            (int) $application['student_id'],
            'candidatura_status',
            'Status da candidatura atualizado',
            'Sua candidatura para ' . $application['title'] . ' agora esta como ' . status_label($status) . '.'
        );

        flash('success', 'Status da candidatura atualizado.');
        redirect(route_url('dashboard'));
    }
}

