<?php
$title = 'EstágioMatch - Plataforma inteligente de estágios';
$active = 'home';
require BASE_PATH . '/app/Views/partials/navbar.php';
?>

<section class="hero">
    <div class="hero-inner">
        <div>
            <span class="badge badge-blue">Recomendação inteligente por perfil e localização</span>
            <h1>Seu próximo estágio, onde você merece estar.</h1>
            <p>
                Uma plataforma web para conectar estudantes e empresas com candidatura simplificada,
                reputação bidirecional, acessibilidade e matching baseado em dados reais.
            </p>
            <div class="inline" style="gap:12px;flex-wrap:wrap">
                <a class="btn btn-primary btn-lg" href="<?= e(route_url('login')) ?>#cadastro">Criar conta gratuita</a>
                <a class="btn btn-outline btn-lg" href="<?= e(route_url('vagas')) ?>">Explorar vagas</a>
            </div>
            <div class="hero-list">
                <span>Match por curso, habilidades, preferência e cidade</span>
                <span>Candidatura em menos de 2 minutos</span>
                <span>Avaliações transparentes entre estudantes e empresas</span>
            </div>
        </div>

        <div class="hero-panel">
            <div class="card">
                <div class="section-hd">
                    <div>
                        <div class="section-title">Vagas em destaque</div>
                        <p class="muted" style="margin:4px 0 0">Dados vindos do banco MySQL</p>
                    </div>
                </div>
                <div style="display:grid;gap:12px">
                    <?php foreach ($featured as $vacancy): ?>
                        <a class="vacancy-card" href="<?= e(route_url('vaga', ['id' => $vacancy['id']])) ?>">
                            <div class="card-row" style="justify-content:space-between;margin-bottom:12px">
                                <div class="inline" style="gap:12px">
                                    <span class="co-logo"><?= e($vacancy['logo_initials']) ?></span>
                                    <div>
                                        <strong><?= e($vacancy['title']) ?></strong>
                                        <p class="muted" style="margin:2px 0 0"><?= e($vacancy['trade_name']) ?> · <?= e($vacancy['city']) ?></p>
                                    </div>
                                </div>
                                <span class="match-pct"><?= e($vacancy['match_score']) ?>%</span>
                            </div>
                            <div class="match-bar"><div class="match-fill" style="width:<?= e($vacancy['match_score']) ?>%"></div></div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<main class="page-wrap" id="como-funciona">
    <div class="grid-3">
        <article class="card">
            <span class="badge badge-blue">1</span>
            <h2>Crie seu perfil</h2>
            <p class="muted">Dados acadêmicos, habilidades, cidade, disponibilidade e preferências profissionais.</p>
        </article>
        <article class="card">
            <span class="badge badge-green">2</span>
            <h2>Receba recomendações</h2>
            <p class="muted">O sistema cruza perfil, habilidades e localização para priorizar vagas compatíveis.</p>
        </article>
        <article class="card">
            <span class="badge badge-orange">3</span>
            <h2>Candidate-se rápido</h2>
            <p class="muted">Candidatura com CSRF, sessão segura e registro no histórico do estudante.</p>
        </article>
    </div>
</main>

