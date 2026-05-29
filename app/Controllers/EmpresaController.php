<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\AuditLog;
use App\Models\Company;
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

        foreach (['title', 'area', 'description', 'requirements', 'city', 'state'] as $field) {
            if ($data[$field] === '') {
                throw new RuntimeException('Preencha todos os campos obrigatórios da vaga.');
            }
        }
        if (!in_array($data['modality'], ['presencial', 'remoto', 'hibrido'], true)) {
            throw new RuntimeException('Modalidade inválida.');
        }
        if ($data['scholarship'] < 0) {
            throw new RuntimeException('Bolsa inválida.');
        }

        $vacancyId = (new Vacancy())->create($data);
        (new AuditLog())->record('vacancy_created', ['vacancy_id' => $vacancyId]);

        flash('success', 'Vaga publicada com segurança.');
        redirect(route_url('vaga', ['id' => $vacancyId]));
    }
}

