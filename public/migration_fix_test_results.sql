-- Fix test_results table schema to match application code
-- Backup old table
CREATE TABLE test_results_backup AS SELECT * FROM test_results;

-- Drop the old table
DROP TABLE IF EXISTS test_results;

-- Create new test_results table with correct schema
CREATE TABLE IF NOT EXISTS test_results (
    result_id INT(32) PRIMARY KEY AUTO_INCREMENT,
    test_type VARCHAR(50) NOT NULL,
    date DATE NOT NULL,
    score VARCHAR(50) NOT NULL,
    notes VARCHAR(255),
    player_id INT(32) NOT NULL,
    team_id INT(32) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (player_id) REFERENCES players(player_id) ON DELETE RESTRICT ON UPDATE CASCADE,
    FOREIGN KEY (team_id) REFERENCES teams(team_id) ON DELETE RESTRICT ON UPDATE CASCADE
);

-- Restore data if needed (map old remarks to score)
INSERT INTO test_results (test_type, date, score, notes, player_id, team_id)
SELECT test_type, date, remarks, remarks, player_id, 1
FROM test_results_backup
WHERE test_results_backup.test_id > 0;
