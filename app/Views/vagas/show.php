<?php
$title = $vacancy['title'] . ' - EstágioMatch';
$active = 'vagas';
require BASE_PATH . '/app/Views/partials/navbar.php';
?>

<main class="page-wrap" id="conteudo">
    <nav class="breadcrumb" aria-label="Você está em">
        <a href="<?= e(route_url('vagas')) ?>">Vagas</a>
        <span aria-hidden="true">/</span>
        <span><?= e($vacancy['title']) ?></span>
    </nav>

    <div class="layout-2">
        <section>
            <article class="card mb-5">
                <div class="card-row flex-start gap-16 mb-5">
                    <span class="co-logo co-logo-lg" aria-hidden="true"><?= e($vacancy['logo_initials']) ?></span>
                    <div class="flex-1">
                        <h1 class="mb-1 m-0"><?= e($vacancy['title']) ?></h1>
                        <p class="muted m-0"><?= e($vacancy['trade_name']) ?> · <?= e($vacancy['city']) ?>, <?= e($vacancy['state']) ?></p>
                        <div class="chips mt-3">
                            <span class="badge badge-blue"><?= e(modality_label($vacancy['modality'])) ?></span>
                            <span class="badge badge-green"><?= e($vacancy['published_at'] ? 'Publicada em ' . date('d/m/Y', strtotime($vacancy['published_at'])) : 'Publicada recentemente') ?></span>
                            <span class="badge badge-gray"><?= e($vacancy['area']) ?></span>
                        </div>
                    </div>
                    <button class="save-btn" type="button" data-save-vacancy="<?= e($vacancy['id']) ?>" aria-pressed="false" aria-label="Salvar vaga">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/></svg>
                    </button>
                </div>

                <div class="card card-accent shadow-none">
                    <div class="card-row gap-18">
                        <div class="text-center">
                            <div class="match-pct xxl"><?= e($vacancy['match_score']) ?>%</div>
                            <div class="match-label">compatível</div>
                        </div>
                        <div>
                            <strong>Por que essa vaga foi recomendada:</strong>
                            <ul class="mt-2 pl-18">
                                <?php foreach ($vacancy['match_reasons'] as $reason): ?>
                                    <li><?= e($reason) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </article>

            <article class="card mb-5">
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

            <article class="card mb-5">
                <h2>Avaliação da empresa</h2>
                <div class="card-row flex-start gap-26">
                    <div class="text-center">
                        <div class="big-number xl"><?= e($rating['avg_score']) ?></div>
                        <div class="muted"><?= e($rating['total']) ?> avaliações</div>
                    </div>
                    <div class="flex-1 stack-10">
                        <?php foreach (['environment' => 'Ambiente', 'learning' => 'Aprendizado', 'mentorship' => 'Mentoria'] as $key => $label): ?>
                            <?php $pct = ((float) $rating[$key] / 5) * 100; ?>
                            <div>
                                <div class="section-hd mb-1">
                                    <span><?= e($label) ?></span>
                                    <strong><?= e($rating[$key]) ?></strong>
                                </div>
                                <div class="eval-bar" role="progressbar" aria-label="<?= e($label) ?>"
                                     aria-valuenow="<?= e($rating[$key]) ?>" aria-valuemin="0" aria-valuemax="5">
                                    <div class="eval-fill" data-fill="<?= e($pct) ?>"></div>
                                </div>
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
                        <strong class="block"><?= e($vacancy['sector'] ?? 'Não informado') ?></strong>
                    </div>
                    <div>
                        <span class="match-label">Cidade</span>
                        <strong class="block"><?= e($vacancy['city']) ?>, <?= e($vacancy['state']) ?></strong>
                    </div>
                    <div>
                        <span class="match-label">Reputação</span>
                        <strong class="block"><?= e($vacancy['rating_avg']) ?>/5</strong>
                    </div>
                </div>
            </article>
        </section>

        <aside class="sticky">
            <div class="card mb-4">
                <div class="text-center mb-5">
                    <div class="big-number fs-30"><?= e(brl($vacancy['scholarship'])) ?></div>
                    <p class="muted mt-1"><?= e($vacancy['workload'] ?: 'Carga horária a combinar') ?></p>
                </div>

                <?php if (!App\Core\Auth::check()): ?>
                    <a class="btn btn-primary btn-lg w-full" href="<?= e(route_url('login')) ?>">Entrar para candidatar</a>
                <?php elseif (!App\Core\Auth::isStudent()): ?>
                    <button class="btn btn-outline btn-lg w-full" disabled>Conta de empresa não candidata</button>
                <?php elseif ($hasApplied): ?>
                    <button class="btn btn-outline btn-lg w-full" disabled>Candidatura enviada</button>
                <?php else: ?>
                    <form method="post" action="<?= e(action_url()) ?>">
                        <?= csrf_field() ?>
                        <input type="hidden" name="acao" value="candidatar">
                        <input type="hidden" name="vaga_id" value="<?= e($vacancy['id']) ?>">
                        <div class="form-group">
                            <label class="label" for="carta">Carta de apresentação (opcional)</label>
                            <textarea class="textarea" id="carta" name="carta" maxlength="2000" placeholder="Conte rapidamente por que essa vaga combina com você."></textarea>
                        </div>
                        <button class="btn btn-primary btn-lg w-full" type="submit"><?= icon('rocket') ?> Candidatar-se agora</button>
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
