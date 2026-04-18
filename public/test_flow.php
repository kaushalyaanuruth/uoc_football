<?php
// Simulate the controller flow
const DB_HOST = 'localhost';
const DB_USER = 'root';
const DB_PASS = '';
const DB_NAME = 'UOC_FOOTBALL';
const ROOT = 'http://localhost/UOC_Football';

class TestDebug {
    private $pdo;
    
    public function __construct() {
        $this->pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
        ]);
    }
    
    public function query($query, $data = []) {
        $stmt = $this->pdo->prepare($query);
        $stmt->execute($data);
        $rows = $stmt->fetchAll();
        return $rows ?: [];
    }
    
    public function getPresentTeamId() {
        try {
            // Get the first team that has players
            $query = "SELECT DISTINCT tp.team_id 
                      FROM team_players tp 
                      LIMIT 1";
            $result = $this->query($query, []);
            if (!empty($result)) {
                return (int)$result[0]->team_id;
            }
            
            // Fallback: get the first team from teams table
            $query = "SELECT team_id FROM teams LIMIT 1";
            $teams = $this->query($query, []);
            if (!empty($teams)) {
                return (int)$teams[0]->team_id;
            }
        } catch (Exception $e) {
            // Silently fail
        }
        
        return 1;
    }
    
    public function getTestResultsByTeamId($team_id) {
        $query = "SELECT tr.*, p.player_id,
                  CONCAT(COALESCE(u.first_name, 'Unknown'), ' ', COALESCE(u.last_name, '')) as player_name,
                  u.first_name, u.last_name
                  FROM test_results tr
                  LEFT JOIN players p ON tr.player_id = p.player_id
                  LEFT JOIN users u ON p.nic = u.nic
                  WHERE tr.team_id = :team_id
                  ORDER BY tr.date DESC";
        
        return $this->query($query, ['team_id' => $team_id]);
    }
    
    public function getMatchResultsByTeamId($team_id) {
        $query = "SELECT * FROM match_results 
                  WHERE team_id = :team_id
                  ORDER BY date DESC";
        
        return $this->query($query, ['team_id' => $team_id]);
    }
    
    public function debug() {
        echo "=== Simulating Controller Index Flow ===\n\n";
        
        $team_id = $this->getPresentTeamId();
        echo "1. Team ID retrieved: $team_id\n";
        
        $testResults = $this->getTestResultsByTeamId($team_id);
        echo "2. Test Results fetched: " . count($testResults) . " rows\n";
        foreach ($testResults as $result) {
            echo "   - ID: {$result->result_id}, Player: {$result->player_name}, Type: {$result->test_type}\n";
        }
        
        $matchResults = $this->getMatchResultsByTeamId($team_id);
        echo "3. Match Results fetched: " . count($matchResults) . " rows\n";
        foreach ($matchResults as $result) {
            echo "   - ID: {$result->result_id}, Opponent: {$result->opponent_team}, Result: {$result->result}\n";
        }
        
        echo "\n4. Data that would be passed to view:\n";
        echo "   testResults count: " . count($testResults) . "\n";
        echo "   matchResults count: " . count($matchResults) . "\n";
        
        echo "\n5. Empty check in view:\n";
        echo "   empty(\$testResults): " . (empty($testResults) ? 'TRUE' : 'FALSE') . "\n";
        echo "   empty(\$matchResults): " . (empty($matchResults) ? 'TRUE' : 'FALSE') . "\n";
        
        if (!empty($testResults)) {
            echo "\n6. First test result details:\n";
            $first = $testResults[0];
            echo "   result_id: {$first->result_id}\n";
            echo "   player_id: {$first->player_id}\n";
            echo "   player_name: {$first->player_name}\n";
            echo "   test_type: {$first->test_type}\n";
            echo "   date: {$first->date}\n";
            echo "   score: {$first->score}\n";
            echo "   team_id: {$first->team_id}\n";
        }
    }
}

try {
    $debug = new TestDebug();
    $debug->debug();
    echo "\n✓ Debug complete\n";
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}
