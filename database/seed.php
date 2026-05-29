<?php

declare(strict_types=1);

require __DIR__ . '/../bootstrap.php';

use App\Core\Database;
use App\Core\Security;

$dbConfig = require BASE_PATH . '/config/database.php';
$serverDsn = sprintf('mysql:host=%s;port=%s;charset=%s', $dbConfig['host'], $dbConfig['port'], $dbConfig['charset']);

$server = new PDO($serverDsn, $dbConfig['username'], $dbConfig['password'], [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);
$server->exec(file_get_contents(__DIR__ . '/schema.sql'));

$db = Database::connection();
$db->beginTransaction();

try {
    $insertUser = $db->prepare(
        'INSERT INTO users (role, name, email, password_hash, status, lgpd_accepted_at, email_verified_at)
         VALUES (:role, :name, :email, :password_hash, "active", NOW(), NOW())'
    );

    $users = [
        'student' => ['estudante', 'João Silva', 'joao@estagiomatch.local'],
        'companyTech' => ['empresa', 'Marina TechCorp', 'empresa@techcorp.local'],
        'companyFin' => ['empresa', 'Rafael FinStart', 'rh@finstart.local'],
        'companyInova' => ['empresa', 'Camila Inova', 'rh@inova.local'],
    ];

    $userIds = [];
    foreach ($users as $key => [$role, $name, $email]) {
        $insertUser->execute([
            'role' => $role,
            'name' => $name,
            'email' => $email,
            'password_hash' => Security::passwordHash('Estagio@12345'),
        ]);
        $userIds[$key] = (int) $db->lastInsertId();
    }

    $db->prepare(
        'INSERT INTO student_profiles
         (user_id, course, institution, current_period, graduation_forecast, performance_index, city, state, neighborhood, interests,
          availability, preferred_modality, max_distance_km, min_scholarship, bio, portfolio_url, profile_completeness)
         VALUES
         (:user_id, "Ciência da Computação", "FCT / UNESP", 4, "Dezembro de 2026", 7.8, "Presidente Prudente", "SP",
          "Centro", "Desenvolvimento Web, Dados", "Manhã ou tarde", "hibrido", 15, 800.00,
          "Estudante em busca de estágio para crescer em produtos digitais e dados.",
          "https://github.com/demo", 88)'
    )->execute(['user_id' => $userIds['student']]);

    $insertSkill = $db->prepare('INSERT INTO student_skills (user_id, skill, level) VALUES (:user_id, :skill, :level)');
    foreach ([
        ['React.js', 'intermediario'],
        ['Python', 'intermediario'],
        ['SQL', 'basico'],
        ['Git', 'intermediario'],
        ['Figma', 'basico'],
    ] as [$skill, $level]) {
        $insertSkill->execute(['user_id' => $userIds['student'], 'skill' => $skill, 'level' => $level]);
    }

    $insertCompany = $db->prepare(
        'INSERT INTO companies (user_id, trade_name, sector, city, state, address, description, logo_initials, is_premium, rating_avg)
         VALUES (:user_id, :trade_name, :sector, :city, :state, :address, :description, :logo_initials, :is_premium, :rating_avg)'
    );

    $companies = [
        'tech' => [$userIds['companyTech'], 'TechCorp', 'Software B2B', 'Presidente Prudente', 'SP', 'Av. Paulista, 1000', 'Empresa de produtos digitais com foco em SaaS B2B.', 'TC', 1, 4.6],
        'fin' => [$userIds['companyFin'], 'FinStart', 'Fintech', 'São Paulo', 'SP', 'Remoto', 'Fintech especializada em análise de dados financeiros.', 'FS', 0, 4.4],
        'inova' => [$userIds['companyInova'], 'Inova Digital', 'Design e Produto', 'Presidente Prudente', 'SP', 'Rua das Startups, 55', 'Estúdio de design e inovação para negócios digitais.', 'IN', 0, 4.7],
    ];

    $companyIds = [];
    foreach ($companies as $key => [$userId, $tradeName, $sector, $city, $state, $address, $description, $initials, $premium, $rating]) {
        $insertCompany->execute([
            'user_id' => $userId,
            'trade_name' => $tradeName,
            'sector' => $sector,
            'city' => $city,
            'state' => $state,
            'address' => $address,
            'description' => $description,
            'logo_initials' => $initials,
            'is_premium' => $premium,
            'rating_avg' => $rating,
        ]);
        $companyIds[$key] = (int) $db->lastInsertId();
    }

    $insertVacancy = $db->prepare(
        'INSERT INTO vacancies
         (company_id, title, area, description, responsibilities, requirements, modality, city, state, address, period,
          duration_months, start_date_label, scholarship, workload, transport_included, status, is_boosted, published_at, expires_at)
         VALUES
         (:company_id, :title, :area, :description, :responsibilities, :requirements, :modality, :city, :state, :address, :period,
          :duration_months, :start_date_label, :scholarship, :workload, :transport_included, "active", :is_boosted, NOW(), DATE_ADD(NOW(), INTERVAL 30 DAY))'
    );
    $insertVacancySkill = $db->prepare('INSERT INTO vacancy_skills (vacancy_id, skill, is_required) VALUES (:vacancy_id, :skill, 1)');

    $vacancies = [
        [
            'company' => 'tech',
            'title' => 'Estágio em Desenvolvimento Web',
            'area' => 'Tecnologia',
            'description' => 'Você trabalhará com desenvolvedores seniores em produtos digitais, participando de features, integrações e code review.',
            'responsibilities' => "Desenvolver interfaces com React.js\nIntegrar APIs REST\nParticipar de cerimônias ágeis\nApoiar code reviews",
            'requirements' => "React.js\nPython\nGit\nInglês intermediário",
            'modality' => 'presencial',
            'city' => 'Presidente Prudente',
            'state' => 'SP',
            'address' => 'Av. Paulista, 1000',
            'period' => 'Manhã ou tarde',
            'duration' => 12,
            'scholarship' => 1200,
            'workload' => '6h/dia',
            'transport' => 1,
            'boosted' => 1,
            'skills' => ['React.js', 'Python', 'Git', 'TypeScript'],
        ],
        [
            'company' => 'fin',
            'title' => 'Estágio em Dados & Analytics',
            'area' => 'Dados & BI',
            'description' => 'Apoio na criação de dashboards, limpeza de dados e análises para decisões de produto financeiro.',
            'responsibilities' => "Criar consultas SQL\nConstruir dashboards\nAutomatizar relatórios em Python",
            'requirements' => "Python\nSQL\nPower BI ou Tableau",
            'modality' => 'remoto',
            'city' => 'São Paulo',
            'state' => 'SP',
            'address' => 'Remoto',
            'period' => 'Flexível',
            'duration' => 12,
            'scholarship' => 1400,
            'workload' => '6h/dia',
            'transport' => 0,
            'boosted' => 0,
            'skills' => ['Python', 'SQL', 'Tableau'],
        ],
        [
            'company' => 'inova',
            'title' => 'Estágio em UX/UI Design',
            'area' => 'Design',
            'description' => 'Participação em pesquisas, prototipação e melhoria de interfaces para produtos web.',
            'responsibilities' => "Mapear jornadas\nCriar wireframes\nApoiar testes de usabilidade",
            'requirements' => "Figma\nUX Research\nDesign System",
            'modality' => 'hibrido',
            'city' => 'Presidente Prudente',
            'state' => 'SP',
            'address' => 'Rua das Startups, 55',
            'period' => 'Tarde',
            'duration' => 6,
            'scholarship' => 900,
            'workload' => '5h/dia',
            'transport' => 1,
            'boosted' => 0,
            'skills' => ['Figma', 'UX', 'Adobe XD'],
        ],
        [
            'company' => 'tech',
            'title' => 'Estágio em Backend Node.js',
            'area' => 'Tecnologia',
            'description' => 'Apoio na criação de APIs, testes automatizados e documentação técnica.',
            'responsibilities' => "Desenvolver endpoints\nCriar testes\nDocumentar APIs",
            'requirements' => "Node.js\nTypeScript\nSQL",
            'modality' => 'hibrido',
            'city' => 'Assis',
            'state' => 'SP',
            'address' => 'Av. Central, 420',
            'period' => 'Manhã',
            'duration' => 12,
            'scholarship' => 1100,
            'workload' => '6h/dia',
            'transport' => 1,
            'boosted' => 0,
            'skills' => ['Node.js', 'TypeScript', 'SQL'],
        ],
    ];

    $vacancyIds = [];
    foreach ($vacancies as $vacancy) {
        $insertVacancy->execute([
            'company_id' => $companyIds[$vacancy['company']],
            'title' => $vacancy['title'],
            'area' => $vacancy['area'],
            'description' => $vacancy['description'],
            'responsibilities' => $vacancy['responsibilities'],
            'requirements' => $vacancy['requirements'],
            'modality' => $vacancy['modality'],
            'city' => $vacancy['city'],
            'state' => $vacancy['state'],
            'address' => $vacancy['address'],
            'period' => $vacancy['period'],
            'duration_months' => $vacancy['duration'],
            'start_date_label' => 'Imediato',
            'scholarship' => $vacancy['scholarship'],
            'workload' => $vacancy['workload'],
            'transport_included' => $vacancy['transport'],
            'is_boosted' => $vacancy['boosted'],
        ]);
        $vacancyId = (int) $db->lastInsertId();
        $vacancyIds[] = $vacancyId;

        foreach ($vacancy['skills'] as $skill) {
            $insertVacancySkill->execute(['vacancy_id' => $vacancyId, 'skill' => $skill]);
        }
    }

    $db->prepare(
        'INSERT INTO applications (vacancy_id, student_id, status, cover_letter, match_score, created_at)
         VALUES (:vacancy_id, :student_id, "em_analise", "Tenho interesse na vaga e disponibilidade para iniciar imediatamente.", 92, DATE_SUB(NOW(), INTERVAL 2 DAY))'
    )->execute(['vacancy_id' => $vacancyIds[0], 'student_id' => $userIds['student']]);

    $applicationId = (int) $db->lastInsertId();

    $db->prepare(
        'INSERT INTO ratings
         (application_id, rater_user_id, rated_company_id, rating_type, score, score_learning, score_mentorship, score_environment, comment)
         VALUES
         (:application_id, :rater_user_id, :rated_company_id, "estudante_para_empresa", 4.8, 4.7, 4.5, 4.8,
          "Ambiente ótimo para aprender, com mentoria próxima e entregas reais.")'
    )->execute([
        'application_id' => $applicationId,
        'rater_user_id' => $userIds['student'],
        'rated_company_id' => $companyIds['tech'],
    ]);

    $db->prepare(
        'INSERT INTO ratings
         (application_id, rater_user_id, rated_user_id, rating_type, score, comment)
         VALUES (:application_id, :rater_user_id, :rated_user_id, "empresa_para_estudante", 4.8,
          "Perfil responsável, comunicativo e com boa base técnica.")'
    )->execute([
        'application_id' => $applicationId,
        'rater_user_id' => $userIds['companyTech'],
        'rated_user_id' => $userIds['student'],
    ]);

    $db->commit();
    echo "Banco e dados demo criados com sucesso.\n";
    echo "Estudante: joao@estagiomatch.local / Estagio@12345\n";
    echo "Empresa: empresa@techcorp.local / Estagio@12345\n";
} catch (Throwable $exception) {
    $db->rollBack();
    throw $exception;
}

