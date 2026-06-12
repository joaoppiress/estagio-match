<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Servicos\ServicoGeocodificacao;
use App\Core\Validador;
use App\Models\AuditLog;
use App\Models\Company;
use App\Models\Notification;
use App\Models\StudentProfile;
use App\Models\Vacancy;
use RuntimeException;

final class EmpresaController extends Controller
{
    public function createVacancy(): void
    {
        Auth::requireRole(['empresa', 'admin']);
        $company = (new Company())->findByUserId((int) Auth::id());

        $this->view('empresa/create_vaga', ['company' => $company]);
    }

    public function storeVacancy(): void
    {
        Auth::requireRole(['empresa', 'admin']);

        $company = (new Company())->findByUserId((int) Auth::id());
        if (!$company) {
            throw new RuntimeException('Cadastre uma empresa antes de publicar vagas.');
        }

        $user = Auth::user();
        if (empty($user['email_verified_at'])) {
            throw new RuntimeException('Verifique seu e-mail antes de publicar vagas.');
        }

        $data = [
            'company_id' => (int) $company['id'],
            'title' => trim((string) ($_POST['title'] ?? '')),
            'area' => trim((string) ($_POST['area'] ?? '')),
            'description' => trim((string) ($_POST['description'] ?? '')),
            'responsibilities' => trim((string) ($_POST['responsibilities'] ?? '')),
            'requirements' => trim((string) ($_POST['requirements'] ?? '')),
            'modality' => (string) ($_POST['modality'] ?? 'presencial'),
            'city' => trim((string) ($_POST['city'] ?? '')),
            'state' => mb_strtoupper(trim((string) ($_POST['state'] ?? ''))),
            'address' => trim((string) ($_POST['address'] ?? '')),
            'period' => trim((string) ($_POST['period'] ?? '')),
            'duration_months' => (int) ($_POST['duration_months'] ?? 0),
            'start_date_label' => trim((string) ($_POST['start_date_label'] ?? 'Imediato')),
            'scholarship' => (float) ($_POST['scholarship'] ?? 0),
            'workload' => trim((string) ($_POST['workload'] ?? '')),
            'transport_included' => !empty($_POST['transport_included']),
            'skills' => trim((string) ($_POST['skills'] ?? '')),
        ];

        Validador::required($data, ['title', 'area', 'description', 'requirements', 'city', 'state'], 'Preencha todos os campos obrigatorios da vaga.');
        Validador::oneOf($data['modality'], ['presencial', 'remoto', 'hibrido'], 'Modalidade invalida.');
        if ($data['scholarship'] < 0) {
            throw new RuntimeException('Bolsa invalida.');
        }

        $location = (new ServicoGeocodificacao())->localizar(null, $data['city'], $data['state']);
        $data['latitude'] = $location['latitude'] ?? null;
        $data['longitude'] = $location['longitude'] ?? null;

        $vacancyId = (new Vacancy())->create($data);
        (new AuditLog())->record('vacancy_created', ['vacancy_id' => $vacancyId]);
        $this->notifyHighMatches($vacancyId);

        flash('success', 'Vaga publicada com seguranca.');
        redirect(route_url('vaga', ['id' => $vacancyId]));
    }

    public function boostVacancy(): void
    {
        Auth::requireRole(['empresa', 'admin']);

        $company = (new Company())->findByUserId((int) Auth::id());
        $vacancyId = (int) ($_POST['vaga_id'] ?? 0);
        if (!$company || !(new Vacancy())->setBoostedForCompany($vacancyId, (int) $company['id'], true)) {
            throw new RuntimeException('Nao foi possivel destacar esta vaga.');
        }

        (new AuditLog())->record('vacancy_boosted', ['vacancy_id' => $vacancyId]);
        flash('success', 'Vaga marcada como destaque.');
        redirect(route_url('dashboard'));
    }

    public function activatePremium(): void
    {
        Auth::requireRole(['empresa', 'admin']);

        $company = (new Company())->findByUserId((int) Auth::id());
        if (!$company) {
            throw new RuntimeException('Empresa nao encontrada.');
        }

        (new Company())->setPremium((int) $company['id'], true);
        (new AuditLog())->record('company_premium_activated', ['company_id' => $company['id']]);

        flash('success', 'Plano premium ativado para o MVP.');
        redirect(route_url('dashboard'));
    }

    private function notifyHighMatches(int $vacancyId): void
    {
        $vacancyModel = new Vacancy();
        $notifications = new Notification();

        foreach ((new StudentProfile())->activeStudentIds() as $studentId) {
            $vacancy = $vacancyModel->find($vacancyId, $studentId);
            if ($vacancy && (int) $vacancy['match_score'] >= 80) {
                $notifications->create(
                    $studentId,
                    'vaga_match',
                    'Nova vaga com alto match',
                    $vacancy['title'] . ' combina ' . $vacancy['match_score'] . '% com seu perfil.'
                );
            }
        }
    }
}
