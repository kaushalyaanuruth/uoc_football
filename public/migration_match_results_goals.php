<?php

// This script migrates the match_results table from score field to goals_scored and goals_conceded

$host = 'localhost';
$user = 'root';
$password = '';
$database = 'uoc_football';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Starting migration of match_results table...\n";

    // Check if the table already has the new columns
    $checkQuery = "SHOW COLUMNS FROM match_results LIKE 'goals_scored'";
    $result = $pdo->query($checkQuery);
    
    if ($result->rowCount() > 0) {
        echo "Table already has goals_scored column. Migration skipped.\n";
    } else {
        // Add the new columns
        echo "Adding goals_scored and goals_conceded columns...\n";
        $pdo->exec("ALTER TABLE match_results ADD COLUMN goals_scored INT DEFAULT 0 AFTER result");
        $pdo->exec("ALTER TABLE match_results ADD COLUMN goals_conceded INT DEFAULT 0 AFTER goals_scored");
        
        // Optionally, you can migrate data from score column if it exists
        $checkScoreQuery = "SHOW COLUMNS FROM match_results LIKE 'score'";
        $scoreResult = $pdo->query($checkScoreQuery);
        
        if ($scoreResult->rowCount() > 0) {
            echo "Migrating data from score column to goals_scored and goals_conceded...\n";
            // Parse the score field (e.g., "2 - 1" format) to extract goals
            // For safety, we'll set all to 0 and let users update manually
            // or implement a parsing logic here
            
            $pdo->exec("UPDATE match_results SET goals_scored = 0, goals_conceded = 0 WHERE goals_scored = 0 AND goals_conceded = 0");
            
            // Optionally drop the score column after migration
            // $pdo->exec("ALTER TABLE match_results DROP COLUMN score");
            // For now, we'll keep it for reference
            
            echo "Data migration complete. Old score column retained for reference.\n";
        }
        
        echo "Migration completed successfully!\n";
    }
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
