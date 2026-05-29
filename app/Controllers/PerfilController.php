<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Application;
use App\Models\AuditLog;
use App\Models\Rating;
use App\Models\StudentProfile;
use RuntimeException;

final class PerfilController extends Controller
{
    public function show(): void
    {
        Auth::requireRole(['estudante']);

        $user = Auth::user();
        $profileModel = new StudentProfile();

        $this->view('perfil/show', [
            'user' => $user,
            'profile' => $profileModel->findByUserId((int) $user['id']),
            'skills' => $profileModel->skills((int) $user['id']),
            'applications' => (new Application())->forStudent((int) $user['id'], 10),
            'studentRating' => (new Rating())->studentAverage((int) $user['id']),
        ]);
    }

    public function update(): void
    {
        Auth::requireRole(['estudante']);

        $userId = (int) Auth::id();
        $data = [
            'course' => trim((string) ($_POST['course'] ?? '')),
            'institution' => trim((string) ($_POST['institution'] ?? '')),
            'current_period' => (int) ($_POST['current_period'] ?? 0),
            'graduation_forecast' => trim((string) ($_POST['graduation_forecast'] ?? '')),
            'performance_index' => (string) ($_POST['performance_index'] ?? ''),
            'city' => trim((string) ($_POST['city'] ?? '')),
            'state' => mb_strtoupper(trim((string) ($_POST['state'] ?? ''))),
            'neighborhood' => trim((string) ($_POST['neighborhood'] ?? '')),
            'cep' => trim((string) ($_POST['cep'] ?? '')),
            'interests' => trim((string) ($_POST['interests'] ?? '')),
            'availability' => trim((string) ($_POST['availability'] ?? '')),
            'preferred_modality' => (string) ($_POST['preferred_modality'] ?? 'qualquer'),
            'max_distance_km' => (int) ($_POST['max_distance_km'] ?? 15),
            'min_scholarship' => (float) ($_POST['min_scholarship'] ?? 800),
            'bio' => trim((string) ($_POST['bio'] ?? '')),
            'portfolio_url' => trim((string) ($_POST['portfolio_url'] ?? '')),
            'accessibility_libras' => !empty($_POST['accessibility_libras']),
        ];

        if ($data['course'] === '' || $data['city'] === '' || $data['state'] === '') {
            throw new RuntimeException('Curso, cidade e estado são obrigatórios.');
        }
        if (!in_array($data['preferred_modality'], ['presencial', 'remoto', 'hibrido', 'qualquer'], true)) {
            throw new RuntimeException('Modalidade inválida.');
        }
        if ($data['portfolio_url'] !== '' && !filter_var($data['portfolio_url'], FILTER_VALIDATE_URL)) {
            throw new RuntimeException('URL do portfólio inválida.');
        }

        $profileModel = new StudentProfile();
        $profileModel->update($userId, $data);
        $profileModel->syncSkills($userId, (string) ($_POST['skills'] ?? ''));
        (new AuditLog())->record('profile_updated');

        flash('success', 'Perfil atualizado.');
        redirect(route_url('perfil'));
    }
}

