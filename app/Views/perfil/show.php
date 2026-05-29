<?php
$title = 'Meu Perfil - EstágioMatch';
$active = 'perfil';
$skillCsv = implode(', ', array_map(static fn (array $skill): string => $skill['skill'], $skills));
require BASE_PATH . '/app/Views/partials/navbar.php';
?>

<main class="page-wrap">
    <div class="profile-grid">
        <aside>
            <section class="card" style="text-align:center;margin-bottom:16px">
                <span class="avatar" style="height:84px;width:84px;font-size:28px;margin-bottom:14px"><?= e((new App\Models\User())->initials($user)) ?></span>
                <h1 style="font-size:22px;margin:0 0 4px"><?= e($user['name']) ?></h1>
                <p class="muted" style="margin:0 0 12px"><?= e($profile['course'] ?? 'Curso não informado') ?> · <?= e($profile['current_period'] ?? '-') ?>º período</p>
                <div class="chips" style="justify-content:center">
                    <span class="badge badge-gray"><?= e($profile['institution'] ?? 'Instituição') ?></span>
                    <span class="badge badge-gray"><?= e($profile['city'] ?? 'Cidade') ?>, <?= e($profile['state'] ?? '') ?></span>
                </div>
                <div class="divider"></div>
                <div class="score-ring" style="--score:<?= e($profile['profile_completeness'] ?? 25) ?>%"><span><?= e($profile['profile_completeness'] ?? 25) ?>%</span></div>
                <p class="muted">Completude do perfil</p>
            </section>

            <section class="card" style="margin-bottom:16px;text-align:center">
                <strong>Reputação</strong>
                <div style="font-size:34px;font-weight:900;margin-top:8px"><?= e($studentRating ?: '0.0') ?></div>
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

        <section>
            <form class="card" method="post" action="<?= e(action_url()) ?>" style="margin-bottom:20px">
                <?= csrf_field() ?>
                <input type="hidden" name="acao" value="perfil_atualizar">

                <div class="section-hd">
                    <div>
                        <h2 style="margin:0">Dados acadêmicos e preferências</h2>
                        <p class="muted" style="margin:4px 0 0">Esses dados alimentam o algoritmo de recomendação.</p>
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
                        <input class="input" id="state" name="state" maxlength="2" value="<?= e($profile['state'] ?? 'SP') ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="label" for="neighborhood">Bairro</label>
                        <input class="input" id="neighborhood" name="neighborhood" value="<?= e($profile['neighborhood'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label class="label" for="cep">CEP</label>
                        <input class="input" id="cep" name="cep" value="<?= e($profile['cep'] ?? '') ?>">
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
                        <input class="input" id="portfolio_url" type="url" name="portfolio_url" value="<?= e($profile['portfolio_url'] ?? '') ?>">
                    </div>
                    <label class="inline" style="gap:8px;margin-top:28px">
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
                    <p class="muted">Nenhuma candidatura enviada ainda.</p>
                <?php else: ?>
                    <div class="table-list">
                        <?php foreach ($applications as $application): ?>
                            <div class="table-row">
                                <span class="co-logo"><?= e($application['logo_initials']) ?></span>
                                <div>
                                    <strong><?= e($application['title']) ?></strong>
                                    <p class="muted" style="margin:2px 0 0"><?= e($application['trade_name']) ?> · match <?= e($application['match_score']) ?>%</p>
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

