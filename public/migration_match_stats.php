<?php

// This script migrates the match_results table to add match statistics fields

$host = 'localhost';
$user = 'root';
$password = '';
$database = 'uoc_football';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Starting migration of match_results table...\n";
    echo "Adding new statistics columns...\n\n";

    // Check if columns already exist
    $checkQuery = "SHOW COLUMNS FROM match_results LIKE 'shots'";
    $result = $pdo->query($checkQuery);
    
    if ($result && $result->rowCount() > 0) {
        echo "✓ Statistics columns already exist. Migration skipped.\n";
    } else {
        // Add new columns one by one
        $columns_to_add = [
            "ALTER TABLE match_results ADD COLUMN shots INT(32) DEFAULT 0 AFTER result",
            "ALTER TABLE match_results ADD COLUMN shots_on_target INT(32) DEFAULT 0 AFTER shots",
            "ALTER TABLE match_results ADD COLUMN possession DECIMAL(5,2) DEFAULT 0 AFTER shots_on_target",
            "ALTER TABLE match_results ADD COLUMN passes INT(32) DEFAULT 0 AFTER possession",
            "ALTER TABLE match_results ADD COLUMN passes_accuracy DECIMAL(5,2) DEFAULT 0 AFTER passes",
            "ALTER TABLE match_results ADD COLUMN corners INT(32) DEFAULT 0 AFTER passes_accuracy"
        ];

        foreach ($columns_to_add as $query) {
            try {
                $pdo->exec($query);
                $column_name = explode(" ADD COLUMN ", $query)[1];
                $column_name = explode(" ", $column_name)[0];
                echo "✓ Added column: $column_name\n";
            } catch (PDOException $e) {
                if (strpos($e->getMessage(), 'Duplicate column') !== false) {
                    echo "⚠ Column already exists, skipping...\n";
                } else {
                    throw $e;
                }
            }
        }

        // Reorder columns if needed - goals_scored and goals_conceded should come after result
        echo "\n✓ Adding goals_scored and goals_conceded columns if needed...\n";
        
        try {
            $pdo->exec("ALTER TABLE match_results ADD COLUMN goals_scored INT(32) DEFAULT 0 AFTER result");
            echo "✓ Added goals_scored column\n";
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate column') !== false) {
                echo "⚠ goals_scored column already exists\n";
            }
        }

        try {
            $pdo->exec("ALTER TABLE match_results ADD COLUMN goals_conceded INT(32) DEFAULT 0 AFTER goals_scored");
            echo "✓ Added goals_conceded column\n";
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate column') !== false) {
                echo "⚠ goals_conceded column already exists\n";
            }
        }

        echo "\n✓ All statistics columns have been added successfully!\n";
        echo "\nNew table structure:\n";
        echo "- result_id (PK)\n";
        echo "- opponent_team\n";
        echo "- result\n";
        echo "- goals_scored\n";
        echo "- goals_conceded\n";
        echo "- shots\n";
        echo "- shots_on_target\n";
        echo "- possession (%)\n";
        echo "- passes\n";
        echo "- passes_accuracy (%)\n";
        echo "- corners\n";
        echo "- date\n";
        echo "- notes\n";
        echo "- team_id (FK)\n";
        echo "- created_at\n";
    }
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    die("Migration failed!\n");
}
?>
