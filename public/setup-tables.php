<?php
/**
 * Database Migration Script - Create Tournaments and Achievements Tables
 * Run this ONCE to set up the tables
 * 
 * Access: http://localhost/UOC_Football/public/setup-tables.php
 */

// Database connection details
$host = 'localhost';
$db = 'uoc_football';
$user = 'root';
$password = '';

try {
    // Connect to database
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>Running Database Migrations...</h2>";
    
    // Create Tournaments Table
    $tournamentsSQL = "CREATE TABLE IF NOT EXISTS tournaments (
        tournament_id INT(32) PRIMARY KEY AUTO_INCREMENT,
        team_id INT(32) NOT NULL,
        tournament_name VARCHAR(255) NOT NULL,
        description TEXT,
        tournament_date DATE,
        result VARCHAR(50),
        FOREIGN KEY (team_id) REFERENCES teams(id) ON DELETE CASCADE ON UPDATE CASCADE
    )";
    
    $pdo->exec($tournamentsSQL);
    echo "<p style='color: green;'>✓ Tournaments table created/verified</p>";
    
    // Create Achievements Table
    $achievementsSQL = "CREATE TABLE IF NOT EXISTS achievements (
        achievement_id INT(32) PRIMARY KEY AUTO_INCREMENT,
        team_id INT(32) NOT NULL,
        achievement_name VARCHAR(255) NOT NULL,
        description TEXT,
        achievement_date DATE,
        award_type VARCHAR(100),
        FOREIGN KEY (team_id) REFERENCES teams(id) ON DELETE CASCADE ON UPDATE CASCADE
    )";
    
    $pdo->exec($achievementsSQL);
    echo "<p style='color: green;'>✓ Achievements table created/verified</p>";
    
    // Create indexes
    $indexSQL1 = "CREATE INDEX idx_tournaments_team_id ON tournaments(team_id)";
    $indexSQL2 = "CREATE INDEX idx_achievements_team_id ON achievements(team_id)";
    
    try {
        $pdo->exec($indexSQL1);
        echo "<p style='color: green;'>✓ Tournament index created</p>";
    } catch (Exception $e) {
        echo "<p style='color: orange;'>⚠ Tournament index may already exist</p>";
    }
    
    try {
        $pdo->exec($indexSQL2);
        echo "<p style='color: green;'>✓ Achievement index created</p>";
    } catch (Exception $e) {
        echo "<p style='color: orange;'>⚠ Achievement index may already exist</p>";
    }
    
    echo "<hr>";
    echo "<h3 style='color: green;'>✅ Migration Completed Successfully!</h3>";
    echo "<p><strong>Tables Created:</strong></p>";
    echo "<ul>";
    echo "<li><strong>tournaments</strong> - Stores team tournaments</li>";
    echo "<li><strong>achievements</strong> - Stores team achievements</li>";
    echo "</ul>";
    echo "<p><strong>Relationships:</strong></p>";
    echo "<ul>";
    echo "<li>One team can have multiple tournaments</li>";
    echo "<li>One team can have multiple achievements</li>";
    echo "<li>Deleting a team will automatically delete related tournaments and achievements</li>";
    echo "</ul>";
    echo "<p><strong>Next Steps:</strong></p>";
    echo "<ol>";
    echo "<li>Delete this file (setup-tables.php) for security</li>";
    echo "<li>Refresh the team management page</li>";
    echo "<li>You can now add tournaments and achievements to teams</li>";
    echo "</ol>";
    
} catch (Exception $e) {
    echo "<h2 style='color: red;'>✗ Error during migration:</h2>";
    echo "<p style='color: red;'>" . $e->getMessage() . "</p>";
    echo "<p><strong>Troubleshooting:</strong></p>";
    echo "<ul>";
    echo "<li>Make sure the 'uoc_football' database exists</li>";
    echo "<li>Make sure 'teams' table exists</li>";
    echo "<li>Check database username and password</li>";
    echo "</ul>";
}
?>
