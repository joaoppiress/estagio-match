<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Application;
use App\Models\Company;
use App\Models\Rating;
use App\Models\StudentProfile;
use App\Models\Vacancy;

final class DashboardController extends Controller
{
    public function index(): void
    {
        Auth::requireLogin();

        $user = Auth::user();
        $vacancyModel = new Vacancy();
        $applicationModel = new Application();

        if (Auth::isCompany()) {
            $company = (new Company())->findByUserId((int) $user['id']);
            $stats = $company ? $vacancyModel->companyStats((int) $company['id']) : ['vagas' => 0, 'ativas' => 0];
            $applications = $company ? $applicationModel->companyApplications((int) $company['id']) : [];
            $vacancies = $company ? $vacancyModel->forCompany((int) $company['id']) : [];

            $this->view('dashboard/index', [
                'user' => $user,
                'company' => $company,
                'stats' => $stats,
                'applications' => $applications,
                'vacancies' => $vacancies,
            ]);
            return;
        }

        $profileModel = new StudentProfile();
        $profile = $profileModel->findByUserId((int) $user['id']);
        $recommended = $vacancyModel->active([], (int) $user['id'], 6);
        $applications = $applicationModel->forStudent((int) $user['id'], 5);
        $avgMatch = $recommended === [] ? 0 : (int) round(array_sum(array_column($recommended, 'match_score')) / count($recommended));

        $this->view('dashboard/index', [
            'user' => $user,
            'profile' => $profile,
            'recommended' => $recommended,
            'applications' => $applications,
            'studentRating' => (new Rating())->studentAverage((int) $user['id']),
            'stats' => [
                'recommended' => count($recommended),
                'applications' => $applicationModel->countByStudent((int) $user['id']),
                'avg_match' => $avgMatch,
                'completeness' => (int) ($profile['profile_completeness'] ?? 25),
            ],
        ]);
    }
}

