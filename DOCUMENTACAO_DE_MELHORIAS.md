# DOCUMENTAÇÃO DE MELHORIAS — EstágioMatch (Back-end)

> **Escopo deste documento:** apenas o **back-end** que ainda precisa ser feito.
> Todo o front-end da auditoria original já foi implementado (acessibilidade/VLibras, responsividade,
> remoção de estilos inline, filtros reais, favoritos, medidor de senha, PWA, landing moderna, etc.)
> e foi removido desta lista. Alguns itens de back-end de baixo custo também já foram concluídos
> (ver “Já concluído” abaixo) e não são repetidos.
>
> Stack: PHP 8.1+ (MVC artesanal, sem framework), MySQL 8 (PDO, prepared statements).
> Base: 2 front controllers (`controlador.php` GET, `processamento.php` POST), 7 controladores, 8 models, 11 tabelas.

---

## JÁ CONCLUÍDO (não refazer)

**Front-end:** acessibilidade completa (VLibras gov.br, barra de fonte/contraste/movimento, tema escuro, skip link, ARIA, `role="progressbar"`), responsividade (768/480 + menu hambúrguer), `prefers-reduced-motion`, remoção de ~100 estilos inline → classes utilitárias, `<select>` de UF (27 estados), medidor de força de senha + confirmar senha, prevenção de duplo submit, filtros reais client-side + “carregar mais”, favoritos (localStorage), checklist de onboarding, validação inline, senha demo escondida fora de debug, PWA (manifest + service worker), landing moderna (hero, contadores, carrossel, FAQ, CTA, scroll-reveal).

**Back-end (suporte ao front, já feito):**
- `Security::sendHeaders()` — CSP ajustada para o player Unity/WebGL do VLibras (`vlibras.gov.br` + `cdn.jsdelivr.net`, `unsafe-eval`, `blob:`, `worker-src`, `frame-src`); `X-Frame-Options` migrado para `frame-ancestors`.
- `helpers.php` — `icon()` (set de ícones SVG), `uf_list()` / `uf_options()`.
- `Vacancy::countActive()` e `HomeController` passando `totalVacancies` + 8 vagas para o carrossel.

> Tudo abaixo é **back-end pendente**. Onde o front já está pronto esperando o back, está marcado com **[front pronto]**.

---

## PRIORIZAÇÃO (back-end pendente)

| # | Item | Esforço | Impacto | Bloqueia |
|---|---|---|---|---|
| 1 | `APP_DEBUG` default `false` + carregador de `.env` | Baixo | Alto (segurança) | — |
| 2 | Corrigir N+1 em `Vacancy` + usar índice FULLTEXT na busca | Baixo | Alto (perf) | — |
| 3 | Gestão de status de candidatura (empresa) | Médio | Alto | item 4 |
| 4 | Avaliação bidirecional (controller + rota) **[front pronto]** | Médio | Alto | — |
| 5 | Recuperação de senha (`password_resets` já existe) | Médio | Médio | — |
| 6 | Verificação de e-mail | Médio | Médio | — |
| 7 | Notificações (tabela + disparo) **[front pronto: centro na navbar]** | Médio | Médio | — |
| 8 | Monetização: turbinar vaga / premium + expiração automática | Médio | Alto (receita) | — |
| 9 | Endurecer demais headers/segurança (CNPJ, rate-limit, portfolio_url) | Médio | Médio | — |
| 10 | Roteador único + camada de Validação + Serviço de Recomendação | Alto | Médio | — |
| 11 | Geolocalização real (lat/long + Haversine) | Alto | Médio | item 10 ideal |
| 12 | Padronização total para português (banco, classes, arquivos) | Alto | Médio | fazer por último |

> Critério: 1–2 são correções baratas de alto impacto (fazer já). 3–8 destravam funcionalidades-âncora de venda. 12 é a mudança mais propensa a quebrar — fazer **isolada, em bloco, com testes**, por último.

---

## 1. CORREÇÕES CRÍTICAS BARATAS

### 1.1 `APP_DEBUG` default inseguro + falta de `.env` (PRIORIDADE 1)

`config/app.php` hoje: `'debug' => filter_var(getenv('APP_DEBUG') ?: true, FILTER_VALIDATE_BOOLEAN)` — **default `true`**. Em produção, `Controller::renderError()` expõe `$exception->getMessage()` (caminhos de arquivo, SQL). Além disso não há carregador de `.env`; `config/database.php` e `config/app.php` leem `getenv()` mas dependem de variáveis de ambiente do servidor.

→ **Fazer:**
1. Trocar o default para `false`: `getenv('APP_DEBUG') ?: false` (ou `getenv('APP_DEBUG') === 'true'`).
2. Adicionar mini-loader de `.env` no `bootstrap.php` (ler `BASE_PATH/.env`, `putenv`/`$_ENV` para cada `CHAVE=valor`, ignorar comentários).
3. Criar `.env.example` com `APP_ENV`, `APP_DEBUG`, `APP_URL`, `DB_*`.
4. Garantir que `.env` está no `.gitignore` e bloqueado no `.htaccess` (já há `FilesMatch` para `.env`).

> O front já condiciona a “senha demo” a `app_config('debug')` — basta o default virar `false` para sumir em produção.

### 1.2 N+1 na listagem de vagas + FULLTEXT ocioso (PRIORIDADE 2)

**N+1:** `Vacancy::attachComputedData()` chama `$this->skills((int)$vacancy['id'])` **dentro do `foreach`** — uma query por vaga.
→ **Fazer:** uma única query `SELECT vacancy_id, skill, is_required FROM vacancy_skills WHERE vacancy_id IN (:ids)` e agrupar por `vacancy_id` em PHP antes do loop.

**FULLTEXT ocioso:** `Vacancy::active()` filtra busca com `LIKE '%termo%'` (linhas 16–19), que faz full table scan e ignora o índice `vacancies_search_fulltext` já existente no schema.
→ **Fazer:** quando houver `$filters['q']`, usar `MATCH(title, description, requirements, area) AGAINST (:q IN NATURAL LANGUAGE MODE)`. Manter `LIKE` apenas como fallback se a coluna `area` não estiver no índice.

**Índice de ordenação:** a query ordena por `is_boosted DESC, published_at DESC`, mas o índice `vacancies_status_idx (status, published_at)` não cobre `is_boosted`.
→ **Fazer:** criar índice `(status, is_boosted, published_at)` e validar com `EXPLAIN` que o `filesort` sumiu.

---

## 2. FUNCIONALIDADES-ÂNCORA (destravam venda)

### 2.1 Gestão de status de candidatura pela empresa (PRIORIDADE 3)

Hoje `Application::companyApplications()` **lista** candidaturas, mas a empresa não consegue mudar o status (`visualizada`, `em_analise`, `entrevista`, `aprovada`, `reprovada`, `cancelada`).
→ **Fazer:**
- Ação POST `candidatura_status` em `processamento.php` → método em `CandidaturaController` (ou novo `EmpresaController::updateApplicationStatus`).
- Validar que a candidatura pertence a uma vaga da empresa logada (`Auth::isCompany()` + ownership).
- Transição de status + registro em `audit_logs`.
- **Front:** adicionar um `<select>`/botões de status na tabela de candidaturas do painel da empresa (`dashboard/index.php`, ramo `Auth::isCompany()`).
- **Pré-requisito do item 2.2** (avaliação só libera após `aprovada`).

### 2.2 Avaliação bidirecional (PRIORIDADE 4) — **[front pronto]**

A tabela `ratings` e o model `Rating` existem; **falta controller e rota** para criar avaliações. O CSS da tela de estrelas (`.star-rating`) e os critérios (aprendizado, mentoria, ambiente) já estão no front, só faltando a view e o back.
→ **Fazer:**
- `AvaliacaoControlador` com ação `avaliar` (POST) e, se quiser, uma rota GET para a tela.
- Liberar avaliação somente quando existir candidatura com status `aprovada` entre as duas partes.
- Gravar `rating_type` = `empresa_para_estudante` / `estudante_para_empresa`, notas por critério e comentário.
- Atualizar `companies.rating_avg` (e a média do estudante) após inserir.
- **Front:** criar a view com `.star-rating` (já estilizada) ligada à nova ação.

### 2.3 Recuperação de senha (PRIORIDADE 5)

Tabela `password_resets` modelada e **sem nenhum uso**.
→ **Fazer:**
- Rotas `esqueci-senha` (GET form + POST gera token) e `redefinir-senha` (GET com token + POST grava).
- Gerar token aleatório, guardar **hash** do token + expiração (ex.: 1h) em `password_resets`.
- Enviar link por e-mail (no MVP, logar o link via `audit_logs` ou exibir em ambiente de dev).
- No reset: validar token/expiração, aplicar `Security::strongPasswordErrors`, `Security::passwordHash`, invalidar token.
- **Front:** a tela de redefinição deve reusar o medidor de força + confirmar senha **já prontos** no `app.js` (`data-password-meter`, `data-confirm-for`).

### 2.4 Verificação de e-mail (PRIORIDADE 6)

Coluna `email_verified_at` nunca é preenchida — qualquer e-mail é aceito.
→ **Fazer:** no cadastro, gerar token de verificação; rota `verificar-email?token=...` que preenche `email_verified_at`. Opcional: bloquear ações sensíveis (publicar vaga) enquanto não verificado. Reaproveitar a infra de e-mail do item 2.3.

### 2.5 Notificações (PRIORIDADE 7) — **[front pronto: centro na navbar]**

Não existe tabela de notificações, apesar de “notificações automáticas” ser processo-âncora.
→ **Fazer:**
- Migração: `notificacoes (id, usuario_id, tipo, titulo, mensagem, lida_em NULL, criado_em)` + índice `(usuario_id, lida_em)`.
- Disparar em: nova vaga com match alto para o estudante; mudança de status de candidatura (item 2.1).
- Endpoints: listar não lidas, marcar como lida.
- **Front:** a navbar já está preparada para receber o ícone de sino com badge — basta consumir o endpoint.

### 2.6 Monetização + expiração automática (PRIORIDADE 8)

`vacancies.is_boosted`, `companies.is_premium` e `vacancies.expires_at` **existem no schema** mas nenhum fluxo os ativa.
→ **Fazer:**
- Ação para marcar vaga como destaque (`is_boosted`) e empresa como `is_premium` (mock de pagamento no MVP — não recriar colunas).
- Comando CLI/cron que muda `status` para `expirada` quando `expires_at < NOW()` e mantém o board limpo.
- **Front:** badge “Destaque” já é renderizado para `is_boosted` na listagem.

---

## 3. SEGURANÇA RESTANTE (PRIORIDADE 9)

| Fraqueza | Correção | Impacto |
|---|---|---|
| **Sem validação de CNPJ**: `companies.cnpj_hash` existe mas empresa é criada sem CNPJ, enquanto a UI sugere “empresas verificadas”. | Validar dígito verificador no cadastro de empresa e gravar `cnpj_hash`. | Médio — claim falso na UI. |
| **Rate-limit só no login**: cadastro e candidatura não são protegidos. | Aplicar o mesmo throttle (`login_attempts`/genérico) a POSTs sensíveis. | Baixo/Médio — abuso automatizado. |
| **`portfolio_url` valida formato, não esquema**: aceitaria `javascript:`. | Exigir `https?://` explicitamente antes de renderizar como link. | Baixo. |
| **Fingerprint só de User-Agent**: fraco e pode deslogar usuário legítimo. | Documentar a limitação ou combinar com checagem parcial de IP. | Baixo. |

> Reforço com `'unsafe-inline'` em `style-src`: ainda é necessário porque as barras de progresso e o anel de score usam `style="--score/width"` dinâmico (data-binding legítimo). Para remover, seria preciso migrar esses valores para CSS custom properties setadas via atributo `data-*` + JS, ou usar nonce por requisição. Baixa prioridade.

---

## 4. ARQUITETURA (PRIORIDADE 10)

**Roteamento duplicado:** `controlador.php` (GET) e `processamento.php` (POST) mantêm dois mapas `match`/array; não há suporte a parâmetros de path (`/vaga/42`), só query string.
→ Extrair `Nucleo\Roteador` que registra rotas com método HTTP, controlador, ação e middleware (`requireRole`). Os dois front controllers passam a delegar para ele.

**Controladores “gordos”:** `AuthController::register()` (~60 linhas, 6 `if throw`), `EmpresaController::storeVacancy()` e `PerfilController::update()` repetem validação manual campo a campo.
→ Criar `Nucleo\Validador` (ou classes `Requisicao*`) que recebem `$_POST`, validam e devolvem dados limpos ou lançam exceção com a lista de erros. Manter a transação no controller (não dentro do validador).
→ **Front pronto:** o front já marca campos com `aria-invalid` + `.field-error` — basta o back devolver os erros por campo.

**Lógica de domínio no Model:** `Vacancy::matchScore()`, `matchReasons()`, `courseAreaHint()` acoplam o algoritmo de recomendação ao acesso a dados.
→ Extrair `Nucleo\Servicos\ServicoRecomendacao` com os pesos como constantes configuráveis; o Model só busca dados.

**Duplicação:** `initials()` é idêntico em `User.php` e `Company.php`.
→ Mover para helper `iniciais(string $nome)` (já existe `helpers.php`).

---

## 5. GEOLOCALIZAÇÃO REAL (PRIORIDADE 11)

`student_profiles.max_distance_km` existe e o discurso vende “recomendação por proximidade”, mas não há `latitude`/`longitude` — o match compara cidade por igualdade de string. O **filtro de distância no front já existe** (slider), porém sem dado real por trás.
→ **Fazer:**
- Migração: `latitude DECIMAL(10,7)`, `longitude DECIMAL(10,7)` em `vacancies` e `student_profiles`.
- Popular via ViaCEP/geocoding ao gravar o CEP no perfil/vaga.
- Usar fórmula de **Haversine** no `ServicoRecomendacao` e respeitar `max_distance_km`.

---

## 6. PADRONIZAÇÃO TOTAL PARA PORTUGUÊS (PRIORIDADE 12 — por último)

O sistema mistura português (rotas `rota`/`acao`, arquivos `controlador.php`) com inglês (classes, tabelas, colunas, banco `estagiomatch`). É a tarefa de **maior risco de quebra** — fazer em bloco isolado, com testes, **depois** das funcionais.

**Banco:** `estagiomatch` → `estagio_match`.

**Tabelas:** `users→usuarios`, `password_resets→redefinicoes_senha`, `student_profiles→perfis_estudantes`, `student_skills→habilidades_estudantes`, `companies→empresas`, `vacancies→vagas`, `vacancy_skills→habilidades_vagas`, `applications→candidaturas`, `ratings→avaliacoes`, `login_attempts→tentativas_login`, `audit_logs→registros_auditoria`.

**Colunas (exemplos — aplicar a todas):**
- `usuarios`: `role→tipo`, `name→nome`, `password_hash→senha_hash`, `status→situacao`, `email_verified_at→email_verificado_em`, `lgpd_accepted_at→lgpd_aceita_em`, `last_login_at→ultimo_acesso_em`, `created_at→criado_em`, `updated_at→atualizado_em`, `deleted_at→excluido_em`.
- `vagas`: `title→titulo`, `description→descricao`, `requirements→requisitos`, `modality→modalidade`, `city→cidade`, `state→estado`, `scholarship→bolsa`, `is_boosted→destaque`, `published_at→publicada_em`, `expires_at→expira_em` (e demais).
- `perfis_estudantes`: `course→curso`, `institution→instituicao`, `current_period→periodo_atual`, `max_distance_km→distancia_maxima_km`, `min_scholarship→bolsa_minima`, `accessibility_libras→acessibilidade_libras`, `profile_completeness→completude_perfil` (e demais).
- `empresas`: `trade_name→nome_fantasia`, `legal_name→razao_social`, `is_premium→premium`, `rating_avg→media_avaliacao`.
- `candidaturas`: `cover_letter→carta_apresentacao`, `match_score→pontuacao_match`, `student_id→estudante_id`, `vacancy_id→vaga_id`.
- `avaliacoes`: `rater_user_id→avaliador_id`, `rated_user_id→avaliado_id`, `score→nota`, etc.

**ENUMs:** `usuarios.situacao` `active/blocked/deleted` → `ativo/bloqueado/excluido`; `vagas.situacao` `draft/active/paused/expired/closed` → `rascunho/ativa/pausada/expirada/encerrada`.

**Arquivos/pastas/classes:** `Core→Nucleo`, `Controllers→Controladores`, `Models→Modelos`, `Views→Visoes`; `AuthController→AutenticacaoControlador`, `VagaController→VagaControlador`, `Database→BancoDeDados`, `Security→Seguranca`, `Auth→Autenticacao`, `Models/User→Modelos/Usuario`, `Models/Vacancy→Modelos/Vaga` etc.
> **Namespace `App\` mantém-se** — trocar o prefixo quebra o PSR-4 sem ganho.

**Ordem segura (obrigatória):**
1. Migração SQL com `RENAME TABLE` e `ALTER TABLE ... CHANGE COLUMN` (não recriar tabelas — preserva dados).
2. Atualizar todos os SQL literais nos Models (busca-e-substitui controlado por tabela/coluna).
3. Renomear arquivos de classe + atualizar `use`/namespaces (o `Autoloader` mapeia `App\Pasta\Classe` → `app/Pasta/Classe.php`; **PascalCase** nos arquivos, pois Linux é case-sensitive).
4. Atualizar Views que acessam chaves de array vindas do banco (`$vacancy['trade_name']` → `$vaga['nome_fantasia']`).
5. Renomear o banco em `config/database.php` e no `schema.sql`.
6. Rodar `database/seed.php` do zero e testar **todos** os fluxos.

---

## RISCOS DE QUEBRA (atenção)

- **Chaves de array acopladas ao SQL:** sem ORM, renomear coluna quebra silenciosamente toda view/controller que acessa `$linha['coluna_antiga']`. Varredura global obrigatória por coluna.
- **Autoloader case/path-sensitive:** em Linux, `Modelos/Usuario.php` ≠ `Modelos/usuario.php`. Manter PascalCase.
- **Transações:** `AuthController::register()` usa `beginTransaction/commit/rollBack`; ao extrair validação, manter a transação no controller.
- **Colunas que já existem** (`is_boosted`, `is_premium`, `expires_at`, `cnpj_hash`, `email_verified_at`): ao implementar os fluxos, **não recriar** — apenas usar.
- **Sessão/fingerprint:** alterar `Security::startSession()` pode deslogar todos. Testar login após qualquer mudança de sessão.

## PADRÕES A PRESERVAR (não regredir)

PDO com prepared statements (nunca concatenar SQL), `e()` em toda saída HTML, `csrf_field()` em todo form POST (e `Security::verifyCsrf` global no `processamento.php`), `Auth::requireRole()` em ações protegidas, hash Argon2id. São a fundação de segurança do projeto.
