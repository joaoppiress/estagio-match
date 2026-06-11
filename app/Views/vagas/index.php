<?php
$title = 'Vagas - EstágioMatch';
$active = 'vagas';
require BASE_PATH . '/app/Views/partials/navbar.php';

// Maior bolsa entre as vagas — limite superior do filtro de faixa.
$maxScholarship = 0;
foreach ($vacancies as $v) {
    $maxScholarship = max($maxScholarship, (int) ($v['scholarship'] ?? 0));
}
$maxScholarship = max(2000, (int) ceil($maxScholarship / 100) * 100);
?>

<main class="page-wrap" id="conteudo">
    <div class="mb-6" data-reveal>
        <h1 class="m-0">Explorar Vagas</h1>
        <p class="muted mt-1"><span class="results-count" data-result-count><?= e(count($vacancies)) ?></span> vagas encontradas · ordenadas por compatibilidade</p>
    </div>

    <form class="search-box" method="get" action="<?= e(base_url('controlador.php')) ?>" role="search" aria-label="Buscar vagas" data-reveal>
        <input type="hidden" name="rota" value="vagas">
        <div class="search-line">
            <input class="input" name="q" value="<?= e($filters['q']) ?>" aria-label="Termo de busca" placeholder="Buscar por cargo, empresa ou tecnologia">
            <select class="input" name="area" aria-label="Área">
                <option value="">Todas as áreas</option>
                <option value="Tecnologia" <?= selected($filters['area'], 'Tecnologia') ?>>Tecnologia</option>
                <option value="Dados & BI" <?= selected($filters['area'], 'Dados & BI') ?>>Dados & BI</option>
                <option value="Design" <?= selected($filters['area'], 'Design') ?>>Design</option>
                <option value="Administração" <?= selected($filters['area'], 'Administração') ?>>Administração</option>
            </select>
            <select class="input" name="modality" aria-label="Modalidade">
                <option value="">Modalidade</option>
                <option value="presencial" <?= selected($filters['modality'], 'presencial') ?>>Presencial</option>
                <option value="hibrido" <?= selected($filters['modality'], 'hibrido') ?>>Híbrido</option>
                <option value="remoto" <?= selected($filters['modality'], 'remoto') ?>>Remoto</option>
            </select>
            <button class="btn btn-primary" type="submit"><?= icon('search') ?> Buscar</button>
        </div>
        <div class="chips mt-3">
            <span class="chip active">Compatibilidade alta</span>
            <span class="chip">Candidatura rápida</span>
            <span class="chip">LGPD e consentimento</span>
            <span class="chip">Avaliação bidirecional</span>
        </div>
    </form>

    <div class="layout-sidebar">
        <aside class="sidebar" aria-label="Refinar resultados">
            <h3>Ordenar por</h3>
            <select class="input" data-sort aria-label="Ordenar resultados">
                <option value="match">Maior compatibilidade</option>
                <option value="scholarship">Maior bolsa</option>
                <option value="recent">Mais recentes</option>
            </select>

            <div class="divider"></div>

            <h3>Bolsa mínima</h3>
            <input class="filter-range" type="range" min="0" max="<?= e($maxScholarship) ?>" step="100" value="0"
                   data-filter="scholarship" aria-label="Bolsa mínima">
            <p class="text-sm muted m-0">A partir de <strong data-filter-out="scholarship">R$ 0</strong></p>

            <div class="divider"></div>

            <h3>Modalidade</h3>
            <label><input type="checkbox" value="presencial" data-filter="modality"> Presencial</label>
            <label><input type="checkbox" value="hibrido" data-filter="modality"> Híbrido</label>
            <label><input type="checkbox" value="remoto" data-filter="modality"> Remoto</label>

            <div class="divider"></div>

            <label><input type="checkbox" data-filter="saved"> Apenas vagas salvas</label>

            <div class="divider"></div>
            <h3>Segurança</h3>
            <p class="muted text-sm m-0">Candidaturas são registradas com sessão autenticada e token CSRF.</p>
        </aside>

        <section aria-label="Resultados de vagas">
            <div class="stack" data-vacancy-list>
                <div class="card empty-block" data-vacancy-empty hidden>
                    <div class="empty-icon" aria-hidden="true">🔍</div>
                    <strong>Nenhuma vaga corresponde aos filtros</strong>
                    <p class="muted m-0">Ajuste a faixa de bolsa, a modalidade ou limpe os filtros.</p>
                </div>

                <?php if ($vacancies === []): ?>
                    <div class="card empty-block">
                        <div class="empty-icon" aria-hidden="true">🔍</div>
                        <strong>Nenhuma vaga encontrada</strong>
                        <p class="muted m-0">Tente remover filtros ou buscar por outra área.</p>
                    </div>
                <?php endif; ?>

                <?php foreach ($vacancies as $i => $vacancy): ?>
                    <article class="vacancy-card <?= $vacancy['is_boosted'] ? 'featured' : '' ?>"
                             data-vacancy="<?= e($vacancy['id']) ?>"
                             data-card-link="<?= e(route_url('vaga', ['id' => $vacancy['id']])) ?>"
                             data-scholarship="<?= e((int) $vacancy['scholarship']) ?>"
                             data-modality="<?= e($vacancy['modality']) ?>"
                             data-match="<?= e($vacancy['match_score']) ?>"
                             data-order="<?= e($i) ?>">
                        <div class="card-row flex-start gap-14">
                            <span class="co-logo" aria-hidden="true"><?= e($vacancy['logo_initials']) ?></span>
                            <div class="flex-1">
                                <div class="section-hd mb-2">
                                    <div>
                                        <h2 class="fs-17 m-0"><?= e($vacancy['title']) ?></h2>
                                        <p class="muted mt-1"><?= e($vacancy['trade_name']) ?> · <?= e($vacancy['city']) ?>, <?= e($vacancy['state']) ?></p>
                                    </div>
                                    <div class="text-right">
                                        <div class="match-pct lg"><?= e($vacancy['match_score']) ?>%</div>
                                        <div class="match-label">compatível</div>
                                    </div>
                                </div>
                                <div class="match-bar" role="progressbar" aria-label="Compatibilidade com seu perfil"
                                     aria-valuenow="<?= e($vacancy['match_score']) ?>" aria-valuemin="0" aria-valuemax="100">
                                    <div class="match-fill" data-fill="<?= e($vacancy['match_score']) ?>"></div>
                                </div>
                                <div class="chips mt-3">
                                    <?php foreach (array_slice($vacancy['skills'], 0, 4) as $skill): ?>
                                        <span class="badge badge-blue"><?= e($skill['skill']) ?></span>
                                    <?php endforeach; ?>
                                    <span class="badge badge-green"><?= e(modality_label($vacancy['modality'])) ?></span>
                                    <span class="badge badge-gray"><?= e(brl($vacancy['scholarship'])) ?></span>
                                </div>
                                <p class="why-tag mt-3"><?= e($vacancy['match_reasons'][0]) ?></p>
                            </div>
                            <div class="stack-10">
                                <button class="save-btn" type="button" data-save-vacancy="<?= e($vacancy['id']) ?>" aria-pressed="false" aria-label="Salvar vaga">
                                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/></svg>
                                </button>
                                <a class="btn btn-primary" href="<?= e(route_url('vaga', ['id' => $vacancy['id']])) ?>">Ver vaga</a>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>

            <div class="text-center mt-4">
                <button class="btn btn-outline" type="button" data-load-more hidden>Carregar mais vagas</button>
            </div>
        </section>
    </div>
</main>
