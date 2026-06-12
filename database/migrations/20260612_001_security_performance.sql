-- Security/performance migration for documented backend requirements.
-- Run against the existing estagiomatch database.

SET @idx_exists := (
  SELECT COUNT(*)
  FROM information_schema.statistics
  WHERE table_schema = DATABASE()
    AND table_name = 'vacancies'
    AND index_name = 'vacancies_status_boosted_published_idx'
);

SET @sql := IF(
  @idx_exists = 0,
  'ALTER TABLE vacancies ADD INDEX vacancies_status_boosted_published_idx (status, is_boosted, published_at)',
  'SELECT 1'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
