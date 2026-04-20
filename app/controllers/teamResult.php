<?php

class TeamResult extends Controller
{
    private $testResultModel;
    private $matchResultModel;
    private $playerModel;

    /**
     * Initialize controller with model instances
     */
    public function __construct()
    {
        $this->testResultModel = $this->model('TestResultModel');
        $this->matchResultModel = $this->model('MatchResultModel');
        $this->playerModel = $this->model('PlayerModel');
    }

    /**
     * Ensure user is authenticated
     */
    private function requireAuth()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . ROOT . '/login');
            exit;
        }
    }

    /**
     * Send JSON response
     */
    private function respondJson(array $payload)
    {
        // Clear any output that may have been buffered
        if (ob_get_length()) {
            ob_clean();
        }
        
        // Set proper headers
        header('Content-Type: application/json');
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');
        
        echo json_encode($payload);
        exit;
    }

    /**
     * Display team results page
     */
    public function index()
    {
        $this->requireAuth();

        $team_id = $this->getPresentTeamId();
        error_log("DEBUG: teamResult index - Team ID: $team_id");

        $testResults = [];
        try {
            $testResults = $this->testResultModel->getByTeamId($team_id);
            $testResults = is_array($testResults) ? $testResults : [];
            error_log("DEBUG: Test Results Count: " . count($testResults));
        } catch (Exception $e) {
            error_log("Error fetching test results: " . $e->getMessage());
            $testResults = [];
        }

        $matchResults = [];
        try {
            $matchResults = $this->matchResultModel->getByTeamId($team_id);
            $matchResults = is_array($matchResults) ? $matchResults : [];
            error_log("DEBUG: Match Results Count: " . count($matchResults));
        } catch (Exception $e) {
            error_log("Error fetching match results: " . $e->getMessage());
            $matchResults = [];
        }

        $data = [
            'testResults' => $testResults,
            'matchResults' => $matchResults
        ];
        
        $this->view('teamResult', $data);
    }

    /**
     * Get team ID - this would typically come from session or request
     * For now, we'll use a default or passed value
     */
    private function getTeamId()
    {
        return $_POST['team_id'] ?? $_GET['team_id'] ?? ($_SESSION['team_id'] ?? 1);
    }

    /**
     * Get present team ID - fetch from database
     */
    private function getPresentTeamId()
    {
        try {
            $statusColumn = $this->playerModel->query("SHOW COLUMNS FROM teams LIKE 'status'", []);
            $hasStatus = !empty($statusColumn);

            if ($hasStatus) {
                $query = "SELECT t.team_id
                          FROM teams t
                          WHERE t.status = 'present'
                          ORDER BY t.team_id DESC
                          LIMIT 1";
                $result = $this->playerModel->query($query, []);
                if (!empty($result)) {
                    return (int) $result[0]->team_id;
                }
            }

            // Fallback: get the latest team that has players.
            $query = "SELECT DISTINCT tp.team_id
                      FROM team_players tp
                      ORDER BY tp.team_id DESC
                      LIMIT 1";
            $result = $this->playerModel->query($query, []);
            if (!empty($result)) {
                return (int)$result[0]->team_id;
            }
            
            // Last fallback: get latest team from teams table.
            $query = "SELECT team_id FROM teams ORDER BY team_id DESC LIMIT 1";
            $teams = $this->playerModel->query($query, []);
            if (!empty($teams)) {
                return (int)$teams[0]->team_id;
            }
        } catch (Exception $e) {
            // Silently fail
        }
        
        return 1;
    }

    private function assertDateNotFuture($dateValue, $fieldLabel)
    {
        $dateValue = trim((string) $dateValue);
        if ($dateValue === '') {
            throw new Exception($fieldLabel . ' is required');
        }

        $date = DateTime::createFromFormat('Y-m-d', $dateValue);
        if (!$date || $date->format('Y-m-d') !== $dateValue) {
            throw new Exception('Invalid ' . strtolower($fieldLabel) . ' format');
        }

        $today = new DateTime('today');
        if ($date > $today) {
            throw new Exception($fieldLabel . ' cannot be in the future');
        }
    }

    /**
     * Add test result - AJAX endpoint
     */
    public function addTestResult()
    {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->respondJson(['success' => false, 'message' => 'Invalid request method']);
        }

        try {
            $team_id = $this->getPresentTeamId();
            
            // Get player ID by name - search in team players
            $playerName = trim($_POST['playerName'] ?? '');
            if (empty($playerName)) {
                throw new Exception('Player name is required');
            }

            // Search for player by name
            $query = "SELECT p.player_id FROM players p
                      LEFT JOIN users u ON p.nic = u.nic
                      WHERE CONCAT(COALESCE(u.first_name, ''), ' ', COALESCE(u.last_name, '')) LIKE :name
                      AND p.player_id IN (
                        SELECT player_id FROM team_players WHERE team_id = :team_id
                      )";
            
            $players = $this->testResultModel->query($query, [
                'name' => "%{$playerName}%",
                'team_id' => $team_id
            ]);

            if (empty($players)) {
                throw new Exception('Player not found in this team');
            }

            $player_id = $players[0]->player_id;

            $data = [
                'test_type' => trim($_POST['testTypeSelect'] ?? ''),
                'date' => $_POST['testDate'] ?? '',
                'score' => trim($_POST['score'] ?? ''),
                'notes' => trim($_POST['notes'] ?? ''),
                'player_id' => $player_id,
                'team_id' => $team_id
            ];

            // Validate required fields
            if (empty($data['test_type']) || empty($data['date']) || empty($data['score'])) {
                throw new Exception('Test type, date, and score are required');
            }

            $this->assertDateNotFuture($data['date'], 'Test date');

            $this->testResultModel->create($data);

            $this->respondJson([
                'success' => true,
                'message' => 'Test result added successfully'
            ]);
        } catch (Exception $e) {
            $this->respondJson([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Edit test result - AJAX endpoint
     */
    public function editTestResult()
    {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->respondJson(['success' => false, 'message' => 'Invalid request method']);
        }

        try {
            $result_id = $_POST['result_id'] ?? null;
            if (!$result_id) {
                throw new Exception('Result ID is required');
            }

            $data = [
                'test_type' => trim($_POST['testTypeSelect'] ?? ''),
                'date' => $_POST['testDate'] ?? '',
                'score' => trim($_POST['score'] ?? ''),
                'notes' => trim($_POST['notes'] ?? '')
            ];

            // Validate required fields
            if (empty($data['test_type']) || empty($data['date']) || empty($data['score'])) {
                throw new Exception('Test type, date, and score are required');
            }

            $this->assertDateNotFuture($data['date'], 'Test date');

            $this->testResultModel->update($result_id, $data);

            $this->respondJson([
                'success' => true,
                'message' => 'Test result updated successfully'
            ]);
        } catch (Exception $e) {
            $this->respondJson([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Delete test result - AJAX endpoint
     */
    public function deleteTestResult()
    {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->respondJson(['success' => false, 'message' => 'Invalid request method']);
        }

        try {
            $result_id = $_POST['result_id'] ?? null;
            if (!$result_id) {
                throw new Exception('Result ID is required');
            }

            $this->testResultModel->delete($result_id);

            $this->respondJson([
                'success' => true,
                'message' => 'Test result deleted successfully'
            ]);
        } catch (Exception $e) {
            $this->respondJson([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Add match result - AJAX endpoint
     */
    public function addMatchResult()
    {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->respondJson(['success' => false, 'message' => 'Invalid request method']);
        }

        try {
            $team_id = $this->getPresentTeamId();

            $data = [
                'opponent_team' => trim($_POST['opponentName'] ?? ''),
                'result' => trim($_POST['matchResult'] ?? ''),
                'goals_scored' => (int)($_POST['goalsScored'] ?? 0),
                'goals_conceded' => (int)($_POST['goalsConceded'] ?? 0),
                'shots' => (int)($_POST['shots'] ?? 0),
                'shots_on_target' => (int)($_POST['shotsOnTarget'] ?? 0),
                'possession' => (float)($_POST['possession'] ?? 0),
                'passes' => (int)($_POST['passes'] ?? 0),
                'passes_accuracy' => (float)($_POST['passesAccuracy'] ?? 0),
                'corners' => (int)($_POST['corners'] ?? 0),
                'date' => $_POST['matchDate'] ?? '',
                'notes' => trim($_POST['matchNotes'] ?? ''),
                'team_id' => $team_id
            ];

            // Validate required fields
            if (empty($data['opponent_team']) || empty($data['result']) || empty($data['date'])) {
                throw new Exception('Opponent team, result, and date are required');
            }

            $this->assertDateNotFuture($data['date'], 'Match date');

            // Validate result value
            if (!in_array($data['result'], ['Won', 'Draw', 'Lost'])) {
                throw new Exception('Result must be Won, Draw, or Lost');
            }

            // Create match result and get the ID
            $this->matchResultModel->create($data);
            $matchResultId = $this->matchResultModel->lastInsertId();
            
            // Handle player stats if provided
            $playerStatsJson = $_POST['player_stats'] ?? '[]';
            $playerStats = json_decode($playerStatsJson, true);
            
            if (!empty($playerStats) && is_array($playerStats)) {
                $playerStatsModel = $this->model('PlayerMatchStatsModel');
                
                foreach ($playerStats as $stats) {
                    // Add match_id to stats
                    $stats['match_id'] = $matchResultId;
                    
                    // Create player stat record
                    try {
                        $playerStatsModel->create($stats);
                    } catch (Exception $e) {
                        // Log error but continue with other players
                        error_log("Error creating player stat for player {$stats['player_id']}: " . $e->getMessage());
                    }
                }
            }

            $this->respondJson([
                'success' => true,
                'message' => 'Match result added successfully' . (!empty($playerStats) ? ' with ' . count($playerStats) . ' player(s)' : '')
            ]);
        } catch (Exception $e) {
            $this->respondJson([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Edit match result - AJAX endpoint
     */
    public function editMatchResult()
    {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->respondJson(['success' => false, 'message' => 'Invalid request method']);
        }

        try {
            $result_id = $_POST['result_id'] ?? null;
            if (!$result_id) {
                throw new Exception('Result ID is required');
            }

            $data = [
                'opponent_team' => trim($_POST['opponentName'] ?? ''),
                'result' => trim($_POST['matchResult'] ?? ''),
                'goals_scored' => (int)($_POST['goalsScored'] ?? 0),
                'goals_conceded' => (int)($_POST['goalsConceded'] ?? 0),
                'shots' => (int)($_POST['shots'] ?? 0),
                'shots_on_target' => (int)($_POST['shotsOnTarget'] ?? 0),
                'possession' => (float)($_POST['possession'] ?? 0),
                'passes' => (int)($_POST['passes'] ?? 0),
                'passes_accuracy' => (float)($_POST['passesAccuracy'] ?? 0),
                'corners' => (int)($_POST['corners'] ?? 0),
                'date' => $_POST['matchDate'] ?? '',
                'notes' => trim($_POST['matchNotes'] ?? '')
            ];

            // Validate required fields
            if (empty($data['opponent_team']) || empty($data['result']) || empty($data['date'])) {
                throw new Exception('Opponent team, result, and date are required');
            }

            $this->assertDateNotFuture($data['date'], 'Match date');

            $this->matchResultModel->update($result_id, $data);
            
            // Handle new player stats if provided
            $playerStatsJson = $_POST['player_stats'] ?? '[]';
            $playerStats = json_decode($playerStatsJson, true);
            
            if (!empty($playerStats) && is_array($playerStats)) {
                $playerStatsModel = $this->model('PlayerMatchStatsModel');
                
                foreach ($playerStats as $stats) {
                    // Add match_id to stats
                    $stats['match_id'] = $result_id;
                    
                    // Create player stat record
                    try {
                        $playerStatsModel->create($stats);
                    } catch (Exception $e) {
                        // Log error but continue with other players
                        error_log("Error creating player stat for player {$stats['player_id']}: " . $e->getMessage());
                    }
                }
            }

            $this->respondJson([
                'success' => true,
                'message' => 'Match result updated successfully' . (!empty($playerStats) ? ' with ' . count($playerStats) . ' new player(s)' : '')
            ]);
        } catch (Exception $e) {
            $this->respondJson([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Delete match result - AJAX endpoint
     */
    public function deleteMatchResult()
    {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->respondJson(['success' => false, 'message' => 'Invalid request method']);
        }

        try {
            $result_id = $_POST['result_id'] ?? null;
            if (!$result_id) {
                throw new Exception('Result ID is required');
            }

            // First, delete all player stats associated with this match
            $playerStatsModel = $this->model('PlayerMatchStatsModel');
            $playerStats = $playerStatsModel->getByMatchId($result_id);
            
            if (!empty($playerStats)) {
                foreach ($playerStats as $stat) {
                    $playerStatsModel->delete($stat['stat_id']);
                }
            }

            // Then delete the match result
            $this->matchResultModel->delete($result_id);

            $this->respondJson([
                'success' => true,
                'message' => 'Match result and associated player statistics deleted successfully'
            ]);
        } catch (Exception $e) {
            $this->respondJson([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }


    /**
     * Get single test result - AJAX endpoint
     */
    public function getTestResult()
    {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            $this->respondJson(['success' => false, 'message' => 'Invalid request method']);
        }

        try {
            $result_id = $_GET['id'] ?? null;
            if (!$result_id) {
                throw new Exception('Result ID is required');
            }

            $result = $this->testResultModel->getById($result_id);
            if (!$result) {
                throw new Exception('Test result not found');
            }

            $this->respondJson([
                'success' => true,
                'result' => $result
            ]);
        } catch (Exception $e) {
            $this->respondJson([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get single match result - AJAX endpoint
     */
    public function getMatchResult()
    {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            $this->respondJson(['success' => false, 'message' => 'Invalid request method']);
        }

        try {
            $result_id = $_GET['id'] ?? null;
            if (!$result_id) {
                throw new Exception('Result ID is required');
            }

            $result = $this->matchResultModel->getById($result_id);
            if (!$result) {
                throw new Exception('Match result not found');
            }

            $this->respondJson([
                'success' => true,
                'result' => $result
            ]);
        } catch (Exception $e) {
            $this->respondJson([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function getTeamMembers()
    {
        // Clear all output buffers
        while (ob_get_level() > 0) {
            ob_end_clean();
        }
        
        // Set JSON headers before any other output
        header('Content-Type: application/json');
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');
        
        try {
            $this->requireAuth();

            if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
                echo json_encode(['success' => false, 'members' => []]);
                exit;
            }

            $team_id = $this->getPresentTeamId();
            $search = trim($_GET['q'] ?? '');

            // Get players from the current team only
            $query = "SELECT DISTINCT 
                        p.player_id,
                        CONCAT(COALESCE(u.first_name, 'Unknown'), ' ', COALESCE(u.last_name, '')) as full_name,
                        u.first_name,
                        u.last_name,
                        p.position
                      FROM team_players tp
                      INNER JOIN players p ON tp.player_id = p.player_id
                      LEFT JOIN users u ON p.nic = u.nic
                      WHERE tp.team_id = :team_id";
            
            $params = ['team_id' => $team_id];

            // Add search filter if provided
            if (!empty($search)) {
                $query .= " AND (u.first_name LIKE :search_first OR u.last_name LIKE :search_last)";
                $searchTerm = "%{$search}%";
                $params['search_first'] = $searchTerm;
                $params['search_last'] = $searchTerm;
            }

            $query .= " ORDER BY u.first_name, u.last_name LIMIT 20";

            $members = $this->playerModel->query($query, $params);

            echo json_encode([
                'success' => true,
                'members' => $members ?? [],
                'debug' => [
                    'team_id' => $team_id,
                    'count' => count($members ?? [])
                ]
            ]);
            exit;
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'members' => [],
                'error' => $e->getMessage()
            ]);
            exit;
        }
    }

    /**
     * Add player match statistics - AJAX endpoint
     */
    public function addPlayerMatchStats()
    {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->respondJson(['success' => false, 'message' => 'Invalid request method']);
        }

        try {
            $playerStatsModel = $this->model('PlayerMatchStatsModel');

            $data = [
                'match_id' => (int)($_POST['match_id'] ?? 0),
                'player_id' => (int)($_POST['player_id'] ?? 0),
                'position_played' => trim($_POST['position_played'] ?? ''),
                'minutes_played' => (int)($_POST['minutes_played'] ?? 0),
                'substitution_status' => trim($_POST['substitution_status'] ?? 'Started'),
                'goals_scored' => (int)($_POST['goals_scored'] ?? 0),
                'assists' => (int)($_POST['assists'] ?? 0),
                'shots_on_target' => (int)($_POST['shots_on_target'] ?? 0),
                'shots_off_target' => (int)($_POST['shots_off_target'] ?? 0),
                'key_passes' => (int)($_POST['key_passes'] ?? 0),
                'successful_dribbles' => (int)($_POST['successful_dribbles'] ?? 0),
                'completed_passes' => (int)($_POST['completed_passes'] ?? 0),
                'line_breaking_passes' => (int)($_POST['line_breaking_passes'] ?? 0),
                'tackles_won' => (int)($_POST['tackles_won'] ?? 0),
                'interceptions' => (int)($_POST['interceptions'] ?? 0),
                'defensive_duels_won' => (int)($_POST['defensive_duels_won'] ?? 0),
                'aerial_duels_won' => (int)($_POST['aerial_duels_won'] ?? 0),
                'yellow_cards' => (int)($_POST['yellow_cards'] ?? 0),
                'red_cards' => (int)($_POST['red_cards'] ?? 0),
                'fouls_committed' => (int)($_POST['fouls_committed'] ?? 0),
                'fouls_won' => (int)($_POST['fouls_won'] ?? 0),
                'notes' => trim($_POST['notes'] ?? '')
            ];

            // Validate required fields
            if (empty($data['match_id']) || empty($data['player_id'])) {
                throw new Exception('Match and Player are required');
            }

            if (!in_array($data['substitution_status'], ['Started', 'Substitute', 'Unused'])) {
                throw new Exception('Invalid substitution status');
            }

            // Check if stats already exist for this player in this match
            if ($playerStatsModel->exists($data['match_id'], $data['player_id'])) {
                throw new Exception('Player statistics already exist for this match');
            }

            $playerStatsModel->create($data);

            $this->respondJson([
                'success' => true,
                'message' => 'Player match statistics added successfully'
            ]);
        } catch (Exception $e) {
            $this->respondJson([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get player match statistics - AJAX endpoint
     */
    public function getPlayerMatchStats()
    {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            $this->respondJson(['success' => false, 'message' => 'Invalid request method']);
        }

        try {
            $playerStatsModel = $this->model('PlayerMatchStatsModel');
            $stat_id = $_GET['id'] ?? null;

            if (!$stat_id) {
                throw new Exception('Stat ID is required');
            }

            $stats = $playerStatsModel->getById($stat_id);
            if (!$stats) {
                throw new Exception('Player match statistics not found');
            }

            $this->respondJson([
                'success' => true,
                'stats' => $stats
            ]);
        } catch (Exception $e) {
            $this->respondJson([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Edit player match statistics - AJAX endpoint
     */
    public function editPlayerMatchStats()
    {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->respondJson(['success' => false, 'message' => 'Invalid request method']);
        }

        try {
            $playerStatsModel = $this->model('PlayerMatchStatsModel');
            $stat_id = $_POST['stat_id'] ?? null;

            if (!$stat_id) {
                throw new Exception('Stat ID is required');
            }

            $data = [
                'position_played' => trim($_POST['position_played'] ?? ''),
                'minutes_played' => (int)($_POST['minutes_played'] ?? 0),
                'substitution_status' => trim($_POST['substitution_status'] ?? 'Started'),
                'goals_scored' => (int)($_POST['goals_scored'] ?? 0),
                'assists' => (int)($_POST['assists'] ?? 0),
                'shots_on_target' => (int)($_POST['shots_on_target'] ?? 0),
                'shots_off_target' => (int)($_POST['shots_off_target'] ?? 0),
                'key_passes' => (int)($_POST['key_passes'] ?? 0),
                'successful_dribbles' => (int)($_POST['successful_dribbles'] ?? 0),
                'completed_passes' => (int)($_POST['completed_passes'] ?? 0),
                'line_breaking_passes' => (int)($_POST['line_breaking_passes'] ?? 0),
                'tackles_won' => (int)($_POST['tackles_won'] ?? 0),
                'interceptions' => (int)($_POST['interceptions'] ?? 0),
                'defensive_duels_won' => (int)($_POST['defensive_duels_won'] ?? 0),
                'aerial_duels_won' => (int)($_POST['aerial_duels_won'] ?? 0),
                'yellow_cards' => (int)($_POST['yellow_cards'] ?? 0),
                'red_cards' => (int)($_POST['red_cards'] ?? 0),
                'fouls_committed' => (int)($_POST['fouls_committed'] ?? 0),
                'fouls_won' => (int)($_POST['fouls_won'] ?? 0),
                'notes' => trim($_POST['notes'] ?? '')
            ];

            if (!in_array($data['substitution_status'], ['Started', 'Substitute', 'Unused'])) {
                throw new Exception('Invalid substitution status');
            }

            $playerStatsModel->update($stat_id, $data);

            $this->respondJson([
                'success' => true,
                'message' => 'Player match statistics updated successfully'
            ]);
        } catch (Exception $e) {
            $this->respondJson([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Delete player match statistics - AJAX endpoint
     */
    public function deletePlayerMatchStats()
    {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->respondJson(['success' => false, 'message' => 'Invalid request method']);
        }

        try {
            $playerStatsModel = $this->model('PlayerMatchStatsModel');
            $stat_id = $_POST['stat_id'] ?? null;

            if (!$stat_id) {
                throw new Exception('Stat ID is required');
            }

            $playerStatsModel->delete($stat_id);

            $this->respondJson([
                'success' => true,
                'message' => 'Player match statistics deleted successfully'
            ]);
        } catch (Exception $e) {
            $this->respondJson([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get players for match selection
     */
    public function getPlayersForMatch()
    {
        while (ob_get_level() > 0) {
            ob_end_clean();
        }
        
        header('Content-Type: application/json');
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');
        
        try {
            $this->requireAuth();

            if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
                echo json_encode(['success' => false, 'players' => []]);
                exit;
            }

            $match_id = $_GET['match_id'] ?? null;
            if (!$match_id) {
                echo json_encode(['success' => false, 'players' => []]);
                exit;
            }

            $team_id = $this->getPresentTeamId();

            // Get all players from the team
            $query = "SELECT 
                        p.player_id,
                        CONCAT(u.first_name, ' ', u.last_name) as full_name,
                        p.position,
                        CASE WHEN pms.stat_id IS NOT NULL THEN 1 ELSE 0 END as has_stats
                      FROM team_players tp
                      INNER JOIN players p ON tp.player_id = p.player_id
                      LEFT JOIN users u ON p.nic = u.nic
                      LEFT JOIN player_match_stats pms ON pms.match_id = :match_id AND pms.player_id = p.player_id
                      WHERE tp.team_id = :team_id
                      ORDER BY u.first_name, u.last_name";

            $players = $this->playerModel->query($query, [
                'team_id' => $team_id,
                'match_id' => (int)$match_id
            ]);

            echo json_encode([
                'success' => true,
                'players' => $players ?? []
            ]);
            exit;
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'players' => [],
                'error' => $e->getMessage()
            ]);
            exit;
        }
    }

    /**
     * Get all player stats for a match
     */
    public function getMatchPlayerStats()
    {
        while (ob_get_level() > 0) {
            ob_end_clean();
        }
        
        header('Content-Type: application/json');
        
        try {
            $this->requireAuth();

            if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
                echo json_encode(['success' => false, 'stats' => []]);
                exit;
            }

            $match_id = $_GET['match_id'] ?? null;
            if (!$match_id) {
                echo json_encode(['success' => false, 'stats' => []]);
                exit;
            }

            $playerStatsModel = $this->model('PlayerMatchStatsModel');
            $stats = $playerStatsModel->getByMatchId($match_id);

            echo json_encode([
                'success' => true,
                'stats' => $stats ?? []
            ]);
            exit;
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'stats' => [],
                'error' => $e->getMessage()
            ]);
            exit;
        }
    }

    /**
     * Get single player match stat - AJAX endpoint
     */
    public function getPlayerStat()
    {
        while (ob_get_level() > 0) {
            ob_end_clean();
        }
        
        header('Content-Type: application/json');
        
        try {
            $this->requireAuth();

            if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
                echo json_encode(['success' => false, 'stat' => null]);
                exit;
            }

            $stat_id = $_GET['stat_id'] ?? null;
            if (!$stat_id) {
                echo json_encode(['success' => false, 'stat' => null]);
                exit;
            }

            $playerStatsModel = $this->model('PlayerMatchStatsModel');
            $stat = $playerStatsModel->getById($stat_id);

            echo json_encode([
                'success' => true,
                'stat' => $stat ?? null
            ]);
            exit;
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'stat' => null,
                'error' => $e->getMessage()
            ]);
            exit;
        }
    }

    /**
     * Update player match stats - alias for editPlayerMatchStats
     */
    public function updatePlayerMatchStats()
    {
        $this->editPlayerMatchStats();
    }
}
