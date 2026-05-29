<?php
$title = 'Publicar vaga - EstágioMatch';
$active = 'empresa/vagas/nova';
require BASE_PATH . '/app/Views/partials/navbar.php';
?>

<main class="page-wrap">
    <div class="section-hd">
        <div>
            <h1 style="margin:0">Publicar nova vaga</h1>
            <p class="muted" style="margin:4px 0 0"><?= e($company['trade_name'] ?? 'Empresa') ?> · anúncio ativo por 30 dias no plano gratuito.</p>
        </div>
        <a class="btn btn-outline" href="<?= e(route_url('dashboard')) ?>">Voltar</a>
    </div>

    <form class="card" method="post" action="<?= e(action_url()) ?>">
        <?= csrf_field() ?>
        <input type="hidden" name="acao" value="empresa_criar_vaga">

        <div class="grid-2">
            <div class="form-group">
                <label class="label" for="title">Título da vaga</label>
                <input class="input" id="title" name="title" placeholder="Estágio em Desenvolvimento Web" required>
            </div>
            <div class="form-group">
                <label class="label" for="area">Área</label>
                <select class="input" id="area" name="area" required>
                    <option value="Tecnologia">Tecnologia</option>
                    <option value="Dados & BI">Dados & BI</option>
                    <option value="Design">Design</option>
                    <option value="Administração">Administração</option>
                    <option value="Marketing">Marketing</option>
                </select>
            </div>
            <div class="form-group">
                <label class="label" for="modality">Modalidade</label>
                <select class="input" id="modality" name="modality">
                    <option value="presencial">Presencial</option>
                    <option value="hibrido">Híbrido</option>
                    <option value="remoto">Remoto</option>
                </select>
            </div>
            <div class="form-group">
                <label class="label" for="scholarship">Bolsa</label>
                <input class="input" id="scholarship" type="number" min="0" step="50" name="scholarship" value="1000" required>
            </div>
            <div class="form-group">
                <label class="label" for="city">Cidade</label>
                <input class="input" id="city" name="city" value="<?= e($company['city'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label class="label" for="state">Estado</label>
                <input class="input" id="state" name="state" maxlength="2" value="<?= e($company['state'] ?? 'SP') ?>" required>
            </div>
            <div class="form-group">
                <label class="label" for="address">Endereço</label>
                <input class="input" id="address" name="address" value="<?= e($company['address'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label class="label" for="period">Período</label>
                <input class="input" id="period" name="period" placeholder="Manhã ou tarde">
            </div>
            <div class="form-group">
                <label class="label" for="duration_months">Duração (meses)</label>
                <input class="input" id="duration_months" type="number" min="1" max="24" name="duration_months" value="12">
            </div>
            <div class="form-group">
                <label class="label" for="workload">Carga horária</label>
                <input class="input" id="workload" name="workload" placeholder="6h/dia">
            </div>
        </div>

        <div class="form-group">
            <label class="label" for="skills">Habilidades exigidas</label>
            <input class="input" id="skills" name="skills" placeholder="React.js, Python, Git">
        </div>

        <div class="form-group">
            <label class="label" for="description">Descrição</label>
            <textarea class="textarea" id="description" name="description" required></textarea>
        </div>

        <div class="grid-2">
            <div class="form-group">
                <label class="label" for="responsibilities">Responsabilidades</label>
                <textarea class="textarea" id="responsibilities" name="responsibilities"></textarea>
            </div>
            <div class="form-group">
                <label class="label" for="requirements">Requisitos</label>
                <textarea class="textarea" id="requirements" name="requirements" required></textarea>
            </div>
        </div>

        <label class="inline" style="gap:8px;margin-bottom:18px">
            <input type="checkbox" name="transport_included" value="1">
            <span>Vale-transporte incluso</span>
        </label>

        <button class="btn btn-primary btn-lg" type="submit">Publicar vaga</button>
    </form>
</main>

