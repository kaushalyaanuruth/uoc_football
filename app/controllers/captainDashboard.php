<?php

class captainDashboard extends Controller {

    private function jsonResponse($payload)
    {
        header('Content-Type: application/json');
        echo json_encode($payload);
        exit;
    }

    private function buildInitialsAvatarUrl($displayName)
    {
        $name = trim((string) $displayName);
        if ($name === '') {
            $name = 'Captain';
        }

        $parts = preg_split('/\s+/', $name);
        $initials = '';
        foreach ($parts as $part) {
            if ($part === '') {
                continue;
            }
            $initials .= strtoupper(substr($part, 0, 1));
            if (strlen($initials) >= 2) {
                break;
            }
        }

        if ($initials === '') {
            $initials = 'C';
        }

        $palette = ['#4f46e5', '#0ea5e9', '#059669', '#d97706', '#dc2626', '#7c3aed'];
        $color = $palette[abs(crc32($name)) % count($palette)];

        $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="320" height="320" viewBox="0 0 320 320">'
            . '<rect width="320" height="320" fill="' . $color . '"/>'
            . '<text x="50%" y="52%" dominant-baseline="middle" text-anchor="middle" '
            . 'font-family="Arial, Helvetica, sans-serif" font-size="120" font-weight="700" fill="#ffffff">'
            . htmlspecialchars($initials, ENT_QUOTES, 'UTF-8')
            . '</text>'
            . '</svg>';

        return 'data:image/svg+xml;utf8,' . rawurlencode($svg);
    }

    private function normalizeImageUrl($imagePath, $displayName = '')
    {
        if (empty($imagePath)) {
            return $this->buildInitialsAvatarUrl($displayName);
        }

        $normalized = str_replace('\\', '/', ltrim((string) $imagePath, '/'));
        return ROOT . '/' . $normalized;
    }

    private function isCaptainLikeRole($role)
    {
        $normalized = strtolower(trim((string) $role));
        $normalized = str_replace(['_', ' '], '-', $normalized);

        return in_array($normalized, ['captain', 'vice-captain', 'vicecaptain'], true);
    }

    private function ensureCaptainAccess()
    {
        if (!isset($_SESSION['user_id'], $_SESSION['nic'])) {
            header('Location: ' . ROOT . '/login');
            exit();
        }

        $userType = strtolower((string) ($_SESSION['user_type'] ?? ''));
        $playerRole = (string) ($_SESSION['player_role'] ?? '');
        $isCaptain = $userType === 'captain' || ($userType === 'player' && $this->isCaptainLikeRole($playerRole));

        if (!$isCaptain) {
            header('Location: ' . ROOT . '/login');
            exit();
        }
    }

    public function index() {
        $this->ensureCaptainAccess();

        $nic = $_SESSION['nic'] ?? '';
        $playerModel = $this->model('PlayerModel');

        $captainRows = $playerModel->query(
            "SELECT
                p.player_id,
                p.position,
                p.role,
                p.nic,
                u.user_id,
                u.email,
                u.phone_number,
                u.first_name,
                u.last_name,
                u.image
             FROM players p
             JOIN users u ON u.nic = p.nic
             WHERE p.nic = :nic
             LIMIT 1",
            ['nic' => $nic]
        );

        if (empty($captainRows)) {
            header('Location: ' . ROOT . '/login?error=invalid_credentials');
            exit();
        }

        $captain = $captainRows[0];
        $fullName = trim(($captain->first_name ?? '') . ' ' . ($captain->last_name ?? ''));
        if ($fullName === '') {
            $fullName = $captain->nic;
        }
        $captainImage = $this->normalizeImageUrl($captain->image ?? '', $fullName);

        // Check if user is logged in
        // if (session_status() === PHP_SESSION_NONE) {
        //     session_start();
        // }
        
        // if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'player') {
        //     header('Location: ' . ROOT . '/login');
        //     exit();
        // }
        
        // // Check if user is captain or vice-captain
        // if (!isset($_SESSION['player_role']) || 
        //     ($_SESSION['player_role'] !== 'captain' && $_SESSION['player_role'] !== 'vice_captain')) {
        //     // Redirect regular players to player dashboard
        //     header('Location: ' . ROOT . '/playerDashboard');
        //     exit();
        // }
        
        // Load captain/vice-captain data if needed
        $noticeModel = $this->model('NoticeModel');
        $noticeRows = [];
        try {
            $noticeRows = $noticeModel->getRecent(6, 'present_team');
        } catch (Exception $e) {
            $noticeRows = [];
        }

        $notices = [];
        foreach ($noticeRows as $row) {
            $notices[] = [
                'id' => (int) ($row->notice_id ?? 0),
                'title' => $row->title ?? 'Notice',
                'content' => $row->content ?? '',
                'author' => $row->created_by ?? 'Admin',
                'date' => !empty($row->created_at) ? date('M d, Y h:i A', strtotime($row->created_at)) : ''
            ];
        }

        if (empty($notices)) {
            $notices[] = [
                'title' => 'No notices yet',
                'content' => 'Admin notices for present team members will appear here.',
                'author' => 'System',
                'date' => ''
            ];
        }

        $eventModel = $this->model('EventModel');
        $upcomingEvents = [];
        try {
            $upcomingEvents = $eventModel->getUpcoming();
        } catch (Exception $e) {
            $upcomingEvents = [];
        }

        $nextTraining = [
            'title' => 'No training session scheduled',
            'location' => 'Ground',
            'time' => 'TBA'
        ];

        $nextMatch = [
            'title' => 'No match scheduled',
            'location' => 'Ground',
            'date_time' => 'TBA',
            'days_until' => 0
        ];

        foreach ($upcomingEvents as $eventRow) {
            $eventType = strtolower((string) ($eventRow->event_type ?? ''));

            if ($eventType === 'training' && $nextTraining['title'] === 'No training session scheduled') {
                $trainingTime = !empty($eventRow->event_time) ? strtotime($eventRow->event_time) : false;
                $nextTraining = [
                    'title' => !empty($eventRow->title) ? (string) $eventRow->title : 'Training Session',
                    'location' => !empty($eventRow->location) ? (string) $eventRow->location : 'Ground',
                    'time' => $trainingTime ? date('g:i A', $trainingTime) : 'TBA'
                ];
            }

            if ($eventType === 'match' && $nextMatch['title'] === 'No match scheduled') {
                $matchDate = !empty($eventRow->date) ? strtotime($eventRow->date) : false;
                $matchTime = !empty($eventRow->event_time) ? strtotime($eventRow->event_time) : false;

                $dateText = $matchDate ? date('l jS F', $matchDate) : 'Date not set';
                $timeText = $matchTime ? date('g:i A', $matchTime) : 'TBA';

                $daysUntil = 0;
                if ($matchDate) {
                    $today = strtotime(date('Y-m-d'));
                    $daysUntil = max(0, (int) floor(($matchDate - $today) / 86400));
                }

                $nextMatch = [
                    'title' => !empty($eventRow->title) ? (string) $eventRow->title : 'UOC Football Match',
                    'location' => !empty($eventRow->location) ? (string) $eventRow->location : 'Ground',
                    'date_time' => $dateText . ' - ' . $timeText,
                    'days_until' => $daysUntil
                ];
            }

            if ($nextTraining['title'] !== 'No training session scheduled' && $nextMatch['title'] !== 'No match scheduled') {
                break;
            }
        }

        $data = [
            'username' => $_SESSION['username'] ?? 'Captain',
            'captain_name' => $fullName,
            'captain_position' => $captain->position ?? 'Player',
            'captain_role' => $captain->role ?? 'Captain',
            'captain_image' => $captainImage,
            'captain_profile' => [
                'first_name' => $captain->first_name ?? '',
                'last_name' => $captain->last_name ?? '',
                'id_number' => $captain->user_id ?? $captain->nic,
                'nic' => $captain->nic,
                'email' => $captain->email ?? '',
                'phone_number' => $captain->phone_number ?? ''
            ],
            'notices' => $notices,
            'next_training' => $nextTraining,
            'next_match' => $nextMatch
        ];
        
        $this->view('captain/captainDashboard', $data);
    }

    public function addNotice()
    {
        $this->ensureCaptainAccess();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['success' => false, 'message' => 'Invalid request method']);
        }

        $payload = json_decode(file_get_contents('php://input'), true);
        if (!is_array($payload)) {
            $this->jsonResponse(['success' => false, 'message' => 'Invalid payload']);
        }

        $title = trim((string) ($payload['title'] ?? ''));
        $content = trim((string) ($payload['content'] ?? ''));
        if ($title === '' || $content === '') {
            $this->jsonResponse(['success' => false, 'message' => 'Title and content are required']);
        }

        try {
            $noticeModel = $this->model('NoticeModel');
            $createdBy = trim((string) ($_SESSION['user_id'] ?? 'Captain'));
            if ($createdBy === '') {
                $createdBy = 'Captain';
            }

            $noticeModel->createNotice($title, $content, $createdBy, 'present_team');
            $this->jsonResponse(['success' => true, 'message' => 'Notice added successfully']);
        } catch (Exception $e) {
            error_log('Captain failed to add notice: ' . $e->getMessage());
            $this->jsonResponse(['success' => false, 'message' => 'Failed to add notice']);
        }
    }

    public function updateNotice()
    {
        $this->ensureCaptainAccess();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['success' => false, 'message' => 'Invalid request method']);
        }

        $payload = json_decode(file_get_contents('php://input'), true);
        if (!is_array($payload)) {
            $this->jsonResponse(['success' => false, 'message' => 'Invalid payload']);
        }

        $noticeId = (int) ($payload['notice_id'] ?? 0);
        $title = trim((string) ($payload['title'] ?? ''));
        $content = trim((string) ($payload['content'] ?? ''));

        if ($noticeId <= 0 || $title === '' || $content === '') {
            $this->jsonResponse(['success' => false, 'message' => 'Notice ID, title and content are required']);
        }

        try {
            $noticeModel = $this->model('NoticeModel');
            $existing = $noticeModel->getById($noticeId);
            if ($existing === null) {
                $this->jsonResponse(['success' => false, 'message' => 'Notice not found']);
            }

            $noticeModel->updateNotice($noticeId, $title, $content);
            $this->jsonResponse(['success' => true, 'message' => 'Notice updated successfully']);
        } catch (Exception $e) {
            error_log('Captain failed to update notice: ' . $e->getMessage());
            $this->jsonResponse(['success' => false, 'message' => 'Failed to update notice']);
        }
    }

    public function deleteNotice()
    {
        $this->ensureCaptainAccess();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['success' => false, 'message' => 'Invalid request method']);
        }

        $payload = json_decode(file_get_contents('php://input'), true);
        if (!is_array($payload)) {
            $this->jsonResponse(['success' => false, 'message' => 'Invalid payload']);
        }

        $noticeId = (int) ($payload['notice_id'] ?? 0);
        if ($noticeId <= 0) {
            $this->jsonResponse(['success' => false, 'message' => 'Notice ID is required']);
        }

        try {
            $noticeModel = $this->model('NoticeModel');
            $existing = $noticeModel->getById($noticeId);
            if ($existing === null) {
                $this->jsonResponse(['success' => false, 'message' => 'Notice not found']);
            }

            $noticeModel->deleteNotice($noticeId);
            $this->jsonResponse(['success' => true, 'message' => 'Notice deleted successfully']);
        } catch (Exception $e) {
            error_log('Captain failed to delete notice: ' . $e->getMessage());
            $this->jsonResponse(['success' => false, 'message' => 'Failed to delete notice']);
        }
    }

    public function updateProfile()
    {
        header('Content-Type: application/json');

        if (!isset($_SESSION['user_id'], $_SESSION['nic'])) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit();
        }

        $userType = strtolower((string) ($_SESSION['user_type'] ?? ''));
        $playerRole = (string) ($_SESSION['player_role'] ?? '');
        $isCaptain = $userType === 'captain' || ($userType === 'player' && $this->isCaptainLikeRole($playerRole));
        if (!$isCaptain) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }

        try {
            $currentNic = $_SESSION['nic'];
            $firstName = trim((string) ($_POST['first_name'] ?? ''));
            $lastName = trim((string) ($_POST['last_name'] ?? ''));
            $idNumber = trim((string) ($_POST['id_number'] ?? ''));
            $email = trim((string) ($_POST['email'] ?? ''));
            $phoneNumber = trim((string) ($_POST['phone_number'] ?? ''));

            if ($firstName === '' || $idNumber === '') {
                echo json_encode(['success' => false, 'message' => 'First name and ID number are required']);
                exit();
            }

            $userModel = $this->model('User');
            $playerModel = $this->model('PlayerModel');

            $duplicateUserRows = $userModel->query(
                "SELECT nic FROM users WHERE user_id = :user_id AND nic != :nic LIMIT 1",
                [
                    'user_id' => $idNumber,
                    'nic' => $currentNic
                ]
            );

            if (!empty($duplicateUserRows)) {
                echo json_encode(['success' => false, 'message' => 'This ID number is already in use']);
                exit();
            }

            $newImagePath = null;
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = ROOT_PATH . '/public/uploads/players/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                $fileName = basename($_FILES['image']['name']);
                $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                $allowedExts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

                if (!in_array($fileExt, $allowedExts, true)) {
                    echo json_encode(['success' => false, 'message' => 'Invalid image format']);
                    exit();
                }

                $newFileName = uniqid('captain_profile_') . '.' . $fileExt;
                $uploadPath = $uploadDir . $newFileName;

                if (!move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
                    echo json_encode(['success' => false, 'message' => 'Failed to upload image']);
                    exit();
                }

                $newImagePath = 'uploads/players/' . $newFileName;
            }

            $updateFields = [
                'first_name' => $firstName,
                'last_name' => $lastName,
                'user_id' => $idNumber,
                'email' => $email,
                'phone_number' => $phoneNumber
            ];

            if (!empty($newImagePath)) {
                $updateFields['image'] = $newImagePath;
            }

            $userModel->updateByNic($currentNic, $updateFields);
            $_SESSION['user_id'] = $idNumber;

            $updatedRows = $playerModel->query(
                "SELECT u.first_name, u.last_name, u.user_id, u.email, u.phone_number, u.image
                 FROM users u
                 WHERE u.nic = :nic
                 LIMIT 1",
                ['nic' => $currentNic]
            );

            $updated = !empty($updatedRows) ? $updatedRows[0] : null;
            $fullName = trim((($updated->first_name ?? '') . ' ' . ($updated->last_name ?? '')));

            echo json_encode([
                'success' => true,
                'message' => 'Profile updated successfully',
                'profile' => [
                    'name' => $fullName !== '' ? $fullName : ($updated->user_id ?? $idNumber),
                    'first_name' => $updated->first_name ?? $firstName,
                    'last_name' => $updated->last_name ?? $lastName,
                    'id_number' => $updated->user_id ?? $idNumber,
                    'email' => $updated->email ?? $email,
                    'phone_number' => $updated->phone_number ?? $phoneNumber,
                    'image_url' => $this->normalizeImageUrl($updated->image ?? $newImagePath, $fullName !== '' ? $fullName : ($updated->user_id ?? $idNumber))
                ]
            ]);
        } catch (Exception $e) {
            error_log('Captain profile update failed: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Profile update failed']);
        }

        exit();
    }

    public function profileData()
    {
        header('Content-Type: application/json');

        if (!isset($_SESSION['user_id'], $_SESSION['nic'])) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }

        $userType = strtolower((string) ($_SESSION['user_type'] ?? ''));
        $playerRole = (string) ($_SESSION['player_role'] ?? '');
        $isCaptain = $userType === 'captain' || ($userType === 'player' && $this->isCaptainLikeRole($playerRole));
        if (!$isCaptain) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }

        $currentNic = (string) $_SESSION['nic'];
        $playerModel = $this->model('PlayerModel');

        $rows = $playerModel->query(
            "SELECT u.first_name, u.last_name, u.user_id, u.nic, u.email, u.phone_number, u.image
             FROM users u
             WHERE u.nic = :nic
             LIMIT 1",
            ['nic' => $currentNic]
        );

        if (empty($rows)) {
            echo json_encode(['success' => false, 'message' => 'Profile not found']);
            exit();
        }

        $profile = $rows[0];
        $fullName = trim((($profile->first_name ?? '') . ' ' . ($profile->last_name ?? '')));

        echo json_encode([
            'success' => true,
            'profile' => [
                'name' => $fullName !== '' ? $fullName : ($profile->user_id ?? $currentNic),
                'first_name' => $profile->first_name ?? '',
                'last_name' => $profile->last_name ?? '',
                'id_number' => $profile->user_id ?? '',
                'nic' => $profile->nic ?? $currentNic,
                'email' => $profile->email ?? '',
                'phone_number' => $profile->phone_number ?? '',
                'image_url' => $this->normalizeImageUrl($profile->image ?? '', $fullName !== '' ? $fullName : ($profile->user_id ?? $currentNic))
            ]
        ]);
        exit();
    }
}
