<?php
$title = 'Entrar ou cadastrar - EstágioMatch';
$active = 'login';
$showDemo = (bool) app_config('debug', false);
require BASE_PATH . '/app/Views/partials/navbar.php';
?>

<main class="auth-shell" id="conteudo">
    <section class="auth-side">
        <span class="badge badge-on-blue">Plataforma inteligente de estágios</span>
        <h1>Entre para uma experiência moderna de estágio.</h1>
        <p>Login seguro com sessão reforçada, proteção CSRF e bloqueio por excesso de tentativas.</p>
        <ul>
            <li>Match inteligente com vagas por perfil real</li>
            <li>Candidatura simplificada</li>
            <li>Avaliações transparentes de empresas</li>
            <li>Coleta mínima de dados, alinhada à LGPD</li>
        </ul>
    </section>

    <section class="auth-main">
        <div class="auth-card">
            <div class="pill-nav" role="tablist" aria-label="Alternar entre entrar e cadastrar">
                <button class="pill-tab active" type="button" role="tab" id="tab-login"
                        aria-selected="true" aria-controls="panel-login" data-auth-tab="login">Entrar</button>
                <button class="pill-tab" type="button" role="tab" id="tab-cadastro" tabindex="-1"
                        aria-selected="false" aria-controls="panel-cadastro" data-auth-tab="cadastro">Cadastrar</button>
            </div>

            <form method="post" action="<?= e(action_url()) ?>" data-auth-panel="login"
                  id="panel-login" role="tabpanel" aria-labelledby="tab-login">
                <?= csrf_field() ?>
                <input type="hidden" name="acao" value="login">
                <h2>Bom te ver de volta</h2>
                <p class="muted">Entre com seu e-mail e senha para continuar.</p>

                <div class="form-group">
                    <label class="label" for="login-email">E-mail</label>
                    <input class="input" id="login-email" type="email" name="email" value="<?= e(old('email')) ?>" autocomplete="email" required>
                    <p class="field-error" aria-live="polite"></p>
                </div>

                <div class="form-group">
                    <label class="label" for="login-password">Senha</label>
                    <input class="input" id="login-password" type="password" name="password" autocomplete="current-password" required>
                </div>

                <button class="btn btn-primary btn-lg w-full" type="submit">Entrar na plataforma</button>
                <?php if ($showDemo): ?>
                    <p class="muted text-center mt-3">Ambiente de desenvolvimento — senha demo: <strong>Estagio@12345</strong></p>
                <?php endif; ?>
            </form>

            <form method="post" action="<?= e(action_url()) ?>" data-auth-panel="cadastro" hidden
                  id="panel-cadastro" role="tabpanel" aria-labelledby="tab-cadastro">
                <?= csrf_field() ?>
                <input type="hidden" name="acao" value="cadastrar">
                <h2>Criar conta gratuita</h2>
                <p class="muted">Preencha os dados básicos para começar.</p>

                <div class="form-group">
                    <label class="label" for="tipo">Tipo de conta</label>
                    <select class="input" id="tipo" name="tipo" data-account-type>
                        <option value="estudante" <?= selected(old('tipo', 'estudante'), 'estudante') ?>>Estudante</option>
                        <option value="empresa" <?= selected(old('tipo'), 'empresa') ?>>Empresa</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="label" for="nome">Nome completo ou responsável</label>
                    <input class="input" id="nome" name="nome" value="<?= e(old('nome')) ?>" autocomplete="name" required>
                </div>

                <div class="form-group" data-company-field>
                    <label class="label" for="empresa">Nome da empresa</label>
                    <input class="input" id="empresa" name="empresa" value="<?= e(old('empresa')) ?>">
                </div>

                <div class="form-group">
                    <label class="label" for="email">E-mail</label>
                    <input class="input" id="email" type="email" name="email" value="<?= e(old('email')) ?>" autocomplete="email" required>
                    <p class="field-error" aria-live="polite"></p>
                </div>

                <div class="form-group">
                    <label class="label" for="senha">Senha forte</label>
                    <input class="input" id="senha" type="password" name="senha" minlength="10" autocomplete="new-password" data-password-meter required>
                </div>

                <div class="form-group">
                    <label class="label" for="senha_confirma">Confirmar senha</label>
                    <input class="input" id="senha_confirma" type="password" autocomplete="new-password" data-confirm-for="senha" required>
                    <p class="field-error" aria-live="polite"></p>
                </div>

                <div class="grid-2">
                    <div class="form-group" data-student-field>
                        <label class="label" for="curso">Curso</label>
                        <input class="input" id="curso" name="curso" value="<?= e(old('curso', 'Ciência da Computação')) ?>">
                    </div>
                    <div class="form-group" data-company-field>
                        <label class="label" for="setor">Setor</label>
                        <input class="input" id="setor" name="setor" value="<?= e(old('setor', 'Tecnologia')) ?>">
                    </div>
                    <div class="form-group">
                        <label class="label" for="cidade">Cidade</label>
                        <input class="input" id="cidade" name="cidade" value="<?= e(old('cidade', 'Presidente Prudente')) ?>" required>
                    </div>
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label class="label" for="estado">Estado</label>
                        <select class="input" id="estado" name="estado" required>
                            <?= uf_options(old('estado', 'SP')) ?>
                        </select>
                    </div>
                    <div class="form-group" data-student-field>
                        <label class="label" for="periodo">Período</label>
                        <input class="input" id="periodo" type="number" min="1" max="12" name="periodo" value="<?= e(old('periodo', 1)) ?>">
                    </div>
                </div>

                <label class="checkbox-row">
                    <input type="checkbox" name="lgpd" value="1" required>
                    <span class="muted">Aceito os Termos de Uso e a Política de Privacidade.</span>
                </label>

                <button class="btn btn-primary btn-lg w-full" type="submit">Criar minha conta</button>
                <p class="muted text-xs text-center mt-3">Senha mínima: 10 caracteres, maiúscula, minúscula, número e símbolo.</p>
            </form>
        </div>
    </section>
</main>
