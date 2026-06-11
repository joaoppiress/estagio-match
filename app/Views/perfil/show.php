<?php
$title = 'Meu Perfil - EstágioMatch';
$active = 'perfil';
$skillCsv = implode(', ', array_map(static fn (array $skill): string => $skill['skill'], $skills));
$completeness = (int) ($profile['profile_completeness'] ?? 25);
require BASE_PATH . '/app/Views/partials/navbar.php';
?>

<main class="page-wrap" id="conteudo">
    <div class="profile-grid">
        <aside data-reveal>
            <section class="card text-center mb-4">
                <span class="avatar avatar-xl"><?= e((new App\Models\User())->initials($user)) ?></span>
                <h1 class="fs-22 mb-1 mt-3"><?= e($user['name']) ?></h1>
                <p class="muted mb-3"><?= e($profile['course'] ?? 'Curso não informado') ?> · <?= e($profile['current_period'] ?? '-') ?>º período</p>
                <div class="chips chips-center">
                    <span class="badge badge-gray"><?= e($profile['institution'] ?? 'Instituição') ?></span>
                    <span class="badge badge-gray"><?= e($profile['city'] ?? 'Cidade') ?>, <?= e($profile['state'] ?? '') ?></span>
                </div>
                <div class="divider"></div>
                <div class="score-ring" style="--score:<?= e($completeness) ?>%" role="progressbar"
                     aria-label="Completude do perfil" aria-valuenow="<?= e($completeness) ?>" aria-valuemin="0" aria-valuemax="100">
                    <span><?= e($completeness) ?>%</span>
                </div>
                <p class="muted">Completude do perfil</p>
            </section>

            <section class="card text-center mb-4">
                <strong>Reputação</strong>
                <div class="big-number mt-2"><?= e($studentRating ?: '0.0') ?></div>
                <p class="muted">Avaliação média recebida de empresas</p>
            </section>

            <section class="card">
                <strong>Preferências</strong>
                <div class="divider"></div>
                <p><span class="muted">Modalidade:</span> <strong><?= e(modality_label($profile['preferred_modality'] ?? 'qualquer')) ?></strong></p>
                <p><span class="muted">Raio máximo:</span> <strong><?= e($profile['max_distance_km'] ?? 15) ?> km</strong></p>
                <p><span class="muted">Bolsa mínima:</span> <strong><?= e(brl($profile['min_scholarship'] ?? 800)) ?></strong></p>
            </section>
        </aside>

        <section data-reveal style="--reveal-delay:90ms">
            <form class="card mb-5" method="post" action="<?= e(action_url()) ?>">
                <?= csrf_field() ?>
                <input type="hidden" name="acao" value="perfil_atualizar">

                <div class="section-hd">
                    <div>
                        <h2 class="m-0">Dados acadêmicos e preferências</h2>
                        <p class="muted mt-1">Esses dados alimentam o algoritmo de recomendação.</p>
                    </div>
                    <button class="btn btn-primary" type="submit">Salvar perfil</button>
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label class="label" for="course">Curso</label>
                        <input class="input" id="course" name="course" value="<?= e($profile['course'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="label" for="institution">Instituição</label>
                        <input class="input" id="institution" name="institution" value="<?= e($profile['institution'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label class="label" for="current_period">Período atual</label>
                        <input class="input" id="current_period" type="number" min="1" max="12" name="current_period" value="<?= e($profile['current_period'] ?? 1) ?>">
                    </div>
                    <div class="form-group">
                        <label class="label" for="graduation_forecast">Previsão de formatura</label>
                        <input class="input" id="graduation_forecast" name="graduation_forecast" value="<?= e($profile['graduation_forecast'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label class="label" for="performance_index">CR / média</label>
                        <input class="input" id="performance_index" type="number" min="0" max="10" step="0.1" name="performance_index" value="<?= e($profile['performance_index'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label class="label" for="interests">Áreas de interesse</label>
                        <input class="input" id="interests" name="interests" value="<?= e($profile['interests'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label class="label" for="city">Cidade</label>
                        <input class="input" id="city" name="city" value="<?= e($profile['city'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="label" for="state">Estado</label>
                        <select class="input" id="state" name="state" required>
                            <?= uf_options($profile['state'] ?? 'SP') ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="label" for="neighborhood">Bairro</label>
                        <input class="input" id="neighborhood" name="neighborhood" value="<?= e($profile['neighborhood'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label class="label" for="cep">CEP</label>
                        <input class="input" id="cep" name="cep" value="<?= e($profile['cep'] ?? '') ?>" inputmode="numeric" placeholder="00000-000">
                    </div>
                    <div class="form-group">
                        <label class="label" for="availability">Disponibilidade</label>
                        <input class="input" id="availability" name="availability" value="<?= e($profile['availability'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label class="label" for="preferred_modality">Modalidade preferida</label>
                        <select class="input" id="preferred_modality" name="preferred_modality">
                            <option value="qualquer" <?= selected($profile['preferred_modality'] ?? '', 'qualquer') ?>>Qualquer</option>
                            <option value="presencial" <?= selected($profile['preferred_modality'] ?? '', 'presencial') ?>>Presencial</option>
                            <option value="hibrido" <?= selected($profile['preferred_modality'] ?? '', 'hibrido') ?>>Híbrido</option>
                            <option value="remoto" <?= selected($profile['preferred_modality'] ?? '', 'remoto') ?>>Remoto</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="label" for="max_distance_km">Raio máximo (km)</label>
                        <input class="input" id="max_distance_km" type="number" min="1" max="100" name="max_distance_km" value="<?= e($profile['max_distance_km'] ?? 15) ?>">
                    </div>
                    <div class="form-group">
                        <label class="label" for="min_scholarship">Bolsa mínima</label>
                        <input class="input" id="min_scholarship" type="number" min="0" step="50" name="min_scholarship" value="<?= e($profile['min_scholarship'] ?? 800) ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label class="label" for="skills">Habilidades (separadas por vírgula)</label>
                    <input class="input" id="skills" name="skills" value="<?= e($skillCsv) ?>" placeholder="React.js, Python, SQL">
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label class="label" for="portfolio_url">Portfólio</label>
                        <input class="input" id="portfolio_url" type="url" name="portfolio_url" value="<?= e($profile['portfolio_url'] ?? '') ?>" placeholder="https://...">
                    </div>
                    <label class="checkbox-row mt-7">
                        <input type="checkbox" name="accessibility_libras" value="1" <?= checked(!empty($profile['accessibility_libras'])) ?>>
                        <span>Desejo recursos de acessibilidade/Libras</span>
                    </label>
                </div>

                <div class="form-group">
                    <label class="label" for="bio">Resumo profissional</label>
                    <textarea class="textarea" id="bio" name="bio"><?= e($profile['bio'] ?? '') ?></textarea>
                </div>
            </form>

            <section class="card">
                <div class="section-hd">
                    <div class="section-title">Minhas candidaturas</div>
                </div>
                <?php if ($applications === []): ?>
                    <div class="empty-block">
                        <div class="empty-icon" aria-hidden="true">📋</div>
                        <strong>Nenhuma candidatura enviada ainda</strong>
                        <p class="muted mb-4">Que tal explorar as vagas recomendadas para o seu perfil?</p>
                        <a class="btn btn-primary" href="<?= e(route_url('vagas')) ?>">Ver vagas</a>
                    </div>
                <?php else: ?>
                    <div class="table-list">
                        <?php foreach ($applications as $application): ?>
                            <div class="table-row">
                                <span class="co-logo"><?= e($application['logo_initials']) ?></span>
                                <div>
                                    <strong><?= e($application['title']) ?></strong>
                                    <p class="muted mt-1"><?= e($application['trade_name']) ?> · match <?= e($application['match_score']) ?>%</p>
                                </div>
                                <span class="badge badge-blue"><?= e(status_label($application['status'])) ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </section>
        </section>
    </div>
</main>
