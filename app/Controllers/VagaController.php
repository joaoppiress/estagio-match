<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Application;
use App\Models\Rating;
use App\Models\Vacancy;

final class VagaController extends Controller
{
    public function index(): void
    {
        $filters = [
            'q' => trim((string) ($_GET['q'] ?? '')),
            'area' => trim((string) ($_GET['area'] ?? '')),
            'modality' => trim((string) ($_GET['modality'] ?? '')),
        ];

        $studentId = Auth::isStudent() ? Auth::id() : null;
        $vacancies = (new Vacancy())->active($filters, $studentId);

        $this->view('vagas/index', [
            'filters' => $filters,
            'vacancies' => $vacancies,
        ]);
    }

    public function show(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $studentId = Auth::isStudent() ? Auth::id() : null;
        $vacancy = (new Vacancy())->find($id, $studentId);

        if (!$vacancy) {
            http_response_code(404);
            require BASE_PATH . '/app/Views/errors/404.php';
            return;
        }

        $hasApplied = $studentId ? (new Application())->exists($id, $studentId) : false;
        $rating = (new Rating())->companySummary((int) $vacancy['company_id']);

        $this->view('vagas/show', [
            'vacancy' => $vacancy,
            'hasApplied' => $hasApplied,
            'rating' => $rating,
        ]);
    }
}

