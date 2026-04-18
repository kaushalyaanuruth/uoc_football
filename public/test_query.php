<?php
const DB_HOST = 'localhost';
const DB_USER = 'root';
const DB_PASS = '';
const DB_NAME = 'UOC_FOOTBALL';

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
    ]);
    
    echo "=== Testing Test Results Query ===\n\n";
    
    $team_id = 7;
    
    // Test raw query
    echo "1. Raw SELECT:\n";
    $result = $pdo->query("SELECT * FROM test_results WHERE team_id = 7");
    $rows = $result->fetchAll();
    echo "   Found: " . count($rows) . " rows\n";
    foreach ($rows as $row) {
        echo "   - ID: {$row->result_id}, Type: {$row->test_type}, Score: {$row->score}\n";
    }
    
    // Test prepared statement with array
    echo "\n2. Prepared with parameter array:\n";
    $stmt = $pdo->prepare("SELECT * FROM test_results WHERE team_id = :team_id");
    $stmt->execute(['team_id' => $team_id]);
    $rows = $stmt->fetchAll();
    echo "   Found: " . count($rows) . " rows\n";
    foreach ($rows as $row) {
        echo "   - ID: {$row->result_id}, Type: {$row->test_type}, Score: {$row->score}\n";
    }
    
    // Test the full getByTeamId query
    echo "\n3. Full getByTeamId query:\n";
    $query = "SELECT tr.*, p.player_id,
              CONCAT(COALESCE(u.first_name, 'Unknown'), ' ', COALESCE(u.last_name, '')) as player_name,
              u.first_name, u.last_name
              FROM test_results tr
              LEFT JOIN players p ON tr.player_id = p.player_id
              LEFT JOIN users u ON p.nic = u.nic
              WHERE tr.team_id = :team_id
              ORDER BY tr.date DESC";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute(['team_id' => $team_id]);
    $rows = $stmt->fetchAll();
    echo "   Found: " . count($rows) . " rows\n";
    foreach ($rows as $row) {
        echo "   - ID: {$row->result_id}, Player Name: {$row->player_name}, Type: {$row->test_type}, Score: {$row->score}\n";
    }
    
    // Check if the controller query works
    echo "\n4. Testing controller logic:\n";
    echo "   Team ID from getPresentTeamId would be: 7\n";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['team_id' => 7]);
    $rows = $stmt->fetchAll();
    echo "   Query returned: " . count($rows) . " rows\n";
    
    echo "\n✓ Query test complete\n";
    
} catch (PDOException $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    exit(1);
}
