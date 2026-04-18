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
        if (!isset($_SESSION['temp_players'])) {
            $_SESSION['temp_players'] = [];
        }
        if (!isset($_SESSION['temp_coaches'])) {
            $_SESSION['temp_coaches'] = [];
        }
        
        try {
            $teams = $this->teamModel->getAllWithPlayersCounts();
        
            $teams = is_array($teams) ? $teams : [];
            foreach ($teams as $team) {

                $tournaments = $this->tournamentModel->getByTeamId($team->team_id);
                $team->tournaments = $tournaments ?: [];

                $achievements = $this->achievementModel->getByTeamId($team->team_id);
                $team->achievements = $achievements ?: [];
                
                $coaches = $this->coachModel->getByTeamId($team->team_id);
  
                $team->coaches = $coaches ?: [];
      

                $team->coach_names = implode(', ', array_map(function($c) { 
                    return (isset($c->first_name) && isset($c->last_name)) ? $c->first_name . ' ' . $c->last_name : ''; 
                }, $coaches));


                
                if (!isset($team->players_count)) {
                    $players = $this->playerModel->getByTeamId($team->team_id);
                    $team->players_count = is_array($players) ? count($players) : 0;
                }
            }
            $data = ['teams' => $teams];
            
        } catch (Exception $e) {
            error_log("Error fetching teams: " . $e->getMessage());
            $data = ['teams' => []];
        }
        
        $this->view('teamManagement', $data);
    }

    public function create() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit;
        }
        
        try {

            $teamData = [
                'season' => trim($_POST['season'] ?? ''),
                'status' => $_POST['status'] ?? 'present'
            ];

            if (empty($teamData['season'])) {
                echo json_encode(['success' => false, 'message' => 'Season is required']);
                exit;
            }

            // Add created_by if user is logged in
            if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id']) && is_numeric($_SESSION['user_id'])) {
                $teamData['created_by'] = (int)$_SESSION['user_id'];
            }

            // Create the team
            $this->teamModel->create($teamData);
            $teamId = $this->teamModel->lastInsertId();
            
            if (!$teamId) {
                echo json_encode(['success' => false, 'message' => 'Failed to create team']);
                exit;
            }

            // Add tournaments if provided
            if (isset($_POST['name']) && is_array($_POST['name'])) {
                foreach ($_POST['name'] as $tournament) {
                    $tournament = trim($tournament);
                    if (!empty($tournament)) {
                        $this->tournamentModel->create([
                            'name' => $tournament,
                            'team_id' => $teamId
                        ]);
                    }
                }
            }

            // Add achievements if provided
            if (isset($_POST['achievement']) && is_array($_POST['achievement'])) {
                foreach ($_POST['achievement'] as $achievement) {
                    $achievement = trim($achievement);
                    if (!empty($achievement)) {
                        $this->achievementModel->create([
                            'team_id' => $teamId,
                            'achievement' => $achievement
                        ]);
                    }
                }
            }

            // Add players if provided
            if (isset($_POST['players']) && is_array($_POST['players'])) {
                foreach ($_POST['players'] as $playerJson) {
                    $playerData = json_decode($playerJson, true);
                    if ($playerData) {
                        // Extract user data
                        $userData = [
                            'first_name' => $playerData['first_name'],
                            'last_name' => $playerData['last_name'],
                            'nic' => $playerData['nic'],
                            'email' => $playerData['email'],
                            'phone_number' => $playerData['phone_number'],
                            'image' => null
                        ];
                        
                        // Extract player data
                        $playerSpecificData = [
                            'position' => $playerData['position'],
                            'role' => $playerData['role']
                        ];
                        
                        // Add player to team
                        $this->playerModel->addToTeam($teamId, $userData, $playerSpecificData);
                    }
                }
            }

            // Add coaches if provided
            if (isset($_POST['coaches']) && is_array($_POST['coaches'])) {
                foreach ($_POST['coaches'] as $coachJson) {
                    $coachData = json_decode($coachJson, true);
                    if ($coachData) {
                        // Extract user data
                        $userData = [
                            'first_name' => $coachData['first_name'],
                            'last_name' => $coachData['last_name'],
                            'nic' => $coachData['nic'],
                            'email' => $coachData['email'],
                            'phone_number' => $coachData['phone_number'],
                            'image' => null
                        ];
                        
                        // Extract coach data
                        $coachSpecificData = [
                            'license' => $coachData['license']
                        ];
                        
                        // Add coach to team
                        $this->coachModel->addToTeam($teamId, $userData, $coachSpecificData);
                    }
                }
            }

            echo json_encode([
                'success' => true,
                'message' => 'Team created successfully',
                'team_id' => $teamId
            ]);

        } catch (Exception $e) {
            error_log("Error creating team: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
        exit;
    }

    /**
     * Check if player exists by NIC
     */
    public function checkPlayerExists() {
        header('Content-Type: application/json');
        
        $nic = $_GET['nic'] ?? null;
        
        if (!$nic) {
            echo json_encode(['success' => false, 'message' => 'NIC is required']);
            exit;
        }
        
        try {
            $player = $this->playerModel->query("SELECT p.player_id, p.position, p.role, u.first_name, u.last_name FROM players p LEFT JOIN users u ON p.nic = u.nic WHERE p.nic = :nic", ['nic' => $nic]);
            
            if (!empty($player)) {
                echo json_encode([
                    'success' => true, 
                    'exists' => true,
                    'player' => $player[0]
                ]);
            } else {
                echo json_encode([
                    'success' => true, 
                    'exists' => false
                ]);
            }
        } catch (Exception $e) {
            error_log("Error checking player: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
        exit;
    }

    /**
     * Link existing player to team without updating details
     */
    public function linkPlayerToTeam() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit;
        }
        
        try {
            $teamId = $_POST['team_id'] ?? null;
            $nic = $_POST['nic'] ?? null;
            
            if (!$teamId || !$nic) {
                echo json_encode(['success' => false, 'message' => 'Team ID and NIC are required']);
                exit;
            }
            
            // Get player by NIC
            $player = $this->playerModel->query("SELECT player_id FROM players WHERE nic = :nic", ['nic' => $nic]);
            
            if (empty($player)) {
                echo json_encode(['success' => false, 'message' => 'Player not found']);
                exit;
            }
            
            $playerId = $player[0]->player_id;
            
            // Check if already linked
            $existingLink = $this->playerModel->query("SELECT * FROM team_players WHERE team_id = :team_id AND player_id = :player_id", [
                'team_id' => $teamId,
                'player_id' => $playerId
            ]);
            
            if (!empty($existingLink)) {
                echo json_encode(['success' => false, 'message' => 'Player is already in this team']);
                exit;
            }
            
            // Link player to team
            $this->playerModel->query("INSERT INTO team_players (team_id, player_id) VALUES (:team_id, :player_id)", [
                'team_id' => $teamId,
                'player_id' => $playerId
            ]);
            
            echo json_encode(['success' => true, 'message' => 'Player linked to team successfully']);
            
        } catch (Exception $e) {
            error_log("Error linking player to team: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
        exit;
    }

    /**
     * Add player to existing team
     */
    /**
     * Add player to existing team (creates new or updates existing)
     */
    public function addPlayerToTeam() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit;
        }
        
        try {
            $teamId = $_POST['team_id'] ?? null;
            
            if (!$teamId) {
                echo json_encode(['success' => false, 'message' => 'Team ID is required']);
                exit;
            }
            
            $userData = [
                'first_name' => $_POST['first_name'] ?? '',
                'last_name' => $_POST['last_name'] ?? '',
                'nic' => $_POST['nic'] ?? '',
                'email' => $_POST['email'] ?? '',
                'phone_number' => $_POST['phone_number'] ?? '',
                'image' => null
            ];
            
            $playerData = [
                'position' => $_POST['position'] ?? '',
                'role' => $_POST['role'] ?? ''
            ];
            
            // Validate required fields
            if (empty($userData['nic']) || empty($userData['first_name']) || empty($userData['last_name'])) {
                echo json_encode(['success' => false, 'message' => 'NIC, First Name, and Last Name are required']);
                exit;
            }
            
            // Check if player already exists
            $existingPlayer = $this->playerModel->query("SELECT player_id FROM players WHERE nic = :nic", ['nic' => $userData['nic']]);
            $playerExists = !empty($existingPlayer);
            
            $playerId = $this->playerModel->addToTeam($teamId, $userData, $playerData);
            
            if ($playerId) {
                echo json_encode([
                    'success' => true, 
                    'message' => $playerExists ? 'Existing player updated and added to team' : 'New player created and added to team',
                    'player_id' => $playerId,
                    'is_existing' => $playerExists
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to add player']);
            }
            
        } catch (Exception $e) {
            error_log("Error adding player to team: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
        exit;
    }

    /**
     * Check if coach exists by NIC
     */
    public function checkCoachExists() {
        header('Content-Type: application/json');
        
        $nic = $_GET['nic'] ?? null;
        
        if (!$nic) {
            echo json_encode(['success' => false, 'message' => 'NIC is required']);
            exit;
        }
        
        try {
            $coach = $this->coachModel->query("SELECT c.coach_id, c.license, u.first_name, u.last_name FROM coaches c LEFT JOIN users u ON c.nic = u.nic WHERE c.nic = :nic", ['nic' => $nic]);
            
            if (!empty($coach)) {
                echo json_encode([
                    'success' => true, 
                    'exists' => true,
                    'coach' => $coach[0]
                ]);
            } else {
                echo json_encode([
                    'success' => true, 
                    'exists' => false
                ]);
            }
        } catch (Exception $e) {
            error_log("Error checking coach: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
        exit;
    }

    /**
     * Link existing coach to team without updating details
     */
    public function linkCoachToTeam() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit;
        }
        
        try {
            $teamId = $_POST['team_id'] ?? null;
            $nic = $_POST['nic'] ?? null;
            
            if (!$teamId || !$nic) {
                echo json_encode(['success' => false, 'message' => 'Team ID and NIC are required']);
                exit;
            }
            
            // Get coach by NIC
            $coach = $this->coachModel->query("SELECT coach_id FROM coaches WHERE nic = :nic", ['nic' => $nic]);
            
            if (empty($coach)) {
                echo json_encode(['success' => false, 'message' => 'Coach not found']);
                exit;
            }
            
            $coachId = $coach[0]->coach_id;
            
            // Check if already linked
            $existingLink = $this->coachModel->query("SELECT * FROM team_coaches WHERE team_id = :team_id AND coach_id = :coach_id", [
                'team_id' => $teamId,
                'coach_id' => $coachId
            ]);
            
            if (!empty($existingLink)) {
                echo json_encode(['success' => false, 'message' => 'Coach is already in this team']);
                exit;
            }
            
            // Link coach to team
            $this->coachModel->query("INSERT INTO team_coaches (team_id, coach_id) VALUES (:team_id, :coach_id)", [
                'team_id' => $teamId,
                'coach_id' => $coachId
            ]);
            
            echo json_encode(['success' => true, 'message' => 'Coach linked to team successfully']);
            
        } catch (Exception $e) {
            error_log("Error linking coach to team: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
        exit;
    }

    /**
     * Add coach to existing team (creates new or updates existing)
     */
    public function addCoachToTeam() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit;
        }
        
        try {
            $teamId = $_POST['team_id'] ?? null;
            
            if (!$teamId) {
                echo json_encode(['success' => false, 'message' => 'Team ID is required']);
                exit;
            }
            
            $userData = [
                'first_name' => $_POST['first_name'] ?? '',
                'last_name' => $_POST['last_name'] ?? '',
                'nic' => $_POST['nic'] ?? '',
                'email' => $_POST['email'] ?? '',
                'phone_number' => $_POST['phone_number'] ?? '',
                'image' => null
            ];
            
            $coachData = [
                'license' => $_POST['license'] ?? ''
            ];
            
            // Validate required fields
            if (empty($userData['nic']) || empty($userData['first_name']) || empty($userData['last_name'])) {
                echo json_encode(['success' => false, 'message' => 'NIC, First Name, and Last Name are required']);
                exit;
            }
            
            // Check if coach already exists
            $existingCoach = $this->coachModel->query("SELECT coach_id FROM coaches WHERE nic = :nic", ['nic' => $userData['nic']]);
            $coachExists = !empty($existingCoach);
            
            $coachId = $this->coachModel->addToTeam($teamId, $userData, $coachData);
            
            if ($coachId) {
                echo json_encode([
                    'success' => true, 
                    'message' => $coachExists ? 'Existing coach updated and added to team' : 'New coach created and added to team',
                    'coach_id' => $coachId,
                    'is_existing' => $coachExists
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to add coach']);
            }
            
        } catch (Exception $e) {
            error_log("Error adding coach to team: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
        exit;
    }

    /**
     * Get player data for editing
     */
    public function getPlayerData() {
        header('Content-Type: application/json');
        
        $playerId = $_GET['id'] ?? null;
        
        if (!$playerId) {
            echo json_encode(['success' => false, 'message' => 'Player ID is required']);
            exit;
        }
        
        try {
            $player = $this->playerModel->getById($playerId);
            
            if (!$player) {
                echo json_encode(['success' => false, 'message' => 'Player not found']);
                exit;
            }
            
            echo json_encode(['success' => true, 'player' => $player]);
            
        } catch (Exception $e) {
            error_log("Error getting player data: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
        exit;
    }

    /**
     * Get coach data for editing
     */
    public function getCoachData() {
        header('Content-Type: application/json');
        
        $coachId = $_GET['id'] ?? null;
        
        if (!$coachId) {
            echo json_encode(['success' => false, 'message' => 'Coach ID is required']);
            exit;
        }
        
        try {
            $coach = $this->coachModel->getById($coachId);
            
            if (!$coach) {
                echo json_encode(['success' => false, 'message' => 'Coach not found']);
                exit;
            }
            
            echo json_encode(['success' => true, 'coach' => $coach]);
            
        } catch (Exception $e) {
            error_log("Error getting coach data: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
        exit;
    }

    /**
     * Update player details
     */
    public function updatePlayer() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit;
        }
        
        try {
            $playerId = $_POST['id'] ?? null;
            
            if (!$playerId) {
                echo json_encode(['success' => false, 'message' => 'Player ID is required']);
                exit;
            }
            
            $playerData = [
                'position' => $_POST['position'] ?? '',
                'role' => $_POST['role'] ?? ''
            ];
            
            $this->playerModel->update($playerId, $playerData);
            
            // Update user data if present
            $userNic = $_POST['nic'] ?? null;
            if ($userNic) {
                $userData = [
                    'first_name' => $_POST['first_name'] ?? '',
                    'last_name' => $_POST['last_name'] ?? '',
                    'email' => $_POST['email'] ?? '',
                    'phone_number' => $_POST['phone_number'] ?? ''
                ];
                
                $this->userModel->updateByNic($userNic, $userData);
            }
            
            echo json_encode(['success' => true, 'message' => 'Player updated successfully']);
            
        } catch (Exception $e) {
            error_log("Error updating player: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
        exit;
    }

    /**
     * Update coach details
     */
    public function updateCoach() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit;
        }
        
        try {
            $coachId = $_POST['id'] ?? null;
            
            if (!$coachId) {
                echo json_encode(['success' => false, 'message' => 'Coach ID is required']);
                exit;
            }
            
            $coachData = [
                'license' => $_POST['license'] ?? ''
            ];
            
            $this->coachModel->update($coachId, $coachData);
            
            // Update user data if present
            $userNic = $_POST['nic'] ?? null;
            if ($userNic) {
                $userData = [
                    'first_name' => $_POST['first_name'] ?? '',
                    'last_name' => $_POST['last_name'] ?? '',
                    'email' => $_POST['email'] ?? '',
                    'phone_number' => $_POST['phone_number'] ?? ''
                ];
                
                $this->userModel->updateByNic($userNic, $userData);
            }
            
            echo json_encode(['success' => true, 'message' => 'Coach updated successfully']);
            
        } catch (Exception $e) {
            error_log("Error updating coach: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
        exit;
    }

    /**
     * Get team data for viewing or editing (AJAX)
     */
    public function getTeamData() {
        header('Content-Type: application/json');
        
        $teamId = $_GET['id'] ?? null;
        
        if (!$teamId) {
            echo json_encode(['success' => false, 'message' => 'Team ID is required']);
            exit;
        }
        
        try {
            // Get team info
            $team = $this->teamModel->getById($teamId);
            
            if (!$team) {
                echo json_encode(['success' => false, 'message' => 'Team not found']);
                exit;
            }
            
            // Get tournaments
            try {
                $tournaments = $this->tournamentModel->getByTeamId($teamId);
            } catch (Exception $e) {
                error_log("Tournament error: " . $e->getMessage());
                $tournaments = [];
            }
            
            // Get achievements
            try {
                $achievements = $this->achievementModel->getByTeamId($teamId);
            } catch (Exception $e) {
                error_log("Achievement error: " . $e->getMessage());
                $achievements = [];
            }
            
            // Get players
            try {
                $players = $this->playerModel->getByTeamId($teamId);
            } catch (Exception $e) {
                error_log("Player error: " . $e->getMessage());
                $players = [];
            }
            
            // Get coaches
            try {
                $coaches = $this->coachModel->getByTeamId($teamId);
            } catch (Exception $e) {
                error_log("Coach error: " . $e->getMessage());
                $coaches = [];
            }
            
            echo json_encode([
                'success' => true,
                'team' => $team,
                'tournaments' => $tournaments ?: [],
                'achievements' => $achievements ?: [],
                'players' => $players ?: [],
                'coaches' => $coaches ?: [],
                'players_count' => count($players ?: [])
            ]);
            
        } catch (Exception $e) {
            error_log("Error fetching team data: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
        exit;
    }


    public function update() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit;
        }
        
        try {
            $teamId = $_POST['team_id'] ?? null;
            
            if (!$teamId) {
                echo json_encode(['success' => false, 'message' => 'Team ID is required']);
                exit;
            }
            
            // Update team basic info
            $updateData = [
                'season' => trim($_POST['season'] ?? ''),
                'status' => $_POST['status'] ?? 'present'
            ];
            
            $this->teamModel->update($teamId, $updateData);
            
            // Update tournaments
            if (isset($_POST['name'])) {
                $this->tournamentModel->deleteByTeamId($teamId);
                
                $tournaments = is_array($_POST['name']) ? $_POST['name'] : [$_POST['name']];
                foreach ($tournaments as $tournament) {
                    $tournament = trim($tournament);
                    if (!empty($tournament)) {
                        $this->tournamentModel->create([
                            'team_id' => $teamId,
                            'name' => $tournament
                        ]);
                    }
                }
            }
            
            // Update achievements
            if (isset($_POST['achievement'])) {
                $this->achievementModel->deleteByTeamId($teamId);
                
                $achievements = is_array($_POST['achievement']) ? $_POST['achievement'] : [$_POST['achievement']];
                foreach ($achievements as $achievement) {
                    $achievement = trim($achievement);
                    if (!empty($achievement)) {
                        $this->achievementModel->create([
                            'team_id' => $teamId,
                            'achievement' => $achievement
                        ]);
                    }
                }
            }
            
            echo json_encode(['success' => true, 'message' => 'Team updated successfully']);
            
        } catch (Exception $e) {
            error_log("Error updating team: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
        exit;
    }


    public function addPlayer() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit;
        }
        
        try {
            $teamId = $_POST['team_id'] ?? null;
            
            if (!$teamId) {
                echo json_encode(['success' => false, 'message' => 'Team ID is required']);
                exit;
            }
            
            // Validate user fields
            $requiredUserFields = ['first_name', 'last_name', 'nic', 'email', 'phone_number'];
            foreach ($requiredUserFields as $field) {
                if (empty($_POST[$field])) {
                    echo json_encode(['success' => false, 'message' => ucfirst(str_replace('_', ' ', $field)) . ' is required']);
                    exit;
                }
            }
            
            // Validate player fields
            $requiredPlayerFields = ['position', 'role'];
            foreach ($requiredPlayerFields as $field) {
                if (empty($_POST[$field])) {
                    echo json_encode(['success' => false, 'message' => ucfirst($field) . ' is required']);
                    exit;
                }
            }
            
            // Handle image upload
            $imagePath = null;
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = ROOT_PATH . '/public/uploads/players/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                
                $fileName = basename($_FILES['image']['name']);
                $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                $allowedExts = ['jpg', 'jpeg', 'png', 'gif'];
                
                if (in_array($fileExt, $allowedExts)) {
                    $newFileName = uniqid('player_') . '.' . $fileExt;
                    $uploadPath = $uploadDir . $newFileName;
                    
                    if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
                        $imagePath = 'uploads/players/' . $newFileName;
                    }
                }
            }
            
            // Prepare user data
            $userData = [
                'first_name' => $_POST['first_name'],
                'last_name' => $_POST['last_name'],
                'nic' => $_POST['nic'],
                'email' => $_POST['email'],
                'phone_number' => $_POST['phone_number'],
                'image' => $imagePath
            ];
            
            // Prepare player data
            $playerData = [
                'position' => $_POST['position'],
                'role' => $_POST['role']
            ];
            
            // Create and link player to team
            $playerId = $this->playerModel->addToTeam($teamId, $userData, $playerData);
            
            if (!$playerId) {
                echo json_encode(['success' => false, 'message' => 'Failed to add player to team']);
                exit;
            }
            
            echo json_encode([
                'success' => true,
                'message' => 'Player account created with NIC as username and password: 123456',
                'player_id' => $playerId
            ]);
            
        } catch (Exception $e) {
            error_log("Error adding player: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
        exit;
    }

    public function addCoach() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit;
        }
        
        try {
            $teamId = $_POST['team_id'] ?? null;
            
            if (!$teamId) {
                echo json_encode(['success' => false, 'message' => 'Team ID is required']);
                exit;
            }
            
            // Validate user fields
            $requiredUserFields = ['first_name', 'last_name', 'nic', 'email', 'phone_number'];
            foreach ($requiredUserFields as $field) {
                if (empty($_POST[$field])) {
                    echo json_encode(['success' => false, 'message' => ucfirst(str_replace('_', ' ', $field)) . ' is required']);
                    exit;
                }
            }
            
            // Validate coach fields
            if (empty($_POST['license'])) {
                echo json_encode(['success' => false, 'message' => 'License is required']);
                exit;
            }
            
            // Handle image upload
            $imagePath = null;
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = ROOT_PATH . '/public/uploads/coaches/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                
                $fileName = basename($_FILES['image']['name']);
                $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                $allowedExts = ['jpg', 'jpeg', 'png', 'gif'];
                
                if (in_array($fileExt, $allowedExts)) {
                    $newFileName = uniqid('coach_') . '.' . $fileExt;
                    $uploadPath = $uploadDir . $newFileName;
                    
                    if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
                        $imagePath = 'uploads/coaches/' . $newFileName;
                    }
                }
            }
            
            // Prepare user data
            $userData = [
                'first_name' => $_POST['first_name'],
                'last_name' => $_POST['last_name'],
                'nic' => $_POST['nic'],
                'email' => $_POST['email'],
                'phone_number' => $_POST['phone_number'],
                'image' => $imagePath
            ];
            
            // Prepare coach data
            $coachData = [
                'license' => $_POST['license']
            ];
            
            // Create and link coach to team
            $coachId = $this->coachModel->addToTeam($teamId, $userData, $coachData);
            
            if (!$coachId) {
                echo json_encode(['success' => false, 'message' => 'Failed to add coach to team']);
                exit;
            }
            
            echo json_encode([
                'success' => true,
                'message' => 'Coach account created with NIC as username and password: 123456',
                'coach_id' => $coachId
            ]);
            
        } catch (Exception $e) {
            error_log("Error adding coach: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
        exit;
    }


    private function handleImageUpload($file, $folder = 'players') {
        if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
            return '';
        }
        
        try {
            $uploadDir = 'uploads/' . $folder . '/';
            
            // Create directory if it doesn't exist
            if (!file_exists($uploadDir)) {
                @mkdir($uploadDir, 0777, true);
            }
            
            $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $fileName = uniqid($folder . '_') . '.' . $fileExtension;
            $uploadPath = $uploadDir . $fileName;
            
            if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
                return $uploadPath;
            }
        } catch (Exception $e) {
            error_log("Image upload error: " . $e->getMessage());
        }
        
        return '';
    }
    

    private function createUserIfNotExists($nic, $postData) {
        try {
            // Check if user exists
            $existingUser = $this->userModel->getUserByNic($nic);
            if ($existingUser) {
                return; // User already exists
            }
            
            // Create new user
            $userData = [
                'nic' => $nic,
                'user_id' => $nic,
                'password' => password_hash('123456', PASSWORD_BCRYPT),
                'first_name' => $postData['full_name'] ?? '',
                'last_name' => '',
                'email' => strtolower(str_replace(' ', '.', $postData['full_name'] ?? '')) . '@uocfootball.com',
                'phone_number' => $postData['phone_number'] ?? '',
                'role' => 'player'
            ];
            
            $this->userModel->create($userData);
            
        } catch (Exception $e) {
            error_log("Warning: Could not create user account for NIC $nic: " . $e->getMessage());
        }
    }
    

    public function delete() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit;
        }
        
        try {
            // Handle both JSON and POST data
            $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
            $teamId = $input['team_id'] ?? null;
            
            if (!$teamId) {
                echo json_encode(['success' => false, 'message' => 'Team ID is required']);
                exit;
            }
            
            $this->teamModel->delete($teamId);
            
            echo json_encode(['success' => true, 'message' => 'Team deleted successfully']);
            
        } catch (Exception $e) {
            error_log("Error deleting team: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
        exit;
    }
    

    public function deletePlayer() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit;
        }
        
        try {
            // Handle both JSON and POST data
            $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
            $id = $input['id'] ?? null;
            
            if (!$id) {
                echo json_encode(['success' => false, 'message' => 'Player ID is required']);
                exit;
            }
            
            $this->playerModel->delete($id);
            
            echo json_encode(['success' => true, 'message' => 'Player deleted successfully']);
            
        } catch (Exception $e) {
            error_log("Error deleting player: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
        exit;
    }
    

    public function deleteCoach() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit;
        }
        
        try {
            // Handle both JSON and POST data
            $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
            $id = $input['id'] ?? null;
            
            if (!$id) {
                echo json_encode(['success' => false, 'message' => 'Coach ID is required']);
                exit;
            }
            
            $this->coachModel->delete($id);
            
            echo json_encode(['success' => true, 'message' => 'Coach deleted successfully']);
            
        } catch (Exception $e) {
            error_log("Error deleting coach: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
        exit;
    }

}

