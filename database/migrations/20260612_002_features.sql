-- Feature migration for password/email verification, notifications, geolocation and vacancy ordering.
-- Run against the existing estagiomatch database.

CREATE TABLE IF NOT EXISTS email_verifications (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NOT NULL,
  token_hash CHAR(64) NOT NULL,
  expires_at DATETIME NOT NULL,
  used_at DATETIME NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT email_verifications_user_fk FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  UNIQUE KEY email_verifications_token_unique (token_hash)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS notificacoes (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  usuario_id BIGINT UNSIGNED NOT NULL,
  tipo VARCHAR(60) NOT NULL,
  titulo VARCHAR(160) NOT NULL,
  mensagem VARCHAR(500) NOT NULL,
  lida_em DATETIME NULL,
  criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT notificacoes_usuario_fk FOREIGN KEY (usuario_id) REFERENCES users(id) ON DELETE CASCADE,
  KEY notificacoes_usuario_lida_idx (usuario_id, lida_em)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET @student_lat_exists := (
  SELECT COUNT(*) FROM information_schema.columns
  WHERE table_schema = DATABASE() AND table_name = 'student_profiles' AND column_name = 'latitude'
);
SET @sql := IF(@student_lat_exists = 0, 'ALTER TABLE student_profiles ADD COLUMN latitude DECIMAL(10,7) NULL AFTER cep', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @student_lng_exists := (
  SELECT COUNT(*) FROM information_schema.columns
  WHERE table_schema = DATABASE() AND table_name = 'student_profiles' AND column_name = 'longitude'
);
SET @sql := IF(@student_lng_exists = 0, 'ALTER TABLE student_profiles ADD COLUMN longitude DECIMAL(10,7) NULL AFTER latitude', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @vacancy_lat_exists := (
  SELECT COUNT(*) FROM information_schema.columns
  WHERE table_schema = DATABASE() AND table_name = 'vacancies' AND column_name = 'latitude'
);
SET @sql := IF(@vacancy_lat_exists = 0, 'ALTER TABLE vacancies ADD COLUMN latitude DECIMAL(10,7) NULL AFTER address', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @vacancy_lng_exists := (
  SELECT COUNT(*) FROM information_schema.columns
  WHERE table_schema = DATABASE() AND table_name = 'vacancies' AND column_name = 'longitude'
);
SET @sql := IF(@vacancy_lng_exists = 0, 'ALTER TABLE vacancies ADD COLUMN longitude DECIMAL(10,7) NULL AFTER latitude', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @idx_exists := (
  SELECT COUNT(*) FROM information_schema.statistics
  WHERE table_schema = DATABASE()
    AND table_name = 'vacancies'
    AND index_name = 'vacancies_status_boosted_published_idx'
);
SET @sql := IF(
  @idx_exists = 0,
  'ALTER TABLE vacancies ADD INDEX vacancies_status_boosted_published_idx (status, is_boosted, published_at)',
  'SELECT 1'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
