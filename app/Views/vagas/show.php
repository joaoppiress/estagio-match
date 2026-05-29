<?php
$title = $vacancy['title'] . ' - EstágioMatch';
$active = 'vagas';
require BASE_PATH . '/app/Views/partials/navbar.php';
?>

<main class="page-wrap">
    <div class="breadcrumb">
        <a href="<?= e(route_url('vagas')) ?>">Vagas</a>
        <span>/</span>
        <span><?= e($vacancy['title']) ?></span>
    </div>

    <div class="layout-2">
        <section>
            <article class="card" style="margin-bottom:20px">
                <div class="card-row" style="align-items:flex-start;gap:16px;margin-bottom:20px">
                    <span class="co-logo" style="height:58px;width:58px;font-size:17px"><?= e($vacancy['logo_initials']) ?></span>
                    <div style="flex:1">
                        <h1 style="margin:0 0 4px"><?= e($vacancy['title']) ?></h1>
                        <p class="muted" style="margin:0"><?= e($vacancy['trade_name']) ?> · <?= e($vacancy['city']) ?>, <?= e($vacancy['state']) ?></p>
                        <div class="chips" style="margin-top:12px">
                            <span class="badge badge-blue"><?= e(modality_label($vacancy['modality'])) ?></span>
                            <span class="badge badge-green"><?= e($vacancy['published_at'] ? 'Publicada em ' . date('d/m/Y', strtotime($vacancy['published_at'])) : 'Publicada recentemente') ?></span>
                            <span class="badge badge-gray"><?= e($vacancy['area']) ?></span>
                        </div>
                    </div>
                </div>

                <div class="card" style="background:var(--blue-soft);border-color:var(--blue-line);box-shadow:none">
                    <div class="card-row" style="gap:18px">
                        <div style="text-align:center">
                            <div class="match-pct" style="font-size:40px"><?= e($vacancy['match_score']) ?>%</div>
                            <div class="match-label">compatível</div>
                        </div>
                        <div>
                            <strong>Por que essa vaga foi recomendada:</strong>
                            <ul style="margin:8px 0 0;padding-left:18px">
                                <?php foreach ($vacancy['match_reasons'] as $reason): ?>
                                    <li><?= e($reason) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </article>

            <article class="card" style="margin-bottom:20px">
                <h2>Sobre a vaga</h2>
                <p><?= nl2br(e($vacancy['description'])) ?></p>

                <?php if (!empty($vacancy['responsibilities'])): ?>
                    <div class="divider"></div>
                    <h3>Responsabilidades</h3>
                    <p><?= nl2br(e($vacancy['responsibilities'])) ?></p>
                <?php endif; ?>

                <div class="divider"></div>
                <h3>Requisitos</h3>
                <p><?= nl2br(e($vacancy['requirements'])) ?></p>
                <div class="chips">
                    <?php foreach ($vacancy['skills'] as $skill): ?>
                        <span class="badge badge-blue"><?= e($skill['skill']) ?></span>
                    <?php endforeach; ?>
                </div>
            </article>

            <article class="card" style="margin-bottom:20px">
                <h2>Avaliação da empresa</h2>
                <div class="card-row" style="gap:26px;align-items:flex-start">
                    <div style="text-align:center">
                        <div style="font-size:42px;font-weight:900"><?= e($rating['avg_score']) ?></div>
                        <div class="muted"><?= e($rating['total']) ?> avaliações</div>
                    </div>
                    <div style="flex:1;display:grid;gap:10px">
                        <?php foreach (['environment' => 'Ambiente', 'learning' => 'Aprendizado', 'mentorship' => 'Mentoria'] as $key => $label): ?>
                            <div>
                                <div class="section-hd" style="margin-bottom:4px">
                                    <span><?= e($label) ?></span>
                                    <strong><?= e($rating[$key]) ?></strong>
                                </div>
                                <div class="eval-bar"><div class="eval-fill" style="width:<?= e(((float) $rating[$key] / 5) * 100) ?>%"></div></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </article>

            <article class="card">
                <h2>Sobre a empresa</h2>
                <p><?= e($vacancy['company_description'] ?? 'Empresa cadastrada na plataforma EstágioMatch.') ?></p>
                <div class="grid-3">
                    <div>
                        <span class="match-label">Setor</span>
                        <strong style="display:block"><?= e($vacancy['sector'] ?? 'Não informado') ?></strong>
                    </div>
                    <div>
                        <span class="match-label">Cidade</span>
                        <strong style="display:block"><?= e($vacancy['city']) ?>, <?= e($vacancy['state']) ?></strong>
                    </div>
                    <div>
                        <span class="match-label">Reputação</span>
                        <strong style="display:block"><?= e($vacancy['rating_avg']) ?>/5</strong>
                    </div>
                </div>
            </article>
        </section>

        <aside class="sticky">
            <div class="card" style="margin-bottom:16px">
                <div style="text-align:center;margin-bottom:18px">
                    <div style="font-size:30px;font-weight:900"><?= e(brl($vacancy['scholarship'])) ?></div>
                    <p class="muted" style="margin:2px 0 0"><?= e($vacancy['workload'] ?: 'Carga horária a combinar') ?></p>
                </div>

                <?php if (!App\Core\Auth::check()): ?>
                    <a class="btn btn-primary btn-lg" style="width:100%" href="<?= e(route_url('login')) ?>">Entrar para candidatar</a>
                <?php elseif (!App\Core\Auth::isStudent()): ?>
                    <button class="btn btn-outline btn-lg" style="width:100%" disabled>Conta de empresa não candidata</button>
                <?php elseif ($hasApplied): ?>
                    <button class="btn btn-outline btn-lg" style="width:100%" disabled>Candidatura enviada</button>
                <?php else: ?>
                    <form method="post" action="<?= e(action_url()) ?>">
                        <?= csrf_field() ?>
                        <input type="hidden" name="acao" value="candidatar">
                        <input type="hidden" name="vaga_id" value="<?= e($vacancy['id']) ?>">
                        <div class="form-group">
                            <label class="label" for="carta">Carta de apresentação (opcional)</label>
                            <textarea class="textarea" id="carta" name="carta" maxlength="2000" placeholder="Conte rapidamente por que essa vaga combina com você."></textarea>
                        </div>
                        <button class="btn btn-primary btn-lg" style="width:100%" type="submit">Candidatar-se agora</button>
                    </form>
                <?php endif; ?>
            </div>

            <div class="card card-sm">
                <strong>Detalhes da vaga</strong>
                <div class="divider"></div>
                <p><span class="muted">Modalidade:</span> <strong><?= e(modality_label($vacancy['modality'])) ?></strong></p>
                <p><span class="muted">Período:</span> <strong><?= e($vacancy['period'] ?: 'A combinar') ?></strong></p>
                <p><span class="muted">Duração:</span> <strong><?= e($vacancy['duration_months'] ?: '-') ?> meses</strong></p>
                <p><span class="muted">Início:</span> <strong><?= e($vacancy['start_date_label'] ?: 'A combinar') ?></strong></p>
                <p><span class="muted">Vale-transporte:</span> <strong><?= $vacancy['transport_included'] ? 'Incluso' : 'Não informado' ?></strong></p>
                <p><span class="muted">Local:</span> <strong><?= e($vacancy['address'] ?: $vacancy['city']) ?></strong></p>
            </div>
        </aside>
    </div>
</main>

