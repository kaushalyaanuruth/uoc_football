<?php
const DB_HOST = 'localhost';
const DB_USER = 'root';
const DB_PASS = '';
const DB_NAME = 'UOC_FOOTBALL';

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
    
    echo "=== Database Debug Report ===\n\n";
    
    // Check teams
    echo "1. Teams in database:\n";
    $result = $pdo->query("SELECT * FROM teams");
    $teams = $result->fetchAll(PDO::FETCH_OBJ);
    echo "   Total: " . count($teams) . "\n";
    foreach ($teams as $team) {
        echo "   - Team ID: {$team->team_id}, Season: {$team->season}, Status: {$team->status}\n";
    }
    
    // Check team_players
    echo "\n2. Team Players:\n";
    $result = $pdo->query("SELECT tp.team_id, COUNT(tp.player_id) as count FROM team_players tp GROUP BY tp.team_id");
    $teamPlayers = $result->fetchAll(PDO::FETCH_OBJ);
    echo "   Total team_players records: " . count($teamPlayers) . "\n";
    foreach ($teamPlayers as $tp) {
        echo "   - Team {$tp->team_id}: {$tp->count} players\n";
    }
    
    // Check players and users
    echo "\n3. Players with User Info:\n";
    $result = $pdo->query("
        SELECT p.player_id, u.first_name, u.last_name, p.position
        FROM players p
        LEFT JOIN users u ON p.nic = u.nic
        LIMIT 5
    ");
    $players = $result->fetchAll(PDO::FETCH_OBJ);
    echo "   Total: " . count($players) . "\n";
    foreach ($players as $player) {
        echo "   - Player {$player->player_id}: {$player->first_name} {$player->last_name} ({$player->position})\n";
    }
    
    // Check test_results
    echo "\n4. Test Results in Database:\n";
    $result = $pdo->query("
        SELECT tr.result_id, tr.player_id, tr.team_id, tr.test_type, tr.date, tr.score
        FROM test_results tr
        ORDER BY tr.result_id DESC
    ");
    $testResults = $result->fetchAll(PDO::FETCH_OBJ);
    echo "   Total: " . count($testResults) . "\n";
    foreach ($testResults as $tr) {
        echo "   - Result ID: {$tr->result_id}, Player: {$tr->player_id}, Team: {$tr->team_id}, Type: {$tr->test_type}, Date: {$tr->date}, Score: {$tr->score}\n";
    }
    
    // Check match_results
    echo "\n5. Match Results in Database:\n";
    $result = $pdo->query("
        SELECT result_id, team_id, opponent_team, result, date, score
        FROM match_results
        ORDER BY result_id DESC
    ");
    $matchResults = $result->fetchAll(PDO::FETCH_OBJ);
    echo "   Total: " . count($matchResults) . "\n";
    foreach ($matchResults as $mr) {
        echo "   - Result ID: {$mr->result_id}, Team: {$mr->team_id}, Opponent: {$mr->opponent_team}, Result: {$mr->result}, Date: {$mr->date}\n";
    }
    
    // Test the getPresentTeamId logic
    echo "\n6. Team ID Resolution (simulating getPresentTeamId):\n";
    $result = $pdo->query("SELECT DISTINCT tp.team_id FROM team_players tp LIMIT 1");
    $teamRes = $result->fetch(PDO::FETCH_OBJ);
    if ($teamRes) {
        echo "   First team with players: {$teamRes->team_id}\n";
    } else {
        echo "   No teams with players found\n";
    }
    
    echo "\n✓ Debug complete\n";
    
} catch (PDOException $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    exit(1);
}
