<?php
$title = 'Recuperar senha - EstagioMatch';
$active = 'login';
require BASE_PATH . '/app/Views/partials/navbar.php';
?>

<main class="auth-shell" id="conteudo">
    <section class="auth-main">
        <form class="auth-card" method="post" action="<?= e(action_url()) ?>">
            <?= csrf_field() ?>
            <input type="hidden" name="acao" value="senha_esqueci">
            <h1>Recuperar senha</h1>
            <p class="muted">Informe o e-mail cadastrado para receber as instrucoes.</p>
            <div class="form-group">
                <label class="label" for="email">E-mail</label>
                <input class="input" id="email" type="email" name="email" autocomplete="email" required>
            </div>
            <button class="btn btn-primary btn-lg w-full" type="submit">Enviar instrucoes</button>
        </form>
    </section>
</main>
