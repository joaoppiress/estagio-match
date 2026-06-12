<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Database;
use App\Core\Security;
use App\Core\Servicos\EmailServico;
use App\Core\Servicos\ServicoGeocodificacao;
use App\Models\AuditLog;
use App\Models\Company;
use App\Models\EmailVerification;
use App\Models\LoginAttempt;
use App\Models\PasswordReset;
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
            throw new RuntimeException('Informe e-mail e senha validos.');
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
            throw new RuntimeException('Credenciais invalidas.');
        }

        $attempts->record($email, $ip, true, (int) $user['id']);
        $userModel->touchLogin((int) $user['id']);
        Auth::login($user);
        (new AuditLog())->record('login_success', ['email' => $email], (int) $user['id']);

        flash('success', 'Login realizado com seguranca.');
        redirect(route_url('dashboard'));
    }

    public function register(): void
    {
        $role = (string) ($_POST['tipo'] ?? 'estudante');
        $name = trim((string) ($_POST['nome'] ?? ''));
        $email = mb_strtolower(trim((string) ($_POST['email'] ?? '')));
        $password = (string) ($_POST['senha'] ?? '');
        $passwordConfirmation = (string) ($_POST['senha_confirmacao'] ?? '');
        $city = trim((string) ($_POST['cidade'] ?? ''));
        $state = mb_strtoupper(trim((string) ($_POST['estado'] ?? 'SP')));
        $cnpj = trim((string) ($_POST['cnpj'] ?? ''));
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';

        keep_old($_POST);

        $attempts = new LoginAttempt();
        $rateKey = 'cadastro:' . ($email ?: $ip);
        if ($attempts->isRateLimited($rateKey, $ip, 8, 15)) {
            throw new RuntimeException('Muitas tentativas de cadastro. Aguarde alguns minutos antes de tentar de novo.');
        }
        $attempts->record($rateKey, $ip, true);

        if (!in_array($role, ['estudante', 'empresa'], true)) {
            throw new RuntimeException('Tipo de conta invalido.');
        }
        if (mb_strlen($name) < 3) {
            throw new RuntimeException('Informe seu nome completo ou o nome do responsavel.');
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new RuntimeException('Informe um e-mail valido.');
        }
        if (empty($_POST['lgpd'])) {
            throw new RuntimeException('E necessario aceitar os termos e a politica de privacidade.');
        }
        if ($role === 'empresa' && !Security::isValidCnpj($cnpj)) {
            throw new RuntimeException('Informe um CNPJ valido.');
        }
        if ($password !== $passwordConfirmation) {
            throw new RuntimeException('A confirmacao de senha nao confere.');
        }

        $passwordErrors = Security::strongPasswordErrors($password);
        if ($passwordErrors !== []) {
            throw new RuntimeException(implode(' ', $passwordErrors));
        }

        $userModel = new User();
        if ($userModel->findByEmail($email)) {
            throw new RuntimeException('Este e-mail ja esta cadastrado.');
        }

        $db = Database::connection();
        $db->beginTransaction();
        $verificationToken = bin2hex(random_bytes(32));

        try {
            $userId = $userModel->create([
                'role' => $role,
                'name' => $name,
                'email' => $email,
                'password' => $password,
            ]);

            if ($role === 'estudante') {
                $location = (new ServicoGeocodificacao())->localizar(null, $city, $state);
                (new StudentProfile())->createForUser($userId, [
                    'course' => trim((string) ($_POST['curso'] ?? '')),
                    'institution' => trim((string) ($_POST['instituicao'] ?? '')),
                    'current_period' => (int) ($_POST['periodo'] ?? 1),
                    'city' => $city,
                    'state' => $state ?: 'SP',
                    'latitude' => $location['latitude'] ?? null,
                    'longitude' => $location['longitude'] ?? null,
                    'interests' => trim((string) ($_POST['interesses'] ?? '')),
                    'preferred_modality' => 'qualquer',
                ]);
            } else {
                (new Company())->createForUser($userId, [
                    'trade_name' => trim((string) ($_POST['empresa'] ?? $name)),
                    'sector' => trim((string) ($_POST['setor'] ?? '')),
                    'city' => $city,
                    'state' => $state ?: 'SP',
                    'description' => 'Empresa cadastrada no EstagioMatch.',
                    'cnpj_hash' => hash('sha256', preg_replace('/\D/', '', $cnpj) ?: $cnpj),
                ]);
            }

            (new EmailVerification())->create($userId, hash('sha256', $verificationToken));
            $db->commit();
        } catch (\Throwable $exception) {
            $db->rollBack();
            throw $exception;
        }

        $user = $userModel->findById($userId);
        Auth::login($user);

        $verificationUrl = $this->absoluteUrl(route_url('verificar-email', ['token' => $verificationToken]));
        $auditLog = new AuditLog();
        $auditLog->record('register_success', ['role' => $role, 'verification_url' => $verificationUrl], $userId);

        $emailService = new EmailServico();
        if ($emailService->configurado()) {
            try {
                $emailService->enviarVerificacaoEmail($email, $name, $verificationUrl);
                $auditLog->record('email_verification_sent', ['role' => $role, 'email' => $email], $userId);
            } catch (\Throwable $exception) {
                $auditLog->record('email_verification_failed', [
                    'role' => $role,
                    'email' => $email,
                    'error' => $exception->getMessage(),
                ], $userId);
            }
        }

        $message = 'Conta criada com sucesso. Verifique seu e-mail para liberar todas as funcionalidades.';
        if ((bool) app_config('debug', false)) {
            $message .= ' Link de dev: ' . $verificationUrl;
        }
        flash('success', $message);
        redirect(route_url('dashboard'));
    }

    public function forgotPasswordForm(): void
    {
        $this->view('auth/forgot_password');
    }

    public function requestPasswordReset(): void
    {
        $email = mb_strtolower(trim((string) ($_POST['email'] ?? '')));
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new RuntimeException('Informe um e-mail valido.');
        }

        $attempts = new LoginAttempt();
        $rateKey = 'senha:' . $email;
        if ($attempts->isRateLimited($rateKey, $ip, 5, 15)) {
            throw new RuntimeException('Muitas solicitacoes. Aguarde alguns minutos antes de tentar de novo.');
        }
        $attempts->record($rateKey, $ip, true);

        $user = (new User())->findByEmail($email);
        if ($user) {
            $token = bin2hex(random_bytes(32));
            (new PasswordReset())->create((int) $user['id'], hash('sha256', $token));
            $resetUrl = $this->absoluteUrl(route_url('redefinir-senha', ['token' => $token]));
            $auditLog = new AuditLog();
            $auditLog->record('password_reset_requested', ['reset_url' => $resetUrl], (int) $user['id']);

            try {
                (new EmailServico())->enviarRedefinicaoSenha(
                    (string) $user['email'],
                    (string) $user['name'],
                    $resetUrl
                );
                $auditLog->record('password_reset_email_sent', ['email' => $email], (int) $user['id']);
            } catch (\Throwable $exception) {
                $auditLog->record('password_reset_email_failed', [
                    'email' => $email,
                    'error' => $exception->getMessage(),
                ], (int) $user['id']);

                $message = (bool) app_config('debug', false)
                    ? 'O link foi gerado, mas o e-mail nao foi enviado: ' . $exception->getMessage()
                    : 'Se o e-mail estiver cadastrado, enviaremos as instrucoes de redefinicao.';
                flash((bool) app_config('debug', false) ? 'error' : 'success', $message);
                redirect(route_url('login'));
            }
        }

        flash('success', 'Se o e-mail estiver cadastrado, enviaremos as instrucoes de redefinicao.');
        redirect(route_url('login'));
    }

    public function resetPasswordForm(): void
    {
        $token = (string) ($_GET['token'] ?? '');
        if ($token === '' || !(new PasswordReset())->findValid(hash('sha256', $token))) {
            throw new RuntimeException('Link de redefinicao invalido ou expirado.');
        }

        $this->view('auth/reset_password', ['token' => $token]);
    }

    public function resetPassword(): void
    {
        $token = (string) ($_POST['token'] ?? '');
        $password = (string) ($_POST['senha'] ?? '');
        $confirmation = (string) ($_POST['senha_confirmacao'] ?? '');
        $reset = (new PasswordReset())->findValid(hash('sha256', $token));

        if (!$reset) {
            throw new RuntimeException('Link de redefinicao invalido ou expirado.');
        }
        if ($password !== $confirmation) {
            throw new RuntimeException('A confirmacao de senha nao confere.');
        }

        $errors = Security::strongPasswordErrors($password);
        if ($errors !== []) {
            throw new RuntimeException(implode(' ', $errors));
        }

        (new User())->updatePasswordHash((int) $reset['user_id'], Security::passwordHash($password));
        (new PasswordReset())->markUsed((int) $reset['id']);
        (new AuditLog())->record('password_reset_completed', [], (int) $reset['user_id']);

        flash('success', 'Senha redefinida com sucesso. Entre novamente.');
        redirect(route_url('login'));
    }

    public function verifyEmail(): void
    {
        $token = (string) ($_GET['token'] ?? '');
        $verification = (new EmailVerification())->findValid(hash('sha256', $token));

        if (!$verification) {
            throw new RuntimeException('Link de verificacao invalido ou expirado.');
        }

        (new User())->markEmailVerified((int) $verification['user_id']);
        (new EmailVerification())->markUsed((int) $verification['id']);
        (new AuditLog())->record('email_verified', [], (int) $verification['user_id']);

        flash('success', 'E-mail verificado com sucesso.');
        redirect(route_url('dashboard'));
    }

    public function logout(): void
    {
        (new AuditLog())->record('logout');
        Auth::logout();
        redirect(route_url('home'));
    }

    private function absoluteUrl(string $url): string
    {
        if (preg_match('/^https?:\/\//i', $url)) {
            return $url;
        }

        $base = rtrim((string) app_config('base_url', ''), '/');
        if (preg_match('/^https?:\/\//i', $base)) {
            $path = parse_url($url, PHP_URL_PATH) ?: '';
            $query = parse_url($url, PHP_URL_QUERY);

            return $base . '/' . ltrim($path, '/') . ($query ? '?' . $query : '');
        }

        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = (string) ($_SERVER['HTTP_HOST'] ?? 'localhost');

        return $scheme . '://' . $host . $url;
    }
}
