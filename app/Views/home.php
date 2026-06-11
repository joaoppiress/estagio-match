<?php
$title = 'EstágioMatch - Plataforma inteligente de estágios';
$active = 'home';
$totalVacancies = $totalVacancies ?? count($featured);
$heroFeatured = array_slice($featured, 0, 3);
require BASE_PATH . '/app/Views/partials/navbar.php';
?>

<section class="hero" aria-labelledby="hero-title">
    <div class="hero-inner">
        <div data-reveal>
            <span class="eyebrow"><span class="pulse-dot"></span> Recomendação inteligente por perfil e localização</span>
            <h1 id="hero-title">Seu próximo estágio,<br><span class="gradient-text">onde você merece estar.</span></h1>
            <p>
                Plataforma que conecta estudantes e empresas com candidatura em menos de 2 minutos,
                reputação bidirecional, acessibilidade nativa e matching baseado em dados reais.
            </p>
            <div class="inline-gap">
                <a class="btn btn-primary btn-lg" href="<?= e(route_url('login')) ?>#cadastro"><?= icon('rocket') ?> Criar conta gratuita</a>
                <a class="btn btn-outline btn-lg" href="<?= e(route_url('vagas')) ?>"><?= icon('search') ?> Explorar vagas</a>
            </div>
            <div class="hero-list">
                <span><?= icon('check') ?> Match por curso, habilidades, preferência e cidade</span>
                <span><?= icon('check') ?> Candidatura em menos de 2 minutos</span>
                <span><?= icon('check') ?> Avaliações transparentes entre estudantes e empresas</span>
            </div>
        </div>

        <div class="hero-panel" data-reveal style="--reveal-delay:120ms">
            <div class="card">
                <div class="section-hd">
                    <div>
                        <div class="section-title">Vagas em destaque</div>
                        <p class="muted mt-1">Atualizado em tempo real do banco</p>
                    </div>
                    <span class="badge badge-green"><?= icon('bolt') ?> ao vivo</span>
                </div>
                <div class="stack">
                    <?php foreach ($heroFeatured as $vacancy): ?>
                        <a class="vacancy-card" href="<?= e(route_url('vaga', ['id' => $vacancy['id']])) ?>">
                            <div class="card-row flex-between mb-3">
                                <div class="inline-gap">
                                    <span class="co-logo" aria-hidden="true"><?= e($vacancy['logo_initials']) ?></span>
                                    <div>
                                        <strong><?= e($vacancy['title']) ?></strong>
                                        <p class="muted mt-1"><?= e($vacancy['trade_name']) ?> · <?= e($vacancy['city']) ?></p>
                                    </div>
                                </div>
                                <span class="match-pct"><?= e($vacancy['match_score']) ?>%</span>
                            </div>
                            <div class="match-bar" role="progressbar" aria-label="Compatibilidade com seu perfil"
                                 aria-valuenow="<?= e($vacancy['match_score']) ?>" aria-valuemin="0" aria-valuemax="100">
                                <div class="match-fill" data-fill="<?= e($vacancy['match_score']) ?>"></div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<main id="conteudo">
    <!-- Stats band -->
    <section class="section-narrow stats-section">
        <div class="stats-band">
            <div class="stat-big" data-reveal>
                <div class="num"><span data-counter="<?= e(max(1, $totalVacancies)) ?>">0</span></div>
                <div class="lbl">Vagas ativas agora</div>
            </div>
            <div class="stat-big" data-reveal style="--reveal-delay:80ms">
                <div class="num"><span data-counter="2" data-prefix="< " data-suffix=" min">0</span></div>
                <div class="lbl">Para se candidatar</div>
            </div>
            <div class="stat-big" data-reveal style="--reveal-delay:160ms">
                <div class="num"><span data-counter="98" data-suffix="%">0</span></div>
                <div class="lbl">Precisão do match</div>
            </div>
            <div class="stat-big" data-reveal style="--reveal-delay:240ms">
                <div class="num"><span data-counter="100" data-suffix="%">0</span></div>
                <div class="lbl">Acessível e web</div>
            </div>
        </div>
    </section>

    <!-- Features -->
    <section class="section section-narrow" aria-labelledby="features-title">
        <div class="section-head" data-reveal>
            <span class="eyebrow">Por que EstágioMatch</span>
            <h2 id="features-title">Tecnologia que aproxima talento e oportunidade</h2>
            <p>Cada recurso foi pensado para reduzir fricção, aumentar a confiança e incluir todo mundo.</p>
        </div>
        <div class="feature-grid">
            <article class="feature-card" data-reveal>
                <span class="feature-icon"><?= icon('bolt') ?></span>
                <h3>Match inteligente</h3>
                <p>Algoritmo cruza curso, habilidades, preferências e cidade para priorizar as vagas mais compatíveis com você.</p>
            </article>
            <article class="feature-card" data-reveal style="--reveal-delay:80ms">
                <span class="feature-icon green"><?= icon('star') ?></span>
                <h3>Reputação bidirecional</h3>
                <p>Estudantes avaliam empresas e vice-versa. Aprendizado, mentoria e ambiente em notas transparentes.</p>
            </article>
            <article class="feature-card" data-reveal style="--reveal-delay:160ms">
                <span class="feature-icon violet"><?= icon('accessibility') ?></span>
                <h3>Acessibilidade nativa</h3>
                <p>VLibras, alto contraste, ajuste de fonte e navegação por teclado — inclusão como padrão, não exceção.</p>
            </article>
            <article class="feature-card" data-reveal>
                <span class="feature-icon orange"><?= icon('clock') ?></span>
                <h3>Candidatura em 2 minutos</h3>
                <p>Fluxo enxuto com carta opcional. Menos formulário, mais oportunidade — direto do dashboard.</p>
            </article>
            <article class="feature-card" data-reveal style="--reveal-delay:80ms">
                <span class="feature-icon"><?= icon('shield') ?></span>
                <h3>Segurança de verdade</h3>
                <p>Sessão reforçada, proteção CSRF, hash Argon2id e coleta mínima de dados alinhada à LGPD.</p>
            </article>
            <article class="feature-card" data-reveal style="--reveal-delay:160ms">
                <span class="feature-icon green"><?= icon('map-pin') ?></span>
                <h3>Recomendação por proximidade</h3>
                <p>Prioriza vagas perto de você, respeitando o raio máximo e a modalidade que você prefere.</p>
            </article>
        </div>
    </section>

    <!-- How it works -->
    <section class="section section-alt" aria-labelledby="how-title">
        <div class="section-narrow">
            <div class="section-head" data-reveal>
                <span class="eyebrow">Como funciona</span>
                <h2 id="how-title">Do cadastro à contratação em 4 passos</h2>
                <p>Simples para o estudante, poderoso para a empresa. Tudo 100% na web, em qualquer dispositivo.</p>
            </div>
            <div class="steps">
                <div class="step" data-reveal>
                    <div class="step-num">1</div>
                    <h3>Crie seu perfil</h3>
                    <p>Dados acadêmicos, habilidades, cidade, disponibilidade e preferências profissionais.</p>
                </div>
                <div class="step" data-reveal style="--reveal-delay:90ms">
                    <div class="step-num">2</div>
                    <h3>Receba recomendações</h3>
                    <p>O sistema cruza perfil, habilidades e localização para priorizar vagas compatíveis.</p>
                </div>
                <div class="step" data-reveal style="--reveal-delay:180ms">
                    <div class="step-num">3</div>
                    <h3>Candidate-se rápido</h3>
                    <p>Candidatura com sessão segura e registro no histórico do estudante, em poucos cliques.</p>
                </div>
                <div class="step" data-reveal style="--reveal-delay:270ms">
                    <div class="step-num">4</div>
                    <h3>Avalie e seja avaliado</h3>
                    <p>Após o processo, a reputação bidirecional fortalece a confiança da próxima conexão.</p>
                </div>
            </div>

            <div class="grid-2 mt-7 gap-20">
                <div class="card" data-reveal>
                    <span class="badge badge-blue"><?= icon('graduation') ?> Para estudantes</span>
                    <ul class="stack-10 mt-3 bullets">
                        <li>Veja só o que combina com você — sem rolar listas infinitas.</li>
                        <li>Entenda <strong>por que</strong> cada vaga foi recomendada.</li>
                        <li>Acompanhe o status de cada candidatura em um só lugar.</li>
                        <li>Salve vagas para decidir depois.</li>
                    </ul>
                </div>
                <div class="card" data-reveal style="--reveal-delay:100ms">
                    <span class="badge badge-green"><?= icon('building') ?> Para empresas</span>
                    <ul class="stack-10 mt-3 bullets">
                        <li>Publique uma vaga e receba candidatos já ranqueados por match.</li>
                        <li>Gerencie candidaturas e reputação no painel.</li>
                        <li>Destaque vagas e ganhe visibilidade.</li>
                        <li>Dados estratégicos sobre quem se candidata.</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Areas -->
    <section class="section section-narrow" aria-labelledby="areas-title">
        <div class="section-head" data-reveal>
            <span class="eyebrow">Áreas em alta</span>
            <h2 id="areas-title">Encontre estágio na sua trilha</h2>
        </div>
        <div class="area-grid">
            <a class="area-card" data-reveal href="<?= e(route_url('vagas', ['area' => 'Tecnologia'])) ?>">
                <span class="feature-icon"><?= icon('bolt') ?></span>
                <strong>Tecnologia</strong>
                <span class="muted">Dev, mobile, QA, infra e mais.</span>
            </a>
            <a class="area-card" data-reveal style="--reveal-delay:70ms" href="<?= e(route_url('vagas', ['area' => 'Dados & BI'])) ?>">
                <span class="feature-icon green"><?= icon('chart') ?></span>
                <strong>Dados &amp; BI</strong>
                <span class="muted">Análise, dashboards e ML.</span>
            </a>
            <a class="area-card" data-reveal style="--reveal-delay:140ms" href="<?= e(route_url('vagas', ['area' => 'Design'])) ?>">
                <span class="feature-icon violet"><?= icon('sparkles') ?></span>
                <strong>Design</strong>
                <span class="muted">UI/UX, produto e branding.</span>
            </a>
            <a class="area-card" data-reveal style="--reveal-delay:210ms" href="<?= e(route_url('vagas', ['area' => 'Administração'])) ?>">
                <span class="feature-icon orange"><?= icon('briefcase') ?></span>
                <strong>Administração</strong>
                <span class="muted">Gestão, financeiro e RH.</span>
            </a>
        </div>
    </section>

    <!-- Featured carousel -->
    <?php if (count($featured) > 0): ?>
        <section class="section section-alt" aria-labelledby="carousel-title">
            <div class="section-narrow">
                <div class="section-hd" data-reveal>
                    <div>
                        <span class="eyebrow">Recém-publicadas</span>
                        <h2 id="carousel-title" class="m-0 mt-2">Vagas em destaque</h2>
                    </div>
                    <a class="btn btn-ghost" href="<?= e(route_url('vagas')) ?>">Ver todas <?= icon('arrow-right') ?></a>
                </div>

                <div class="carousel" data-carousel data-reveal>
                    <div class="carousel-viewport">
                        <div class="carousel-track">
                            <?php foreach ($featured as $vacancy): ?>
                                <div class="carousel-slide">
                                    <article class="vacancy-card <?= $vacancy['is_boosted'] ? 'featured' : '' ?>" data-card-link="<?= e(route_url('vaga', ['id' => $vacancy['id']])) ?>">
                                        <div class="card-row flex-between mb-3">
                                            <span class="co-logo" aria-hidden="true"><?= e($vacancy['logo_initials']) ?></span>
                                            <div class="text-right">
                                                <div class="match-pct"><?= e($vacancy['match_score']) ?>%</div>
                                                <div class="match-label">compatível</div>
                                            </div>
                                        </div>
                                        <h3 class="m-0 mb-1"><?= e($vacancy['title']) ?></h3>
                                        <p class="muted mb-3"><?= e($vacancy['trade_name']) ?> · <?= e($vacancy['city']) ?>, <?= e($vacancy['state']) ?></p>
                                        <div class="match-bar" role="progressbar" aria-label="Compatibilidade"
                                             aria-valuenow="<?= e($vacancy['match_score']) ?>" aria-valuemin="0" aria-valuemax="100">
                                            <div class="match-fill" data-fill="<?= e($vacancy['match_score']) ?>"></div>
                                        </div>
                                        <div class="chips mt-3">
                                            <span class="badge badge-green"><?= e(modality_label($vacancy['modality'])) ?></span>
                                            <span class="badge badge-gray"><?= e(brl($vacancy['scholarship'])) ?></span>
                                        </div>
                                        <div class="divider"></div>
                                        <div class="flex-between">
                                            <span class="why-tag"><?= e($vacancy['match_reasons'][0]) ?></span>
                                            <a class="btn btn-primary btn-sm" href="<?= e(route_url('vaga', ['id' => $vacancy['id']])) ?>">Ver</a>
                                        </div>
                                    </article>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="carousel-nav">
                        <button class="carousel-btn" type="button" data-carousel-prev aria-label="Vaga anterior"><?= icon('chevron-left') ?></button>
                        <div class="carousel-dots" data-carousel-dots></div>
                        <button class="carousel-btn" type="button" data-carousel-next aria-label="Próxima vaga"><?= icon('chevron-right') ?></button>
                    </div>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <!-- Testimonials -->
    <section class="section section-narrow" aria-labelledby="quotes-title">
        <div class="section-head" data-reveal>
            <span class="eyebrow">Quem usa, aprova</span>
            <h2 id="quotes-title">Confiança dos dois lados</h2>
        </div>
        <div class="grid-3">
            <figure class="quote-card m-0" data-reveal>
                <div class="quote-stars" aria-hidden="true">★★★★★</div>
                <blockquote class="m-0"><p>“Em uma tarde montei meu perfil e já tinha 3 vagas com mais de 90% de match. Me candidatei em minutos.”</p></blockquote>
                <figcaption class="quote-author">
                    <span class="avatar">MA</span>
                    <span><strong>Marina A.</strong><span class="muted block">Estudante de Ciência da Computação</span></span>
                </figcaption>
            </figure>
            <figure class="quote-card m-0" data-reveal style="--reveal-delay:90ms">
                <div class="quote-stars" aria-hidden="true">★★★★★</div>
                <blockquote class="m-0"><p>“Recebemos candidatos já ranqueados por compatibilidade. A reputação bidirecional elevou a qualidade do processo.”</p></blockquote>
                <figcaption class="quote-author">
                    <span class="avatar">TC</span>
                    <span><strong>TechCorp</strong><span class="muted block">Empresa parceira</span></span>
                </figcaption>
            </figure>
            <figure class="quote-card m-0" data-reveal style="--reveal-delay:180ms">
                <div class="quote-stars" aria-hidden="true">★★★★★</div>
                <blockquote class="m-0"><p>“O VLibras e o alto contraste fizeram diferença real pra mim. Finalmente uma plataforma que pensou em acessibilidade.”</p></blockquote>
                <figcaption class="quote-author">
                    <span class="avatar">JP</span>
                    <span><strong>João P.</strong><span class="muted block">Estudante de Sistemas</span></span>
                </figcaption>
            </figure>
        </div>
    </section>

    <!-- FAQ -->
    <section class="section section-alt" aria-labelledby="faq-title">
        <div class="section-narrow">
            <div class="section-head" data-reveal>
                <span class="eyebrow">Dúvidas frequentes</span>
                <h2 id="faq-title">Tudo que você precisa saber</h2>
            </div>
            <div class="faq" data-reveal>
                <details open>
                    <summary>O EstágioMatch é gratuito?</summary>
                    <p>Sim. Estudantes usam de graça e empresas têm um plano gratuito para publicar vagas ativas por 30 dias.</p>
                </details>
                <details>
                    <summary>Como funciona o cálculo de compatibilidade?</summary>
                    <p>O match combina suas habilidades, curso, modalidade preferida, cidade e bolsa mínima com cada vaga, gerando uma nota de 0 a 100%.</p>
                </details>
                <details>
                    <summary>Quais recursos de acessibilidade existem?</summary>
                    <p>Tradução em Libras (VLibras), modo de alto contraste, ajuste de tamanho de fonte, redução de animações, tema escuro e navegação por teclado.</p>
                </details>
                <details>
                    <summary>Meus dados estão seguros?</summary>
                    <p>Usamos sessão reforçada, proteção CSRF, hash de senha Argon2id e coletamos o mínimo de dados, em conformidade com a LGPD.</p>
                </details>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="section section-narrow">
        <div class="landing-cta" data-reveal>
            <h2>Pronto para o seu próximo estágio?</h2>
            <p>Crie sua conta gratuita e receba vagas compatíveis com o seu perfil ainda hoje.</p>
            <div class="inline-gap justify-center">
                <a class="btn btn-primary btn-lg" href="<?= e(route_url('login')) ?>#cadastro"><?= icon('rocket') ?> Começar agora</a>
                <a class="btn btn-outline btn-lg" href="<?= e(route_url('vagas')) ?>">Explorar vagas</a>
            </div>
        </div>
    </section>
</main>
