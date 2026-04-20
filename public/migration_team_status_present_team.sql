-- Ensure teams.status exists and enforce a single present team.
-- Run this once on existing databases.

SET @has_status := (
    SELECT COUNT(*)
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'teams'
      AND COLUMN_NAME = 'status'
);

SET @alter_sql := IF(
    @has_status = 0,
    "ALTER TABLE teams ADD COLUMN status ENUM('present','past') NOT NULL DEFAULT 'past' AFTER season",
    "SELECT 'teams.status already exists' AS info"
);

PREPARE stmt FROM @alter_sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Normalize invalid values to 'past'.
UPDATE teams
SET status = 'past'
WHERE status IS NULL OR status NOT IN ('present', 'past');

-- Keep only one present team.
SET @present_count := (
    SELECT COUNT(*) FROM teams WHERE status = 'present'
);

SET @target_present_team := IF(
    @present_count > 0,
    (SELECT team_id FROM teams WHERE status = 'present' ORDER BY team_id DESC LIMIT 1),
    (SELECT team_id FROM teams ORDER BY team_id DESC LIMIT 1)
);

UPDATE teams SET status = 'past';
UPDATE teams SET status = 'present' WHERE team_id = @target_present_team;
