<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Database;
use App\Core\Security;
use App\Models\AuditLog;
use App\Models\Company;
use App\Models\LoginAttempt;
use App\Models\StudentProfile;
use App\Models\User;
use RuntimeException;

final class AuthController extends Controller
{
    public function login(): void
    {
        $email = mb_strtolower(trim((string) ($_POST['email'] ?? '')));
        $password = (string) ($_POST['password'] ?? '');
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';

        keep_old(['email' => $email]);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') {
            throw new RuntimeException('Informe e-mail e senha válidos.');
        }

        $attempts = new LoginAttempt();
        if ($attempts->isLocked($email, $ip)) {
            throw new RuntimeException('Muitas tentativas incorretas. Aguarde alguns minutos antes de tentar de novo.');
        }

        $userModel = new User();
        $user = $userModel->findByEmail($email);

        if (!$user || $user['status'] !== 'active' || !password_verify($password, (string) $user['password_hash'])) {
            $attempts->record($email, $ip, false, $user['id'] ?? null);
            (new AuditLog())->record('login_failed', ['email' => $email], $user['id'] ?? null);
            throw new RuntimeException('Credenciais inválidas.');
        }

        $attempts->record($email, $ip, true, (int) $user['id']);
        $userModel->touchLogin((int) $user['id']);
        Auth::login($user);
        (new AuditLog())->record('login_success', ['email' => $email], (int) $user['id']);

        flash('success', 'Login realizado com segurança.');
        redirect(route_url('dashboard'));
    }

    public function register(): void
    {
        $role = (string) ($_POST['tipo'] ?? 'estudante');
        $name = trim((string) ($_POST['nome'] ?? ''));
        $email = mb_strtolower(trim((string) ($_POST['email'] ?? '')));
        $password = (string) ($_POST['senha'] ?? '');
        $city = trim((string) ($_POST['cidade'] ?? ''));
        $state = mb_strtoupper(trim((string) ($_POST['estado'] ?? 'SP')));

        keep_old($_POST);

        if (!in_array($role, ['estudante', 'empresa'], true)) {
            throw new RuntimeException('Tipo de conta inválido.');
        }
        if (mb_strlen($name) < 3) {
            throw new RuntimeException('Informe seu nome completo ou o nome do responsável.');
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new RuntimeException('Informe um e-mail válido.');
        }
        if (empty($_POST['lgpd'])) {
            throw new RuntimeException('É necessário aceitar os termos e a política de privacidade.');
        }

        $passwordErrors = Security::strongPasswordErrors($password);
        if ($passwordErrors !== []) {
            throw new RuntimeException(implode(' ', $passwordErrors));
        }

        $userModel = new User();
        if ($userModel->findByEmail($email)) {
            throw new RuntimeException('Este e-mail já está cadastrado.');
        }

        $db = Database::connection();
        $db->beginTransaction();

        try {
            $userId = $userModel->create([
                'role' => $role,
                'name' => $name,
                'email' => $email,
                'password' => $password,
            ]);

            if ($role === 'estudante') {
                (new StudentProfile())->createForUser($userId, [
                    'course' => trim((string) ($_POST['curso'] ?? '')),
                    'institution' => trim((string) ($_POST['instituicao'] ?? '')),
                    'current_period' => (int) ($_POST['periodo'] ?? 1),
                    'city' => $city,
                    'state' => $state ?: 'SP',
                    'interests' => trim((string) ($_POST['interesses'] ?? '')),
                    'preferred_modality' => 'qualquer',
                ]);
            } else {
                (new Company())->createForUser($userId, [
                    'trade_name' => trim((string) ($_POST['empresa'] ?? $name)),
                    'sector' => trim((string) ($_POST['setor'] ?? '')),
                    'city' => $city,
                    'state' => $state ?: 'SP',
                    'description' => 'Empresa cadastrada no EstágioMatch.',
                ]);
            }

            $db->commit();
        } catch (\Throwable $exception) {
            $db->rollBack();
            throw $exception;
        }

        $user = $userModel->findById($userId);
        Auth::login($user);
        (new AuditLog())->record('register_success', ['role' => $role], $userId);

        flash('success', 'Conta criada com sucesso. Bem-vindo ao EstágioMatch!');
        redirect(route_url('dashboard'));
    }

    public function logout(): void
    {
        (new AuditLog())->record('logout');
        Auth::logout();
        redirect(route_url('home'));
    }
}

