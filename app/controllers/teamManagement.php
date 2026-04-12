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

    /**
     * Handle image upload
     */
    private function uploadPlayerImage() {
        if (!isset($_FILES['image']) || $_FILES['image']['error'] === UPLOAD_ERR_NO_FILE) {
            return '';
        }

        if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            throw new Exception('Image upload failed');
        }

        $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/uploads/players/';
        
        // Create directory if not exists
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $file = $_FILES['image'];
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        
        if (!in_array($file['type'], $allowedTypes)) {
            throw new Exception('Invalid image type. Only JPEG, PNG, GIF, and WebP are allowed');
        }

        $maxSize = 5 * 1024 * 1024; // 5MB
        if ($file['size'] > $maxSize) {
            throw new Exception('Image size exceeds 5MB limit');
        }

        $filename = uniqid('player_') . '_' . time() . '.' . pathinfo($file['name'], PATHINFO_EXTENSION);
        $filepath = $uploadDir . $filename;

        if (!move_uploaded_file($file['tmp_name'], $filepath)) {
            throw new Exception('Failed to save image');
        }

        return ROOT . '/uploads/players/' . $filename;
    }

    public function index() {
        try {
            // Get all players from database
            $players = $this->playerModel->getAll();
            
            $data = [];
            $data['players'] = $players ? $players : [];
            
        } catch (Exception $e) {
            error_log("Error fetching players: " . $e->getMessage());
            $data = [];
            $data['players'] = [];
        }
        
        $this->view('teamManagement', $data);
    }

    /**
     * Add new player to database
     */
    public function addPlayer() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // Handle image upload
                $imagePath = '';
                try {
                    $imagePath = $this->uploadPlayerImage();
                } catch (Exception $e) {
                    // Image is optional, so we can continue without it
                    // But we could log the error if needed
                }

                $playerData = [
                    'team_id' => 1, // Default team ID
                    'full_name' => $_POST['full_name'] ?? '',
                    'name_with_initials' => $_POST['name_with_initials'] ?? '',
                    'position' => $_POST['position'] ?? '',
                    'role' => $_POST['role'] ?? 'player',
                    'jersey_number' => !empty($_POST['jersey_number']) ? (int)$_POST['jersey_number'] : null,
                    'faculty' => $_POST['faculty'] ?? '',
                    'nic' => $_POST['nic'] ?? '',
                    'uni_register_number' => $_POST['uni_register_number'] ?? '',
                    'mobile_number' => $_POST['mobile_number'] ?? '',
                    'address' => $_POST['address'] ?? '',
                    'height' => !empty($_POST['height']) ? (float)$_POST['height'] : null,
                    'weight' => !empty($_POST['weight']) ? (float)$_POST['weight'] : null,
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

                // Create player
                $result = $this->playerModel->create($playerData);
                
                if ($result) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Player added successfully'
                    ]);
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Failed to add player'
                    ]);
                }
                
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
     * Get player by ID for editing
     */
    public function getPlayer() {
        header('Content-Type: application/json');
        
        $playerId = $_GET['id'] ?? null;
        
        if (!$playerId) {
            echo json_encode([
                'success' => false,
                'message' => 'Player ID not provided'
            ]);
            exit;
        }

        try {
            $player = $this->playerModel->getById($playerId);
            
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
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
        exit;
    }

    /**
     * Update existing player
     */
    public function updatePlayer() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $playerId = $_GET['id'] ?? null;
            
            if (!$playerId) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Player ID not provided'
                ]);
                exit;
            }

            try {
                // Get existing player data to preserve image if not updating
                $existingPlayer = $this->playerModel->getById($playerId);
                
                if (!$existingPlayer) {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Player not found'
                    ]);
                    exit;
                }
                
                $imagePath = $existingPlayer->image ?? '';

                // Handle image upload if new image is provided
                try {
                    $newImagePath = $this->uploadPlayerImage();
                    if ($newImagePath) {
                        // Delete old image if it exists
                        if (!empty($existingPlayer->image)) {
                            $oldImagePath = str_replace(ROOT, $_SERVER['DOCUMENT_ROOT'], $existingPlayer->image);
                            if (file_exists($oldImagePath)) {
                                @unlink($oldImagePath);
                            }
                        }
                        $imagePath = $newImagePath;
                    }
                } catch (Exception $e) {
                    // Image update is optional, continue without it
                    error_log("Image upload error: " . $e->getMessage());
                }

                $playerData = [
                    'full_name' => $_POST['full_name'] ?? '',
                    'name_with_initials' => $_POST['name_with_initials'] ?? '',
                    'position' => $_POST['position'] ?? '',
                    'role' => $_POST['role'] ?? 'player',
                    'jersey_number' => !empty($_POST['jersey_number']) ? (int)$_POST['jersey_number'] : null,
                    'faculty' => $_POST['faculty'] ?? '',
                    'nic' => $_POST['nic'] ?? '',
                    'uni_register_number' => $_POST['uni_register_number'] ?? '',
                    'mobile_number' => $_POST['mobile_number'] ?? '',
                    'address' => $_POST['address'] ?? '',
                    'height' => !empty($_POST['height']) ? (float)$_POST['height'] : null,
                    'weight' => !empty($_POST['weight']) ? (float)$_POST['weight'] : null,
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

                error_log("Updating player $playerId with data: " . json_encode($playerData));
                
                $result = $this->playerModel->update($playerId, $playerData);
                
                if ($result) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Player updated successfully'
                    ]);
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Failed to update player - database error'
                    ]);
                }
                
            } catch (Exception $e) {
                error_log("Update player ERROR: " . $e->getMessage() . " | Trace: " . $e->getTraceAsString());
                echo json_encode([
                    'success' => false,
                    'message' => 'Error: ' . $e->getMessage()
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
            $playerId = $_GET['id'] ?? null;
            
            if (!$playerId) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Player ID not provided'
                ]);
                exit;
            }

            try {
                $result = $this->playerModel->delete($playerId);
                
                if ($result) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Player deleted successfully'
                    ]);
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Failed to delete player'
                    ]);
                }
                
            } catch (Exception $e) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Error: ' . $e->getMessage()
                ]);
            }
        }
        exit;
    }
}

