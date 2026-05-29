<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Vacancy;

final class HomeController extends Controller
{
    public function index(): void
    {
        $featured = (new Vacancy())->active([], Auth::id(), 3);
        $this->view('home', ['featured' => $featured]);
    }

    public function login(): void
    {
        if (Auth::check()) {
            $this->redirect(route_url('dashboard'));
        }

        $this->view('auth/login');
    }

    public function notFound(): void
    {
        require BASE_PATH . '/app/Views/errors/404.php';
    }
}

