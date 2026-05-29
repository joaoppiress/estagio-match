<?php

declare(strict_types=1);

require __DIR__ . '/bootstrap.php';

use App\Controllers\AuthController;
use App\Controllers\CandidaturaController;
use App\Controllers\EmpresaController;
use App\Controllers\PerfilController;
use App\Core\Security;

Security::sendHeaders();
Security::requirePost();

$action = (string) ($_POST['acao'] ?? '');

try {
    if (!Security::verifyCsrf((string) ($_POST['_csrf'] ?? ''))) {
        throw new RuntimeException('Token de segurança inválido. Atualize a página e tente novamente.');
    }

    match ($action) {
        'login' => (new AuthController())->login(),
        'cadastrar' => (new AuthController())->register(),
        'logout' => (new AuthController())->logout(),
        'candidatar' => (new CandidaturaController())->store(),
        'perfil_atualizar' => (new PerfilController())->update(),
        'empresa_criar_vaga' => (new EmpresaController())->storeVacancy(),
        default => throw new RuntimeException('Ação não reconhecida.'),
    };
} catch (Throwable $exception) {
    flash('error', $exception->getMessage());
    redirect(back_url());
}

