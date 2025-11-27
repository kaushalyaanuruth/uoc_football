<?php
class teamManagement extends Controller {

    private $teamModel;
    private $playerModel;
    private $coachModel;
    private $tournamentModel;
    private $achievementModel;
    private $userModel;
    
    public function __construct() {
        $this->teamModel = $this->model('TeamModel');
        $this->playerModel = $this->model('PlayerModel');
        $this->coachModel = $this->model('CoachModel');
        $this->tournamentModel = $this->model('TournamentModel');
        $this->achievementModel = $this->model('AchievementModel');
        $this->userModel = $this->model('User');
    }

    public function index() {
        // Initialize session for players and coaches if not exists
        if (!isset($_SESSION['temp_players'])) {
            $_SESSION['temp_players'] = [];
        }
        if (!isset($_SESSION['temp_coaches'])) {
            $_SESSION['temp_coaches'] = [];
        }
        
        // Get all teams with player counts
        try {
            // Test basic query first
            $testQuery = $this->teamModel->getAll();
            error_log("Basic getAll() result: " . print_r($testQuery, true));
            
            $teams = $this->teamModel->getAllWithPlayersCounts();
            
            // Debug logging
            error_log("Teams query result: " . print_r($teams, true));
            error_log("Teams count: " . (is_array($teams) ? count($teams) : 'not an array'));
            error_log("Teams empty? " . (empty($teams) ? 'YES' : 'NO'));
            
            // Initialize data array
            $data = [];
            $data['teams'] = $teams ? $teams : [];
            
            error_log("Data being passed to view: " . print_r($data['teams'], true));
            
        } catch (Exception $e) {
            error_log("Error fetching teams: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            $data = [];
            $data['teams'] = [];
        }
        
        $this->view('teamManagement', $data);
    }

    public function addPlayer() {
        // Set header for JSON response
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // Initialize session array if not exists
                if (!isset($_SESSION['temp_players'])) {
                    $_SESSION['temp_players'] = [];
                }
                
                // Check player limit
                if (count($_SESSION['temp_players']) >= 30) {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Maximum of 30 players allowed per team'
                    ]);
                    exit;
                }
                
                // Handle image upload
                $imagePath = '';
                if (isset($_FILES['player_image']) && $_FILES['player_image']['error'] === UPLOAD_ERR_OK) {
                    $uploadDir = 'uploads/players/';
                    // Create directory if it doesn't exist
                    if (!file_exists($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }
                    
                    $fileExtension = pathinfo($_FILES['player_image']['name'], PATHINFO_EXTENSION);
                    $fileName = uniqid('player_') . '.' . $fileExtension;
                    $uploadPath = $uploadDir . $fileName;
                    
                    if (move_uploaded_file($_FILES['player_image']['tmp_name'], $uploadPath)) {
                        $imagePath = $uploadPath;
                    }
                }
                
                // Get player data
                $playerData = [
                    'full_name' => $_POST['full_name'] ?? '',
                    'name_with_initials' => $_POST['name_with_initials'] ?? '',
                    'faculty' => $_POST['faculty'] ?? '',
                    'position' => $_POST['position'] ?? '',
                    'role' => $_POST['role'] ?? 'player',
                    'jersey_number' => $_POST['jersey_number'] ?? '',
                    'nic' => $_POST['nic'] ?? '',
                    'uni_register_number' => $_POST['uni_register_number'] ?? '',
                    'mobile_number' => $_POST['mobile_number'] ?? '',
                    'address' => $_POST['address'] ?? '',
                    'height' => $_POST['height'] ?? '',
                    'weight' => $_POST['weight'] ?? '',
                    'image' => $imagePath
                ];
                
                // Validate required fields
                if (empty($playerData['full_name']) || empty($playerData['position'])) {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Full name and position are required'
                    ]);
                    exit;
                }
                
                // Add to session
                $_SESSION['temp_players'][] = $playerData;
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Player added successfully',
                    'player' => $playerData,
                    'total_players' => count($_SESSION['temp_players'])
                ]);
                
            } catch (Exception $e) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Error: ' . $e->getMessage()
                ]);
            }
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Invalid request method'
            ]);
        }
        exit;
    }

    public function addCoach() {
        // Set header for JSON response
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // Initialize session array if not exists
                if (!isset($_SESSION['temp_coaches'])) {
                    $_SESSION['temp_coaches'] = [];
                }
                
                // Check coach limit
                if (count($_SESSION['temp_coaches']) >= 5) {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Maximum of 5 coaches allowed per team'
                    ]);
                    exit;
                }
                
                // Handle image upload
                $imagePath = '';
                if (isset($_FILES['coach_image']) && $_FILES['coach_image']['error'] === UPLOAD_ERR_OK) {
                    $uploadDir = 'uploads/coaches/';
                    // Create directory if it doesn't exist
                    if (!file_exists($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }
                    
                    $fileExtension = pathinfo($_FILES['coach_image']['name'], PATHINFO_EXTENSION);
                    $fileName = uniqid('coach_') . '.' . $fileExtension;
                    $uploadPath = $uploadDir . $fileName;
                    
                    if (move_uploaded_file($_FILES['coach_image']['tmp_name'], $uploadPath)) {
                        $imagePath = $uploadPath;
                    }
                }
                
                // Get coach data
                $coachData = [
                    'full_name' => $_POST['full_name'] ?? '',
                    'name_with_initials' => $_POST['name_with_initials'] ?? '',
                    'age' => $_POST['age'] ?? '',
                    'nic' => $_POST['nic'] ?? '',
                    'phone_number' => $_POST['phone_number'] ?? '',
                    'address' => $_POST['address'] ?? '',
                    'licence' => $_POST['licence'] ?? '',
                    'image' => $imagePath
                ];
                
                // Validate required fields
                if (empty($coachData['full_name']) || empty($coachData['licence'])) {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Full name and licence are required'
                    ]);
                    exit;
                }
                
                // Add to session
                $_SESSION['temp_coaches'][] = $coachData;
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Coach added successfully',
                    'coach' => $coachData,
                    'total_coaches' => count($_SESSION['temp_coaches'])
                ]);
                
            } catch (Exception $e) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Error: ' . $e->getMessage()
                ]);
            }
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Invalid request method'
            ]);
        }
        exit;
    }

    public function removeCoach() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $index = $_POST['index'] ?? -1;
            
            if (isset($_SESSION['temp_coaches'][$index])) {
                array_splice($_SESSION['temp_coaches'], $index, 1);
                echo json_encode([
                    'success' => true,
                    'message' => 'Coach removed successfully',
                    'total_coaches' => count($_SESSION['temp_coaches'])
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Coach not found'
                ]);
            }
        }
        exit;
    }

    public function getCoaches() {
        header('Content-Type: application/json');
        
        $coaches = $_SESSION['temp_coaches'] ?? [];
        echo json_encode([
            'success' => true,
            'coaches' => $coaches
        ]);
        exit;
    }

    public function removePlayer() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $index = $_POST['index'] ?? -1;
            
            if (isset($_SESSION['temp_players'][$index])) {
                array_splice($_SESSION['temp_players'], $index, 1);
                echo json_encode([
                    'success' => true,
                    'message' => 'Player removed successfully',
                    'total_players' => count($_SESSION['temp_players'])
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Player not found'
                ]);
            }
        }
        exit;
    }

    public function getPlayers() {
        header('Content-Type: application/json');
        
        $players = $_SESSION['temp_players'] ?? [];
        echo json_encode([
            'success' => true,
            'players' => $players
        ]);
        exit;
    }

    public function add() {
        // Set header for JSON response
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // Get form data
                $team_name = $_POST['team_name'] ?? '';
                $season = $_POST['season'] ?? '';
                $team_status = $_POST['team_status'] ?? 'present';
                
                // Get arrays
                $tournaments = $_POST['tournaments'] ?? [];
                $achievements = $_POST['achievements'] ?? [];
                
                // Get players and coaches from session
                $players = $_SESSION['temp_players'] ?? [];
                $coaches = $_SESSION['temp_coaches'] ?? [];
                
                // Validate required fields
                if (empty($team_name) || empty($season)) {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Team name and season are required'
                    ]);
                    exit;
                }
                
                // Insert team
                $teamData = [
                    'team_name' => $team_name,
                    'season' => $season,
                    'team_status' => $team_status
                ];
                
                // Debug: Log session state
                error_log("=== TEAM CREATION DEBUG ===");
                error_log("Session user_id exists: " . (isset($_SESSION['user_id']) ? 'YES' : 'NO'));
                error_log("Session user_id value: " . ($_SESSION['user_id'] ?? 'NOT SET'));
                error_log("Session user_id empty: " . (empty($_SESSION['user_id']) ? 'YES' : 'NO'));
                
                // Only add created_by if user is logged in AND has a NUMERIC ID (valid user)
                if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id']) && is_numeric($_SESSION['user_id'])) {
                    $teamData['created_by'] = (int)$_SESSION['user_id'];
                    error_log("Adding created_by to teamData: " . $_SESSION['user_id']);
                } else {
                    error_log("NOT adding created_by - user not logged in or invalid user ID");
                    error_log("Session user_id is numeric: " . (is_numeric($_SESSION['user_id'] ?? '') ? 'YES' : 'NO'));
                }
                
                error_log("Team data before create: " . json_encode($teamData));
                
                $result = $this->teamModel->create($teamData);
                $team_id = $this->teamModel->lastInsertId();
                
                if (!$team_id) {
                    throw new Exception("Failed to create team - no ID returned");
                }
                
                // Insert tournaments
                foreach ($tournaments as $tournament) {
                    if (!empty($tournament)) {
                        $tournamentData = [
                            'team_id' => $team_id,
                            'tournament_name' => $tournament,
                            'tournament_date' => null,
                            'location' => null
                        ];
                        $this->tournamentModel->create($tournamentData);
                    }
                }
                
                // Insert achievements
                foreach ($achievements as $achievement) {
                    if (!empty($achievement)) {
                        $achievementData = [
                            'team_id' => $team_id,
                            'achievement_title' => $achievement,
                            'achievement_date' => null,
                            'description' => null
                        ];
                        $this->achievementModel->create($achievementData);
                    }
                }
                
                // Insert players
                $playersCreated = 0;
                $usersCreated = 0;
                
                foreach ($players as $player) {
                    $playerData = [
                        'team_id' => $team_id,
                        'full_name' => $player['full_name'],
                        'name_with_initials' => $player['name_with_initials'],
                        'faculty' => $player['faculty'],
                        'position' => $player['position'],
                        'role' => $player['role'],
                        'jersey_number' => $player['jersey_number'],
                        'nic' => $player['nic'],
                        'uni_register_number' => $player['uni_register_number'],
                        'mobile_number' => $player['mobile_number'],
                        'address' => $player['address'],
                        'height' => $player['height'] ?? null,
                        'weight' => $player['weight'] ?? null,
                        'image' => $player['image'] ?? ''
                    ];
                    $this->playerModel->create($playerData);
                    $playersCreated++;
                    
                    // Create user account for player
                    // Generate email from full name or use a default
                    $email = strtolower(str_replace(' ', '.', $player['full_name'])) . '@uocfootball.com';
                    $userResult = $this->userModel->createPlayerUser(
                        $player['nic'], 
                        $player['full_name'], 
                        $email
                    );
                    
                    if ($userResult['success']) {
                        $usersCreated++;
                    } else {
                        error_log("Warning: Could not create user account for player {$player['full_name']}: {$userResult['message']}");
                    }
                }
                
                // Insert coaches
                $coachesCreated = 0;
                $coachUsersCreated = 0;
                
                foreach ($coaches as $coach) {
                    $coachData = [
                        'full_name' => $coach['full_name'],
                        'name_with_initials' => $coach['name_with_initials'],
                        'age' => $coach['age'],
                        'nic' => $coach['nic'],
                        'phone_number' => $coach['phone_number'],
                        'address' => $coach['address'],
                        'licence' => $coach['licence'],
                        'image' => $coach['image'] ?? ''
                    ];
                    $this->coachModel->create($coachData);
                    $coachesCreated++;
                    
                    $coach_id = $this->coachModel->lastInsertId();
                    $this->coachModel->assignToTeam($coach_id, $team_id);
                    
                    // Create user account for coach
                    $email = strtolower(str_replace(' ', '.', $coach['full_name'])) . '@uocfootball.com';
                    $userResult = $this->userModel->createCoachUser(
                        $coach['nic'], 
                        $coach['full_name'],
                        $coach['phone_number'],
                        $email
                    );
                    
                    if ($userResult['success']) {
                        $coachUsersCreated++;
                    } else {
                        error_log("Warning: Could not create user account for coach {$coach['full_name']}: {$userResult['message']}");
                    }
                }
                
                // Clear session players and coaches after successful team creation
                $_SESSION['temp_players'] = [];
                $_SESSION['temp_coaches'] = [];
                
                // Build success message
                $message = "Team added successfully!";
                
                if ($playersCreated > 0) {
                    $message .= " {$playersCreated} player(s) added.";
                    if ($usersCreated > 0) {
                        $message .= " {$usersCreated} player account(s) created.";
                    }
                }
                
                if ($coachesCreated > 0) {
                    $message .= " {$coachesCreated} coach(es) added.";
                    if ($coachUsersCreated > 0) {
                        $message .= " {$coachUsersCreated} coach account(s) created.";
                    }
                }
                
                if ($usersCreated > 0 || $coachUsersCreated > 0) {
                    $message .= " All accounts use username=NIC and password='123456'.";
                }
                
                echo json_encode([
                    'success' => true,
                    'message' => $message,
                    'team_id' => $team_id,
                    'players_created' => $playersCreated,
                    'coaches_created' => $coachesCreated,
                    'player_accounts_created' => $usersCreated,
                    'coach_accounts_created' => $coachUsersCreated
                ]);
                
            } catch (Exception $e) {
                error_log("Team creation error: " . $e->getMessage());
                
                echo json_encode([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
            }
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Invalid request method'
            ]);
        }
        exit;
    }
    
    /**
     * Get team by ID
     */
    public function getTeamById($id) {
        header('Content-Type: application/json');
        
        try {
            $team = $this->teamModel->getById($id);
            
            if ($team) {
                // Get tournaments
                $tournaments = $this->tournamentModel->getByTeamId($id);
                $tournamentNames = array_map(function($t) { return $t->tournament_name; }, $tournaments);
                
                // Get achievements
                $achievements = $this->achievementModel->getByTeamId($id);
                $achievementTitles = array_map(function($a) { return $a->achievement_title; }, $achievements);
                
                // Get players count
                $playersCount = $this->playerModel->countByTeam($id);
                
                // Get coaches
                $coaches = $this->coachModel->getByTeamId($id);
                $coachNames = array_map(function($c) { return $c->full_name; }, $coaches);
                
                echo json_encode([
                    'success' => true,
                    'team' => [
                        'id' => $team->id,
                        'team_name' => $team->team_name,
                        'season' => $team->season,
                        'team_status' => $team->team_status,
                        'tournaments' => implode("\n", $tournamentNames), // Newline separated for textarea
                        'achievements' => implode("\n", $achievementTitles), // Newline separated for textarea
                        'coach' => implode(', ', $coachNames),
                        'players' => $playersCount
                    ]
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Team not found'
                ]);
            }
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
        exit;
    }
    
    /**
     * Update team
     */
    public function updateTeam() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $team_id = $_POST['team_id'] ?? 0;
                
                if (empty($team_id)) {
                    echo json_encode(['success' => false, 'message' => 'Team ID is required']);
                    exit;
                }
                
                // Update team basic info
                $teamData = [
                    'team_name' => $_POST['team_name'] ?? '',
                    'season' => $_POST['season'] ?? '',
                    'team_status' => $_POST['team_status'] ?? 'present'
                ];
                
                $this->teamModel->update($team_id, $teamData);
                
                // Update tournaments
                if (isset($_POST['tournaments'])) {
                    // Delete existing tournaments
                    $this->tournamentModel->deleteByTeamId($team_id);
                    
                    // Add new tournaments
                    $tournaments = explode("\n", $_POST['tournaments']);
                    foreach ($tournaments as $tournament) {
                        $tournament = trim($tournament);
                        if (!empty($tournament)) {
                            $this->tournamentModel->create([
                                'team_id' => $team_id,
                                'tournament_name' => $tournament,
                                'tournament_date' => null,
                                'location' => null
                            ]);
                        }
                    }
                }
                
                // Update achievements
                if (isset($_POST['achievements'])) {
                    // Delete existing achievements
                    $this->achievementModel->deleteByTeamId($team_id);
                    
                    // Add new achievements
                    $achievements = explode("\n", $_POST['achievements']);
                    foreach ($achievements as $achievement) {
                        $achievement = trim($achievement);
                        if (!empty($achievement)) {
                            $this->achievementModel->create([
                                'team_id' => $team_id,
                                'achievement_title' => $achievement,
                                'achievement_date' => null,
                                'description' => null
                            ]);
                        }
                    }
                }
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Team updated successfully'
                ]);
            } catch (Exception $e) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Error: ' . $e->getMessage()
                ]);
            }
        }
        exit;
    }
    
    /**
     * Delete team
     */
    public function deleteTeam() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $team_id = $_POST['id'] ?? 0;
                
                if (empty($team_id)) {
                    echo json_encode(['success' => false, 'message' => 'Team ID is required']);
                    exit;
                }
                
                // Delete team (cascades to players, tournaments, achievements)
                $this->teamModel->delete($team_id);
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Team deleted successfully'
                ]);
            } catch (Exception $e) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Error: ' . $e->getMessage()
                ]);
            }
        }
        exit;
    }
    
    /**
     * Get players by team ID
     */
    public function getTeamPlayers($teamId) {
        header('Content-Type: application/json');
        
        try {
            $players = $this->playerModel->getByTeamId($teamId);
            echo json_encode([
                'success' => true,
                'players' => $players
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
        exit;
    }
    
    /**
     * Get coaches by team ID
     */
    public function getTeamCoaches($teamId) {
        header('Content-Type: application/json');
        
        try {
            $coaches = $this->coachModel->getByTeamId($teamId);
            echo json_encode([
                'success' => true,
                'coaches' => $coaches
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
        exit;
    }
    
    /**
     * Get player by ID
     */
    public function getPlayerById($id) {
        header('Content-Type: application/json');
        
        try {
            $player = $this->playerModel->getById($id);
            if ($player) {
                echo json_encode([
                    'success' => true,
                    'player' => $player
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Player not found'
                ]);
            }
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
        exit;
    }
    
    /**
     * Get coach by ID
     */
    public function getCoachById($id) {
        header('Content-Type: application/json');
        
        try {
            $coach = $this->coachModel->getById($id);
            if ($coach) {
                echo json_encode([
                    'success' => true,
                    'coach' => $coach
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Coach not found'
                ]);
            }
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
        exit;
    }
    
    /**
     * Update player
     */
    public function updatePlayer() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $id = $_POST['id'] ?? 0;
                if (empty($id)) {
                    echo json_encode(['success' => false, 'message' => 'Player ID is required']);
                    exit;
                }
                
                // Collect all fields from the form
                $data = [];
                if (isset($_POST['full_name'])) $data['full_name'] = $_POST['full_name'];
                if (isset($_POST['name_with_initials'])) $data['name_with_initials'] = $_POST['name_with_initials'];
                if (isset($_POST['age'])) $data['age'] = $_POST['age'];
                if (isset($_POST['date_of_birth'])) $data['date_of_birth'] = $_POST['date_of_birth'];
                if (isset($_POST['nic'])) $data['nic'] = $_POST['nic'];
                if (isset($_POST['phone_number'])) $data['phone_number'] = $_POST['phone_number'];
                if (isset($_POST['address'])) $data['address'] = $_POST['address'];
                if (isset($_POST['position'])) $data['position'] = $_POST['position'];
                if (isset($_POST['jersey_number'])) $data['jersey_number'] = $_POST['jersey_number'];
                if (isset($_POST['role'])) $data['role'] = $_POST['role'];
                if (isset($_POST['batting_style'])) $data['batting_style'] = $_POST['batting_style'];
                if (isset($_POST['bowling_style'])) $data['bowling_style'] = $_POST['bowling_style'];
                if (isset($_POST['wicket_keeping'])) $data['wicket_keeping'] = $_POST['wicket_keeping'];
                
                $this->playerModel->update($id, $data);
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Player updated successfully'
                ]);
            } catch (Exception $e) {
                echo json_encode([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
            }
        }
        exit;
    }
    
    /**
     * Update coach
     */
    public function updateCoach() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $id = $_POST['id'] ?? 0;
                if (empty($id)) {
                    echo json_encode(['success' => false, 'message' => 'Coach ID is required']);
                    exit;
                }
                
                // Collect all fields from the form
                $data = [];
                if (isset($_POST['full_name'])) $data['full_name'] = $_POST['full_name'];
                if (isset($_POST['name_with_initials'])) $data['name_with_initials'] = $_POST['name_with_initials'];
                if (isset($_POST['age'])) $data['age'] = $_POST['age'];
                if (isset($_POST['nic'])) $data['nic'] = $_POST['nic'];
                if (isset($_POST['phone_number'])) $data['phone_number'] = $_POST['phone_number'];
                if (isset($_POST['address'])) $data['address'] = $_POST['address'];
                if (isset($_POST['licence'])) $data['licence'] = $_POST['licence'];
                
                $this->coachModel->update($id, $data);
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Coach updated successfully'
                ]);
            } catch (Exception $e) {
                echo json_encode([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
            }
        }
        exit;
    }
    
    /**
     * Add player directly to an existing team
     */
    public function addPlayerToTeam() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $team_id = $_POST['team_id'] ?? 0;
                
                if (empty($team_id)) {
                    echo json_encode(['success' => false, 'message' => 'Team ID is required']);
                    exit;
                }
                
                // Validate required fields
                $required = ['full_name', 'name_with_initials', 'faculty', 'position', 'jersey_number', 
                            'nic', 'uni_register_number', 'mobile_number'];
                foreach ($required as $field) {
                    if (empty($_POST[$field])) {
                        echo json_encode(['success' => false, 'message' => ucfirst(str_replace('_', ' ', $field)) . ' is required']);
                        exit;
                    }
                }
                
                // Create player data with all required fields
                $playerData = [
                    'team_id' => $team_id,
                    'full_name' => $_POST['full_name'],
                    'name_with_initials' => $_POST['name_with_initials'],
                    'faculty' => $_POST['faculty'],
                    'position' => $_POST['position'],
                    'role' => $_POST['role'] ?? 'player',
                    'jersey_number' => $_POST['jersey_number'],
                    'nic' => $_POST['nic'],
                    'uni_register_number' => $_POST['uni_register_number'],
                    'mobile_number' => $_POST['mobile_number'],
                    'address' => $_POST['address'] ?? '',
                    'height' => $_POST['height'] ?? null,
                    'weight' => $_POST['weight'] ?? null,
                    'image' => $_POST['image'] ?? ''
                ];
                
                // Insert player
                $this->playerModel->create($playerData);
                $player_id = $this->playerModel->lastInsertId();
                
                // Create user account for the player
                try {
                    $username = $playerData['nic'];
                    $password = '123456';
                    $this->userModel->createPlayerUser($username, $password, $player_id);
                } catch (Exception $e) {
                    error_log("Failed to create user account for player: " . $e->getMessage());
                }
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Player added successfully',
                    'player' => $playerData
                ]);
            } catch (Exception $e) {
                echo json_encode([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
            }
        }
        exit;
    }
    
    /**
     * Add coach directly to an existing team
     */
    public function addCoachToTeam() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $team_id = $_POST['team_id'] ?? 0;
                
                if (empty($team_id)) {
                    echo json_encode(['success' => false, 'message' => 'Team ID is required']);
                    exit;
                }
                
                // Validate required fields
                $required = ['full_name', 'name_with_initials', 'age', 'nic', 'phone_number', 'address', 'licence'];
                foreach ($required as $field) {
                    if (!isset($_POST[$field]) || $_POST[$field] === '') {
                        echo json_encode(['success' => false, 'message' => ucfirst(str_replace('_', ' ', $field)) . ' is required']);
                        exit;
                    }
                }
                
                // Create coach data
                $coachData = [
                    'full_name' => $_POST['full_name'],
                    'name_with_initials' => $_POST['name_with_initials'],
                    'age' => $_POST['age'],
                    'nic' => $_POST['nic'],
                    'phone_number' => $_POST['phone_number'],
                    'address' => $_POST['address'],
                    'licence' => $_POST['licence'],
                    'image' => $_POST['image'] ?? ''
                ];
                
                // Insert coach
                $this->coachModel->create($coachData);
                $coach_id = $this->coachModel->lastInsertId();
                
                // Assign coach to team (many-to-many relationship)
                $this->coachModel->assignToTeam($coach_id, $team_id);
                
                // Create user account for the coach
                try {
                    $username = $coachData['nic'];
                    $password = '123456';
                    $this->userModel->createCoachUser($username, $password, $coach_id);
                } catch (Exception $e) {
                    error_log("Failed to create user account for coach: " . $e->getMessage());
                }
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Coach added successfully',
                    'coach' => $coachData
                ]);
            } catch (Exception $e) {
                echo json_encode([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
            }
        }
        exit;
    }
    
    /**
     * Delete player
     */
    public function deletePlayer() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $id = $_POST['id'] ?? 0;
                if (empty($id)) {
                    echo json_encode(['success' => false, 'message' => 'Player ID is required']);
                    exit;
                }
                
                $this->playerModel->delete($id);
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Player deleted successfully'
                ]);
            } catch (Exception $e) {
                echo json_encode([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
            }
        }
        exit;
    }
    
    /**
     * Delete coach
     */
    public function deleteCoach() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $id = $_POST['id'] ?? 0;
                if (empty($id)) {
                    echo json_encode(['success' => false, 'message' => 'Coach ID is required']);
                    exit;
                }
                
                $this->coachModel->delete($id);
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Coach deleted successfully'
                ]);
            } catch (Exception $e) {
                echo json_encode([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
            }
        }
        exit;
    }
}
