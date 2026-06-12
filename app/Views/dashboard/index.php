<?php
use App\Core\Auth;

$title = 'Dashboard - EstágioMatch';
$active = 'dashboard';
require BASE_PATH . '/app/Views/partials/navbar.php';
?>

<main class="page-wrap" id="conteudo">
    <?php if (Auth::isCompany()): ?>
        <div class="section-hd">
            <div>
                <h1 class="m-0">Painel da empresa</h1>
                <p class="muted mt-1"><?= e($company['trade_name'] ?? $user['name']) ?> · gestão de vagas e candidaturas</p>
            </div>
            <div class="inline-gap">
                <?php if (empty($company['is_premium'])): ?>
                    <form method="post" action="<?= e(action_url()) ?>">
                        <?= csrf_field() ?>
                        <input type="hidden" name="acao" value="empresa_premium">
                        <button class="btn btn-outline" type="submit">Ativar premium</button>
                    </form>
                <?php else: ?>
                    <span class="badge badge-green">Premium</span>
                <?php endif; ?>
                <a class="btn btn-primary" href="<?= e(route_url('empresa/vagas/nova')) ?>">Publicar vaga</a>
            </div>
        </div>

        <section class="grid-4 mb-7" aria-label="Indicadores">
            <div class="stat" data-reveal>
                <div class="stat-val accent-blue" data-counter="<?= e($stats['ativas'] ?? 0) ?>">0</div>
                <div class="stat-label">Vagas ativas</div>
            </div>
            <div class="stat" data-reveal style="--reveal-delay:70ms">
                <div class="stat-val" data-counter="<?= e($stats['vagas'] ?? 0) ?>">0</div>
                <div class="stat-label">Vagas publicadas</div>
            </div>
            <div class="stat" data-reveal style="--reveal-delay:140ms">
                <div class="stat-val" data-counter="<?= e(count($applications)) ?>">0</div>
                <div class="stat-label">Candidaturas recentes</div>
            </div>
            <div class="stat" data-reveal style="--reveal-delay:210ms">
                <div class="stat-val"><?= e($company['rating_avg'] ?? '0.0') ?></div>
                <div class="stat-label">Reputação média</div>
            </div>
        </section>

        <section class="card mb-7">
            <div class="section-hd">
                <div class="section-title">Vagas publicadas</div>
            </div>
            <?php if (($vacancies ?? []) === []): ?>
                <p class="muted m-0">Nenhuma vaga publicada ainda.</p>
            <?php else: ?>
                <div class="table-list">
                    <?php foreach ($vacancies as $vacancy): ?>
                        <div class="table-row">
                            <div>
                                <strong><?= e($vacancy['title']) ?></strong>
                                <p class="muted mt-1"><?= e(vacancy_status_label($vacancy['status'])) ?> · <?= e($vacancy['is_boosted'] ? 'Destaque' : 'Padrao') ?></p>
                            </div>
                            <?php if (empty($vacancy['is_boosted'])): ?>
                                <form method="post" action="<?= e(action_url()) ?>">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="acao" value="empresa_turbinar_vaga">
                                    <input type="hidden" name="vaga_id" value="<?= e($vacancy['id']) ?>">
                                    <button class="btn btn-outline btn-sm" type="submit">Turbinar</button>
                                </form>
                            <?php else: ?>
                                <span class="badge badge-blue">Destaque</span>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>

        <section class="card">
            <div class="section-hd">
                <div class="section-title">Candidaturas recebidas</div>
            </div>
            <?php if ($applications === []): ?>
                <div class="empty-block">
                    <div class="empty-icon" aria-hidden="true">📨</div>
                    <strong>Ainda não há candidaturas</strong>
                    <p class="muted m-0">Publique uma vaga atrativa para começar a receber candidatos.</p>
                </div>
            <?php else: ?>
                <div class="table-list">
                    <?php foreach ($applications as $application): ?>
                        <div class="table-row">
                            <span class="avatar"><?= e(mb_substr($application['student_name'], 0, 1)) ?></span>
                            <div>
                                <strong><?= e($application['student_name']) ?></strong>
                                <p class="muted mt-1"><?= e($application['title']) ?> · <?= e($application['course'] ?? 'Curso não informado') ?></p>
                            </div>
                            <form method="post" action="<?= e(action_url()) ?>" class="inline-gap">
                                <?= csrf_field() ?>
                                <input type="hidden" name="acao" value="candidatura_status">
                                <input type="hidden" name="candidatura_id" value="<?= e($application['id']) ?>">
                                <select class="input input-sm" name="status" aria-label="Status da candidatura">
                                    <?php foreach (['visualizada', 'em_analise', 'entrevista', 'aprovada', 'reprovada', 'cancelada'] as $status): ?>
                                        <option value="<?= e($status) ?>" <?= selected($application['status'], $status) ?>><?= e(status_label($status)) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <button class="btn btn-outline btn-sm" type="submit">Atualizar</button>
                            </form>
                            <?php if ($application['status'] === 'aprovada'): ?>
                                <form method="post" action="<?= e(action_url()) ?>" class="inline-gap">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="acao" value="avaliacao_criar">
                                    <input type="hidden" name="candidatura_id" value="<?= e($application['id']) ?>">
                                    <input class="input input-sm" type="number" min="1" max="5" step="0.5" name="score" placeholder="Nota" required>
                                    <button class="btn btn-primary btn-sm" type="submit">Avaliar</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    <?php else: ?>
        <div class="section-hd">
            <div>
                <h1 class="m-0">Olá, <?= e(explode(' ', $user['name'])[0]) ?>!</h1>
                <p class="muted mt-1">Você tem <strong><?= e($stats['recommended']) ?></strong> recomendações baseadas no seu perfil.</p>
            </div>
            <a class="btn btn-primary" href="<?= e(route_url('vagas')) ?>">Ver todas as vagas</a>
        </div>

        <section class="grid-4 mb-7" aria-label="Indicadores">
            <div class="stat" data-reveal>
                <div class="stat-val accent-blue" data-counter="<?= e($stats['recommended']) ?>">0</div>
                <div class="stat-label">Novas recomendações</div>
            </div>
            <div class="stat" data-reveal style="--reveal-delay:70ms">
                <div class="stat-val" data-counter="<?= e($stats['applications']) ?>">0</div>
                <div class="stat-label">Candidaturas enviadas</div>
            </div>
            <div class="stat" data-reveal style="--reveal-delay:140ms">
                <div class="stat-val accent-green" data-counter="<?= e($stats['avg_match']) ?>" data-suffix="%">0</div>
                <div class="stat-label">Compatibilidade média</div>
            </div>
            <div class="stat" data-reveal style="--reveal-delay:210ms">
                <div class="stat-val"><?= e($studentRating ?: '0.0') ?></div>
                <div class="stat-label">Avaliação do estudante</div>
            </div>
        </section>

        <?php
        $completeness = (int) $stats['completeness'];
        $hasApplied = ($stats['applications'] ?? 0) > 0;
        $hasSkills = !empty($profile['interests']) || $completeness >= 60;
        ?>
        <?php if ($completeness < 100): ?>
            <section class="card card-accent mb-7" aria-labelledby="onboarding-title" data-reveal>
                <div class="flex-between mb-4">
                    <div class="inline-gap gap-16">
                        <div class="score-ring" style="--score:<?= e($completeness) ?>%"><span><?= e($completeness) ?>%</span></div>
                        <div>
                            <strong id="onboarding-title">Comece por aqui</strong>
                            <p class="muted mt-1">Complete estes passos para receber recomendações mais precisas.</p>
                        </div>
                    </div>
                    <a class="btn btn-primary" href="<?= e(route_url('perfil')) ?>">Completar perfil</a>
                </div>
                <ul class="checklist">
                    <li class="<?= $completeness >= 50 ? 'done' : '' ?>">
                        <span class="tick" aria-hidden="true">✓</span>
                        <span class="step-text">Complete seus dados acadêmicos e preferências</span>
                    </li>
                    <li class="<?= $hasSkills ? 'done' : '' ?>">
                        <span class="tick" aria-hidden="true">✓</span>
                        <span class="step-text">Adicione suas habilidades e áreas de interesse</span>
                    </li>
                    <li class="<?= $hasApplied ? 'done' : '' ?>">
                        <span class="tick" aria-hidden="true">✓</span>
                        <span class="step-text">Candidate-se à sua primeira vaga</span>
                    </li>
                </ul>
            </section>
        <?php else: ?>
            <section class="card card-accent mb-7">
                <div class="flex-between">
                    <div class="inline-gap gap-16">
                        <div class="score-ring" style="--score:100%"><span>100%</span></div>
                        <div>
                            <strong>Perfil completo 🎉</strong>
                            <p class="muted mt-1">Seu perfil está pronto para os melhores matches.</p>
                        </div>
                    </div>
                    <a class="btn btn-primary" href="<?= e(route_url('vagas')) ?>">Ver vagas</a>
                </div>
            </section>
        <?php endif; ?>

        <section class="mb-8" data-reveal>
            <div class="section-hd">
                <div>
                    <div class="section-title">Vagas recomendadas para você</div>
                    <p class="muted mt-1"><?= e($profile['course'] ?? 'Curso não informado') ?> · <?= e($profile['city'] ?? 'Cidade não informada') ?></p>
                </div>
                <a class="section-link" href="<?= e(route_url('vagas')) ?>">Ver todas</a>
            </div>
            <div class="grid-3">
                <?php foreach (array_slice($recommended, 0, 3) as $vacancy): ?>
                    <article class="vacancy-card <?= $vacancy['is_boosted'] ? 'featured' : '' ?>" data-card-link="<?= e(route_url('vaga', ['id' => $vacancy['id']])) ?>">
                        <div class="card-row flex-between mb-3">
                            <span class="co-logo" aria-hidden="true"><?= e($vacancy['logo_initials']) ?></span>
                            <div class="text-right">
                                <div class="match-pct"><?= e($vacancy['match_score']) ?>%</div>
                                <div class="match-label">compatível</div>
                            </div>
                        </div>
                        <h3 class="mb-1 m-0"><?= e($vacancy['title']) ?></h3>
                        <p class="muted mb-3"><?= e($vacancy['trade_name']) ?> · <?= e($vacancy['city']) ?></p>
                        <div class="match-bar" role="progressbar" aria-label="Compatibilidade"
                             aria-valuenow="<?= e($vacancy['match_score']) ?>" aria-valuemin="0" aria-valuemax="100">
                            <div class="match-fill" data-fill="<?= e($vacancy['match_score']) ?>"></div>
                        </div>
                        <p class="why-tag mt-3"><?= e($vacancy['match_reasons'][0]) ?></p>
                        <div class="divider"></div>
                        <div class="card-row flex-between">
                            <strong><?= e(brl($vacancy['scholarship'])) ?></strong>
                            <a class="btn btn-primary btn-sm" href="<?= e(route_url('vaga', ['id' => $vacancy['id']])) ?>">Detalhes</a>
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
                <div class="empty-block">
                    <div class="empty-icon" aria-hidden="true">📋</div>
                    <strong>Você ainda não enviou candidaturas</strong>
                    <p class="muted mb-4">Explore as vagas recomendadas e candidate-se em menos de 2 minutos.</p>
                    <a class="btn btn-primary" href="<?= e(route_url('vagas')) ?>">Explorar vagas</a>
                </div>
            <?php else: ?>
                <div class="table-list">
                    <?php foreach ($applications as $application): ?>
                        <div class="table-row">
                            <span class="co-logo"><?= e($application['logo_initials']) ?></span>
                            <div>
                                <strong><?= e($application['title']) ?></strong>
                                <p class="muted mt-1"><?= e($application['trade_name']) ?> · enviada em <?= e(date('d/m/Y', strtotime($application['created_at']))) ?></p>
                            </div>
                            <div class="inline-gap">
                                <span class="badge badge-green"><?= e(status_label($application['status'])) ?></span>
                                <?php if ($application['status'] === 'aprovada'): ?>
                                    <form method="post" action="<?= e(action_url()) ?>" class="inline-gap">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="acao" value="avaliacao_criar">
                                        <input type="hidden" name="candidatura_id" value="<?= e($application['id']) ?>">
                                        <input class="input input-sm" type="number" min="1" max="5" step="0.5" name="score" placeholder="Nota" required>
                                        <input class="input input-sm" type="number" min="1" max="5" step="0.5" name="score_learning" placeholder="Aprend." required>
                                        <input class="input input-sm" type="number" min="1" max="5" step="0.5" name="score_mentorship" placeholder="Ment." required>
                                        <input class="input input-sm" type="number" min="1" max="5" step="0.5" name="score_environment" placeholder="Amb." required>
                                        <button class="btn btn-primary btn-sm" type="submit">Avaliar</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    <?php endif; ?>
</main>
