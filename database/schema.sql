CREATE DATABASE IF NOT EXISTS estagiomatch
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE estagiomatch;

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS audit_logs;
DROP TABLE IF EXISTS login_attempts;
DROP TABLE IF EXISTS ratings;
DROP TABLE IF EXISTS applications;
DROP TABLE IF EXISTS vacancy_skills;
DROP TABLE IF EXISTS vacancies;
DROP TABLE IF EXISTS companies;
DROP TABLE IF EXISTS student_skills;
DROP TABLE IF EXISTS student_profiles;
DROP TABLE IF EXISTS password_resets;
DROP TABLE IF EXISTS users;

SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE users (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  role ENUM('estudante','empresa','admin') NOT NULL DEFAULT 'estudante',
  name VARCHAR(140) NOT NULL,
  email VARCHAR(190) NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  status ENUM('active','blocked','deleted') NOT NULL DEFAULT 'active',
  email_verified_at DATETIME NULL,
  lgpd_accepted_at DATETIME NULL,
  last_login_at DATETIME NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  deleted_at DATETIME NULL,
  UNIQUE KEY users_email_unique (email),
  KEY users_role_status_idx (role, status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE password_resets (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NOT NULL,
  token_hash CHAR(64) NOT NULL,
  expires_at DATETIME NOT NULL,
  used_at DATETIME NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT password_resets_user_fk FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  UNIQUE KEY password_resets_token_unique (token_hash)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE student_profiles (
  user_id BIGINT UNSIGNED PRIMARY KEY,
  course VARCHAR(140) NULL,
  institution VARCHAR(140) NULL,
  current_period TINYINT UNSIGNED NULL,
  graduation_forecast VARCHAR(80) NULL,
  performance_index DECIMAL(3,1) NULL,
  city VARCHAR(100) NULL,
  state CHAR(2) NULL,
  neighborhood VARCHAR(100) NULL,
  cep VARCHAR(12) NULL,
  interests VARCHAR(255) NULL,
  availability VARCHAR(80) NULL,
  preferred_modality ENUM('presencial','remoto','hibrido','qualquer') NOT NULL DEFAULT 'qualquer',
  max_distance_km SMALLINT UNSIGNED NOT NULL DEFAULT 15,
  min_scholarship DECIMAL(10,2) NOT NULL DEFAULT 800.00,
  bio TEXT NULL,
  portfolio_url VARCHAR(255) NULL,
  accessibility_libras TINYINT(1) NOT NULL DEFAULT 0,
  profile_completeness TINYINT UNSIGNED NOT NULL DEFAULT 25,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT student_profiles_user_fk FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  KEY student_profiles_location_idx (city, state),
  KEY student_profiles_course_idx (course)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE student_skills (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NOT NULL,
  skill VARCHAR(80) NOT NULL,
  level ENUM('basico','intermediario','avancado') NOT NULL DEFAULT 'basico',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT student_skills_user_fk FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  UNIQUE KEY student_skills_unique (user_id, skill)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE companies (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NOT NULL,
  trade_name VARCHAR(160) NOT NULL,
  legal_name VARCHAR(190) NULL,
  cnpj_hash CHAR(64) NULL,
  sector VARCHAR(120) NULL,
  city VARCHAR(100) NULL,
  state CHAR(2) NULL,
  address VARCHAR(190) NULL,
  description TEXT NULL,
  logo_initials VARCHAR(4) NULL,
  is_premium TINYINT(1) NOT NULL DEFAULT 0,
  rating_avg DECIMAL(2,1) NOT NULL DEFAULT 0.0,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT companies_user_fk FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  UNIQUE KEY companies_user_unique (user_id),
  KEY companies_location_idx (city, state)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE vacancies (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  company_id BIGINT UNSIGNED NOT NULL,
  title VARCHAR(160) NOT NULL,
  area VARCHAR(100) NOT NULL,
  description TEXT NOT NULL,
  responsibilities TEXT NULL,
  requirements TEXT NULL,
  modality ENUM('presencial','remoto','hibrido') NOT NULL DEFAULT 'presencial',
  city VARCHAR(100) NULL,
  state CHAR(2) NULL,
  address VARCHAR(190) NULL,
  period VARCHAR(80) NULL,
  duration_months TINYINT UNSIGNED NULL,
  start_date_label VARCHAR(80) NULL,
  scholarship DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  workload VARCHAR(80) NULL,
  transport_included TINYINT(1) NOT NULL DEFAULT 0,
  status ENUM('draft','active','paused','expired','closed') NOT NULL DEFAULT 'active',
  is_boosted TINYINT(1) NOT NULL DEFAULT 0,
  published_at DATETIME NULL,
  expires_at DATETIME NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT vacancies_company_fk FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
  KEY vacancies_status_idx (status, published_at),
  KEY vacancies_area_idx (area),
  KEY vacancies_location_idx (city, state),
  FULLTEXT KEY vacancies_search_fulltext (title, description, requirements, area)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE vacancy_skills (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  vacancy_id BIGINT UNSIGNED NOT NULL,
  skill VARCHAR(80) NOT NULL,
  is_required TINYINT(1) NOT NULL DEFAULT 1,
  CONSTRAINT vacancy_skills_vacancy_fk FOREIGN KEY (vacancy_id) REFERENCES vacancies(id) ON DELETE CASCADE,
  UNIQUE KEY vacancy_skills_unique (vacancy_id, skill)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE applications (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  vacancy_id BIGINT UNSIGNED NOT NULL,
  student_id BIGINT UNSIGNED NOT NULL,
  status ENUM('enviada','visualizada','em_analise','entrevista','aprovada','reprovada','cancelada') NOT NULL DEFAULT 'enviada',
  cover_letter TEXT NULL,
  match_score TINYINT UNSIGNED NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT applications_vacancy_fk FOREIGN KEY (vacancy_id) REFERENCES vacancies(id) ON DELETE CASCADE,
  CONSTRAINT applications_student_fk FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
  UNIQUE KEY applications_unique (vacancy_id, student_id),
  KEY applications_student_status_idx (student_id, status),
  KEY applications_vacancy_status_idx (vacancy_id, status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE ratings (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  application_id BIGINT UNSIGNED NULL,
  rater_user_id BIGINT UNSIGNED NOT NULL,
  rated_user_id BIGINT UNSIGNED NULL,
  rated_company_id BIGINT UNSIGNED NULL,
  rating_type ENUM('empresa_para_estudante','estudante_para_empresa') NOT NULL,
  score DECIMAL(2,1) NOT NULL,
  score_learning DECIMAL(2,1) NULL,
  score_mentorship DECIMAL(2,1) NULL,
  score_environment DECIMAL(2,1) NULL,
  comment TEXT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT ratings_application_fk FOREIGN KEY (application_id) REFERENCES applications(id) ON DELETE SET NULL,
  CONSTRAINT ratings_rater_fk FOREIGN KEY (rater_user_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT ratings_user_fk FOREIGN KEY (rated_user_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT ratings_company_fk FOREIGN KEY (rated_company_id) REFERENCES companies(id) ON DELETE CASCADE,
  KEY ratings_company_idx (rated_company_id, rating_type),
  KEY ratings_user_idx (rated_user_id, rating_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE login_attempts (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(190) NOT NULL,
  ip_address VARBINARY(16) NOT NULL,
  user_id BIGINT UNSIGNED NULL,
  successful TINYINT(1) NOT NULL DEFAULT 0,
  attempted_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT login_attempts_user_fk FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
  KEY login_attempts_email_ip_idx (email, attempted_at),
  KEY login_attempts_user_idx (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE audit_logs (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NULL,
  event VARCHAR(80) NOT NULL,
  context JSON NULL,
  ip_address VARBINARY(16) NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT audit_logs_user_fk FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
  KEY audit_logs_event_idx (event, created_at),
  KEY audit_logs_user_idx (user_id, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

