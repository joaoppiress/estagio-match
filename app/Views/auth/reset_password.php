<?php
$title = 'Redefinir senha - EstagioMatch';
$active = 'login';
require BASE_PATH . '/app/Views/partials/navbar.php';
?>

<main class="auth-shell" id="conteudo">
    <section class="auth-main">
        <form class="auth-card" method="post" action="<?= e(action_url()) ?>">
            <?= csrf_field() ?>
            <input type="hidden" name="acao" value="senha_redefinir">
            <input type="hidden" name="token" value="<?= e($token) ?>">
            <h1>Redefinir senha</h1>
            <div class="form-group">
                <label class="label" for="senha">Nova senha</label>
                <input class="input" id="senha" type="password" name="senha" minlength="10" data-password-meter autocomplete="new-password" required>
            </div>
            <div class="form-group">
                <label class="label" for="senha_confirmacao">Confirmar senha</label>
                <input class="input" id="senha_confirmacao" type="password" name="senha_confirmacao" data-confirm-for="senha" autocomplete="new-password" required>
                <p class="field-error" aria-live="polite"></p>
            </div>
            <button class="btn btn-primary btn-lg w-full" type="submit">Salvar nova senha</button>
        </form>
    </section>
</main>
