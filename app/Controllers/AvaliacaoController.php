<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Application;
use App\Models\AuditLog;
use App\Models\Company;
use App\Models\Rating;
use RuntimeException;

final class AvaliacaoController extends Controller
{
    public function create(): void
    {
        Auth::requireLogin();

        $applicationId = (int) ($_POST['candidatura_id'] ?? 0);
        $application = (new Application())->findApprovedById($applicationId);
        if (!$application) {
            throw new RuntimeException('Avaliacao liberada apenas para candidaturas aprovadas.');
        }

        $isStudent = Auth::isStudent() && (int) Auth::id() === (int) $application['student_id'];
        $isCompany = Auth::isCompany() && (int) Auth::id() === (int) $application['company_user_id'];
        if (!$isStudent && !$isCompany) {
            throw new RuntimeException('Voce nao tem permissao para avaliar esta candidatura.');
        }

        $type = $isStudent ? 'estudante_para_empresa' : 'empresa_para_estudante';
        $ratingModel = new Rating();
        if ($ratingModel->alreadyRated($applicationId, (int) Auth::id(), $type)) {
            throw new RuntimeException('Voce ja avaliou esta candidatura.');
        }

        $ratingModel->create([
            'application_id' => $applicationId,
            'rater_user_id' => (int) Auth::id(),
            'rated_user_id' => $isCompany ? (int) $application['student_id'] : null,
            'rated_company_id' => $isStudent ? (int) $application['company_id'] : null,
            'rating_type' => $type,
            'score' => $this->score('score'),
            'score_learning' => $isStudent ? $this->optionalScore('score_learning') : null,
            'score_mentorship' => $isStudent ? $this->optionalScore('score_mentorship') : null,
            'score_environment' => $isStudent ? $this->optionalScore('score_environment') : null,
            'comment' => trim((string) ($_POST['comentario'] ?? '')) ?: null,
        ]);

        if ($isStudent) {
            (new Company())->refreshRatingAverage((int) $application['company_id']);
        }

        (new AuditLog())->record('rating_created', ['application_id' => $applicationId, 'type' => $type]);
        flash('success', 'Avaliacao registrada com sucesso.');
        redirect(back_url());
    }

    private function score(string $field): float
    {
        $score = (float) ($_POST[$field] ?? 0);
        if ($score < 1 || $score > 5) {
            throw new RuntimeException('Informe notas entre 1 e 5.');
        }

        return $score;
    }

    private function optionalScore(string $field): ?float
    {
        return isset($_POST[$field]) && $_POST[$field] !== '' ? $this->score($field) : null;
    }
}
