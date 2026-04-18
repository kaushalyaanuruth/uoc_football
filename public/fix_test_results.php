<?php
const DB_HOST = 'localhost';
const DB_USER = 'root';
const DB_PASS = '';
const DB_NAME = 'UOC_FOOTBALL';

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
    
    // Check current table structure
    echo "=== Current test_results table structure ===\n";
    $result = $pdo->query("DESCRIBE test_results");
    $columns = $result->fetchAll(PDO::FETCH_OBJ);
    foreach ($columns as $col) {
        echo "- {$col->Field} ({$col->Type})\n";
    }
    
    // Drop and recreate if needed
    echo "\n=== Checking for backup and cleanup ===\n";
    
    // Check if backup exists
    $tables = $pdo->query("SHOW TABLES LIKE 'test_results_backup'")->fetchAll();
    if (!empty($tables)) {
        echo "✓ Backup found, dropping it...\n";
        $pdo->exec("DROP TABLE IF EXISTS test_results_backup");
    }
    
    // Check if old test_results has wrong structure
    $hasScore = $pdo->query("SHOW COLUMNS FROM test_results LIKE 'score'")->fetch();
    
    if (!$hasScore) {
        echo "✗ Score column missing! Recreating table...\n";
        
        // Backup existing data if any
        $pdo->exec("CREATE TABLE test_results_old AS SELECT * FROM test_results");
        
        // Drop old table
        $pdo->exec("DROP TABLE test_results");
        
        // Create new table with correct schema
        $pdo->exec("
            CREATE TABLE test_results (
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
            )
        ");
        
        echo "✓ Table recreated successfully\n";
        
        // Restore data if any existed
        $oldCount = $pdo->query("SELECT COUNT(*) as cnt FROM test_results_old")->fetch(PDO::FETCH_OBJ)->cnt;
        if ($oldCount > 0) {
            echo "Restoring $oldCount records...\n";
            $pdo->exec("
                INSERT INTO test_results (test_type, date, score, notes, player_id, team_id)
                SELECT test_type, date, remarks, remarks, player_id, 1
                FROM test_results_old
            ");
            echo "✓ Data restored\n";
        }
        
        // Clean up old table
        $pdo->exec("DROP TABLE test_results_old");
    } else {
        echo "✓ Score column exists\n";
    }
    
    echo "\n=== Final table structure ===\n";
    $result = $pdo->query("DESCRIBE test_results");
    $columns = $result->fetchAll(PDO::FETCH_OBJ);
    foreach ($columns as $col) {
        echo "- {$col->Field} ({$col->Type})\n";
    }
    
    echo "\n✓ Database fixed successfully!\n";
    
} catch (PDOException $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    exit(1);
}
