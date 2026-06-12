<?php

declare(strict_types=1);

namespace App\Core\Servicos;

use PHPMailer\PHPMailer\Exception as MailerException;
use PHPMailer\PHPMailer\PHPMailer;
use RuntimeException;

final class EmailServico
{
    public function configurado(): bool
    {
        return trim((string) getenv('MAIL_HOST')) !== ''
            && trim((string) getenv('MAIL_FROM_ADDRESS')) !== '';
    }

    public function enviarVerificacaoEmail(string $toEmail, string $toName, string $verificationUrl): void
    {
        $subject = 'Verificação de e-mail - Estágio Match';
        $safeName = htmlspecialchars($toName, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $safeUrl = htmlspecialchars($verificationUrl, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

        $html = '<p>Olá, ' . $safeName . '.</p>'
            . '<p>Confirme seu e-mail para liberar todas as funcionalidades do Estágio Match.</p>'
            . '<p><a href="' . $safeUrl . '">Clique aqui para verificar seu e-mail</a>.</p>'
            . '<p>Este link expira em 24 horas.</p>'
            . '<p>Se você não criou uma conta, ignore este e-mail.</p>';

        $text = "Olá, {$toName}.\n\n"
            . "Confirme seu e-mail para liberar todas as funcionalidades do Estágio Match.\n"
            . "Acesse o link abaixo para verificar seu e-mail:\n{$verificationUrl}\n\n"
            . "Este link expira em 24 horas.\n"
            . "Se você não criou uma conta, ignore este e-mail.";

        $this->send($toEmail, $toName, $subject, $html, $text);
    }

    public function enviarRedefinicaoSenha(string $toEmail, string $toName, string $resetUrl): void
    {
        $subject = 'Redefinição de senha - Estágio Match';
        $safeName = htmlspecialchars($toName, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $safeUrl = htmlspecialchars($resetUrl, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

        $html = '<p>Olá, ' . $safeName . '.</p>'
            . '<p>Recebemos uma solicitação para redefinir sua senha no Estágio Match.</p>'
            . '<p><a href="' . $safeUrl . '">Clique aqui para redefinir sua senha</a>.</p>'
            . '<p>Este link expira em 1 hora.</p>'
            . '<p>Se você não solicitou a redefinição, ignore este e-mail.</p>';

        $text = "Olá, {$toName}.\n\n"
            . "Recebemos uma solicitação para redefinir sua senha no Estágio Match.\n"
            . "Acesse o link abaixo para redefinir sua senha:\n{$resetUrl}\n\n"
            . "Este link expira em 1 hora.\n"
            . "Se você não solicitou a redefinição, ignore este e-mail.";

        $this->send($toEmail, $toName, $subject, $html, $text);
    }

    private function send(string $toEmail, string $toName, string $subject, string $html, string $text): void
    {
        if (!class_exists(PHPMailer::class)) {
            throw new RuntimeException('PHPMailer não está disponível. Execute composer install.');
        }

        $host = trim((string) getenv('MAIL_HOST'));
        $port = (int) (getenv('MAIL_PORT') ?: 587);
        $username = trim((string) getenv('MAIL_USERNAME'));
        $password = (string) getenv('MAIL_PASSWORD');
        $encryption = trim((string) (getenv('MAIL_ENCRYPTION') ?: 'tls'));
        $fromAddress = trim((string) getenv('MAIL_FROM_ADDRESS'));
        $fromName = trim((string) (getenv('MAIL_FROM_NAME') ?: 'Estágio Match'));

        if ($host === '' || $fromAddress === '') {
            throw new RuntimeException('Configuração SMTP incompleta: MAIL_HOST e MAIL_FROM_ADDRESS são obrigatórios.');
        }

        $mail = new PHPMailer(true);

        try {
            $mail->CharSet = 'UTF-8';
            $mail->isSMTP();
            $mail->Host = $host;
            $mail->Port = $port;

            if ($username !== '') {
                $mail->SMTPAuth = true;
                $mail->Username = $username;
                $mail->Password = $password;
            }

            if ($encryption !== '') {
                $mail->SMTPSecure = match (mb_strtolower($encryption)) {
                    'ssl', 'smtps' => PHPMailer::ENCRYPTION_SMTPS,
                    'tls', 'starttls' => PHPMailer::ENCRYPTION_STARTTLS,
                    default => '',
                };
            }

            $mail->setFrom($fromAddress, $fromName);
            $mail->addAddress($toEmail, $toName);
            $mail->Subject = $subject;
            $mail->isHTML(true);
            $mail->Body = $html;
            $mail->AltBody = $text;
            $mail->send();
        } catch (MailerException $exception) {
            throw new RuntimeException($exception->getMessage(), previous: $exception);
        }
    }
}
