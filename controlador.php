<?php

declare(strict_types=1);

require __DIR__ . '/bootstrap.php';

use App\Controllers\DashboardController;
use App\Controllers\EmpresaController;
use App\Controllers\HomeController;
use App\Controllers\PerfilController;
use App\Controllers\VagaController;
use App\Core\Security;

Security::sendHeaders();

$route = trim((string) ($_GET['rota'] ?? 'home'), '/');
$route = $route === '' ? 'home' : $route;

$routes = [
    'home' => [HomeController::class, 'index'],
    'login' => [HomeController::class, 'login'],
    'dashboard' => [DashboardController::class, 'index'],
    'vagas' => [VagaController::class, 'index'],
    'vaga' => [VagaController::class, 'show'],
    'perfil' => [PerfilController::class, 'show'],
    'empresa/vagas/nova' => [EmpresaController::class, 'createVacancy'],
];

try {
    if (!isset($routes[$route])) {
        http_response_code(404);
        (new HomeController())->notFound();
        exit;
    }

    [$controllerClass, $method] = $routes[$route];
    (new $controllerClass())->{$method}();
} catch (Throwable $exception) {
    http_response_code(500);
    App\Core\Controller::renderError($exception);
}

