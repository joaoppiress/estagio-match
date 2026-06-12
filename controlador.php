<?php

declare(strict_types=1);

require __DIR__ . '/bootstrap.php';

use App\Controllers\DashboardController;
use App\Controllers\AuthController;
use App\Controllers\EmpresaController;
use App\Controllers\HomeController;
use App\Controllers\PerfilController;
use App\Controllers\VagaController;
use App\Core\Roteador;
use App\Core\Security;

Security::sendHeaders();

$router = new Roteador();
$router->add('GET', 'home', HomeController::class, 'index');
$router->add('GET', 'login', HomeController::class, 'login');
$router->add('GET', 'esqueci-senha', AuthController::class, 'forgotPasswordForm');
$router->add('GET', 'redefinir-senha', AuthController::class, 'resetPasswordForm');
$router->add('GET', 'verificar-email', AuthController::class, 'verifyEmail');
$router->add('GET', 'dashboard', DashboardController::class, 'index');
$router->add('GET', 'vagas', VagaController::class, 'index');
$router->add('GET', 'vaga', VagaController::class, 'show');
$router->add('GET', 'perfil', PerfilController::class, 'show');
$router->add('GET', 'empresa/vagas/nova', EmpresaController::class, 'createVacancy');

try {
    $router->dispatch('GET', (string) ($_GET['rota'] ?? 'home'));
} catch (Throwable $exception) {
    if (http_response_code() === 404) {
        (new HomeController())->notFound();
        exit;
    }
    http_response_code(500);
    App\Core\Controller::renderError($exception);
}

