<?php

declare(strict_types=1);

require __DIR__ . '/bootstrap.php';

use App\Controllers\AuthController;
use App\Controllers\AvaliacaoController;
use App\Controllers\CandidaturaController;
use App\Controllers\EmpresaController;
use App\Controllers\NotificacaoController;
use App\Controllers\PerfilController;
use App\Core\Roteador;
use App\Core\Security;

Security::sendHeaders();
Security::requirePost();

$action = (string) ($_POST['acao'] ?? '');
$router = new Roteador();
$router->add('POST', 'login', AuthController::class, 'login');
$router->add('POST', 'cadastrar', AuthController::class, 'register');
$router->add('POST', 'logout', AuthController::class, 'logout');
$router->add('POST', 'senha_esqueci', AuthController::class, 'requestPasswordReset');
$router->add('POST', 'senha_redefinir', AuthController::class, 'resetPassword');
$router->add('POST', 'candidatar', CandidaturaController::class, 'store');
$router->add('POST', 'candidatura_status', CandidaturaController::class, 'updateStatus');
$router->add('POST', 'avaliacao_criar', AvaliacaoController::class, 'create');
$router->add('POST', 'notificacao_lida', NotificacaoController::class, 'markRead');
$router->add('POST', 'perfil_atualizar', PerfilController::class, 'update');
$router->add('POST', 'empresa_criar_vaga', EmpresaController::class, 'storeVacancy');
$router->add('POST', 'empresa_turbinar_vaga', EmpresaController::class, 'boostVacancy');
$router->add('POST', 'empresa_premium', EmpresaController::class, 'activatePremium');

try {
    if (!Security::verifyCsrf((string) ($_POST['_csrf'] ?? ''))) {
        throw new RuntimeException('Token de seguranca invalido. Atualize a pagina e tente novamente.');
    }

    $router->dispatch('POST', $action);
} catch (Throwable $exception) {
    flash('error', $exception->getMessage());
    redirect(back_url());
}
