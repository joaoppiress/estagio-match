<?php
use App\Core\Auth;

$title = 'Dashboard - EstágioMatch';
$active = 'dashboard';
require BASE_PATH . '/app/Views/partials/navbar.php';
?>

<main class="page-wrap">
    <?php if (Auth::isCompany()): ?>
        <div class="section-hd">
            <div>
                <h1 style="margin:0">Painel da empresa</h1>
                <p class="muted" style="margin:4px 0 0"><?= e($company['trade_name'] ?? $user['name']) ?> · gestão de vagas e candidaturas</p>
            </div>
            <a class="btn btn-primary" href="<?= e(route_url('empresa/vagas/nova')) ?>">Publicar vaga</a>
        </div>

        <section class="grid-4" style="margin-bottom:28px">
            <div class="stat">
                <div class="stat-val"><?= e($stats['ativas'] ?? 0) ?></div>
                <div class="stat-label">Vagas ativas</div>
            </div>
            <div class="stat">
                <div class="stat-val"><?= e($stats['vagas'] ?? 0) ?></div>
                <div class="stat-label">Vagas publicadas</div>
            </div>
            <div class="stat">
                <div class="stat-val"><?= e(count($applications)) ?></div>
                <div class="stat-label">Candidaturas recentes</div>
            </div>
            <div class="stat">
                <div class="stat-val"><?= e($company['rating_avg'] ?? '0.0') ?></div>
                <div class="stat-label">Reputação média</div>
            </div>
        </section>

        <section class="card">
            <div class="section-hd">
                <div class="section-title">Candidaturas recebidas</div>
            </div>
            <?php if ($applications === []): ?>
                <p class="muted">Ainda não há candidaturas nas suas vagas.</p>
            <?php else: ?>
                <div class="table-list">
                    <?php foreach ($applications as $application): ?>
                        <div class="table-row">
                            <span class="avatar"><?= e(mb_substr($application['student_name'], 0, 1)) ?></span>
                            <div>
                                <strong><?= e($application['student_name']) ?></strong>
                                <p class="muted" style="margin:2px 0 0"><?= e($application['title']) ?> · <?= e($application['course'] ?? 'Curso não informado') ?></p>
                            </div>
                            <span class="badge badge-blue"><?= e(status_label($application['status'])) ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    <?php else: ?>
        <div class="section-hd">
            <div>
                <h1 style="margin:0">Olá, <?= e(explode(' ', $user['name'])[0]) ?>!</h1>
                <p class="muted" style="margin:4px 0 0">Você tem <strong><?= e($stats['recommended']) ?></strong> recomendações baseadas no seu perfil.</p>
            </div>
            <a class="btn btn-primary" href="<?= e(route_url('vagas')) ?>">Ver todas as vagas</a>
        </div>

        <section class="grid-4" style="margin-bottom:28px">
            <div class="stat">
                <div class="stat-val" style="color:var(--blue)"><?= e($stats['recommended']) ?></div>
                <div class="stat-label">Novas recomendações</div>
            </div>
            <div class="stat">
                <div class="stat-val"><?= e($stats['applications']) ?></div>
                <div class="stat-label">Candidaturas enviadas</div>
            </div>
            <div class="stat">
                <div class="stat-val" style="color:var(--green)"><?= e($stats['avg_match']) ?>%</div>
                <div class="stat-label">Compatibilidade média</div>
            </div>
            <div class="stat">
                <div class="stat-val"><?= e($studentRating ?: '0.0') ?></div>
                <div class="stat-label">Avaliação do estudante</div>
            </div>
        </section>

        <section class="card" style="margin-bottom:28px;background:var(--blue-soft);border-color:var(--blue-line)">
            <div class="card-row" style="justify-content:space-between;gap:18px">
                <div class="inline" style="gap:16px">
                    <div class="score-ring" style="--score:<?= e($stats['completeness']) ?>%"><span><?= e($stats['completeness']) ?>%</span></div>
                    <div>
                        <strong>Perfil <?= e($stats['completeness']) ?>% completo</strong>
                        <p class="muted" style="margin:4px 0 0">Completar curso, habilidades e preferências aumenta a qualidade do match.</p>
                    </div>
                </div>
                <a class="btn btn-primary" href="<?= e(route_url('perfil')) ?>">Completar perfil</a>
            </div>
        </section>

        <section style="margin-bottom:30px">
            <div class="section-hd">
                <div>
                    <div class="section-title">Vagas recomendadas para você</div>
                    <p class="muted" style="margin:3px 0 0"><?= e($profile['course'] ?? 'Curso não informado') ?> · <?= e($profile['city'] ?? 'Cidade não informada') ?></p>
                </div>
                <a class="section-link" href="<?= e(route_url('vagas')) ?>">Ver todas</a>
            </div>
            <div class="grid-3">
                <?php foreach (array_slice($recommended, 0, 3) as $vacancy): ?>
                    <article class="vacancy-card <?= $vacancy['is_boosted'] ? 'featured' : '' ?>" data-card-link="<?= e(route_url('vaga', ['id' => $vacancy['id']])) ?>">
                        <div class="card-row" style="justify-content:space-between;margin-bottom:12px">
                            <span class="co-logo"><?= e($vacancy['logo_initials']) ?></span>
                            <div style="text-align:right">
                                <div class="match-pct"><?= e($vacancy['match_score']) ?>%</div>
                                <div class="match-label">compatível</div>
                            </div>
                        </div>
                        <h3 style="margin:0 0 4px"><?= e($vacancy['title']) ?></h3>
                        <p class="muted" style="margin:0 0 12px"><?= e($vacancy['trade_name']) ?> · <?= e($vacancy['city']) ?></p>
                        <div class="match-bar"><div class="match-fill" style="width:<?= e($vacancy['match_score']) ?>%"></div></div>
                        <p class="why-tag" style="margin-top:12px"><?= e($vacancy['match_reasons'][0]) ?></p>
                        <div class="divider"></div>
                        <div class="card-row" style="justify-content:space-between">
                            <strong><?= e(brl($vacancy['scholarship'])) ?></strong>
                            <a class="btn btn-primary" href="<?= e(route_url('vaga', ['id' => $vacancy['id']])) ?>">Detalhes</a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="card">
            <div class="section-hd">
                <div class="section-title">Minhas candidaturas</div>
            </div>
            <?php if ($applications === []): ?>
                <p class="muted">Você ainda não enviou candidaturas.</p>
            <?php else: ?>
                <div class="table-list">
                    <?php foreach ($applications as $application): ?>
                        <div class="table-row">
                            <span class="co-logo"><?= e($application['logo_initials']) ?></span>
                            <div>
                                <strong><?= e($application['title']) ?></strong>
                                <p class="muted" style="margin:2px 0 0"><?= e($application['trade_name']) ?> · enviada em <?= e(date('d/m/Y', strtotime($application['created_at']))) ?></p>
                            </div>
                            <span class="badge badge-green"><?= e(status_label($application['status'])) ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    <?php endif; ?>
</main>

