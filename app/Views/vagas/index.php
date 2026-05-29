<?php
$title = 'Vagas - EstágioMatch';
$active = 'vagas';
require BASE_PATH . '/app/Views/partials/navbar.php';
?>

<main class="page-wrap">
    <div style="margin-bottom:22px">
        <h1 style="margin:0">Explorar Vagas</h1>
        <p class="muted" style="margin:4px 0 0"><?= e(count($vacancies)) ?> vagas disponíveis · ordenadas por compatibilidade</p>
    </div>

    <form class="search-box" method="get" action="<?= e(base_url('controlador.php')) ?>">
        <input type="hidden" name="rota" value="vagas">
        <div class="search-line">
            <input class="input" name="q" value="<?= e($filters['q']) ?>" placeholder="Buscar por cargo, empresa ou tecnologia">
            <select class="input" name="area">
                <option value="">Todas as áreas</option>
                <option value="Tecnologia" <?= selected($filters['area'], 'Tecnologia') ?>>Tecnologia</option>
                <option value="Dados & BI" <?= selected($filters['area'], 'Dados & BI') ?>>Dados & BI</option>
                <option value="Design" <?= selected($filters['area'], 'Design') ?>>Design</option>
                <option value="Administração" <?= selected($filters['area'], 'Administração') ?>>Administração</option>
            </select>
            <select class="input" name="modality">
                <option value="">Modalidade</option>
                <option value="presencial" <?= selected($filters['modality'], 'presencial') ?>>Presencial</option>
                <option value="hibrido" <?= selected($filters['modality'], 'hibrido') ?>>Híbrido</option>
                <option value="remoto" <?= selected($filters['modality'], 'remoto') ?>>Remoto</option>
            </select>
            <button class="btn btn-primary" type="submit">Buscar</button>
        </div>
        <div class="chips" style="margin-top:14px">
            <span class="chip active">Compatibilidade alta</span>
            <span class="chip">Candidatura rápida</span>
            <span class="chip">LGPD e consentimento</span>
            <span class="chip">Avaliação bidirecional</span>
        </div>
    </form>

    <div class="layout-sidebar">
        <aside class="sidebar">
            <h3>Filtros ativos</h3>
            <label><input type="checkbox" checked disabled> Vagas ativas</label>
            <label><input type="checkbox" checked disabled> Empresas verificadas</label>
            <label><input type="checkbox" checked disabled> Ordenação por match</label>
            <div class="divider"></div>
            <h3>Segurança</h3>
            <p class="muted" style="font-size:13px">Candidaturas são registradas com sessão autenticada e token CSRF.</p>
        </aside>

        <section>
            <div style="display:grid;gap:12px">
                <?php if ($vacancies === []): ?>
                    <div class="card">
                        <strong>Nenhuma vaga encontrada</strong>
                        <p class="muted">Tente remover filtros ou buscar por outra área.</p>
                    </div>
                <?php endif; ?>

                <?php foreach ($vacancies as $vacancy): ?>
                    <article class="vacancy-card <?= $vacancy['is_boosted'] ? 'featured' : '' ?>" data-card-link="<?= e(route_url('vaga', ['id' => $vacancy['id']])) ?>">
                        <div class="card-row" style="align-items:flex-start;gap:14px">
                            <span class="co-logo"><?= e($vacancy['logo_initials']) ?></span>
                            <div style="flex:1;min-width:0">
                                <div class="section-hd" style="margin-bottom:8px">
                                    <div>
                                        <h2 style="font-size:17px;margin:0"><?= e($vacancy['title']) ?></h2>
                                        <p class="muted" style="margin:2px 0 0"><?= e($vacancy['trade_name']) ?> · <?= e($vacancy['city']) ?>, <?= e($vacancy['state']) ?></p>
                                    </div>
                                    <div style="text-align:right">
                                        <div class="match-pct" style="font-size:22px"><?= e($vacancy['match_score']) ?>%</div>
                                        <div class="match-label">compatível</div>
                                    </div>
                                </div>
                                <div class="match-bar"><div class="match-fill" style="width:<?= e($vacancy['match_score']) ?>%"></div></div>
                                <div class="chips" style="margin-top:12px">
                                    <?php foreach (array_slice($vacancy['skills'], 0, 4) as $skill): ?>
                                        <span class="badge badge-blue"><?= e($skill['skill']) ?></span>
                                    <?php endforeach; ?>
                                    <span class="badge badge-green"><?= e(modality_label($vacancy['modality'])) ?></span>
                                    <span class="badge badge-gray"><?= e(brl($vacancy['scholarship'])) ?></span>
                                </div>
                                <p class="why-tag" style="margin:12px 0 0"><?= e($vacancy['match_reasons'][0]) ?></p>
                            </div>
                            <a class="btn btn-primary" href="<?= e(route_url('vaga', ['id' => $vacancy['id']])) ?>">Ver vaga</a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>
    </div>
</main>

