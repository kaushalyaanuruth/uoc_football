<?php
class adminDashboard extends Controller {

    private function buildInitialsAvatarUrl($displayName)
    {
        $name = trim((string) $displayName);
        if ($name === '') {
            $name = 'Admin';
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
            $initials = 'A';
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

    private function jsonResponse($payload)
    {
        header('Content-Type: application/json');
        echo json_encode($payload);
        exit;
    }

    private function resolveTeamId()
    {
        $teamId = (int) ($_GET['team_id'] ?? $_SESSION['team_id'] ?? 0);
        if ($teamId > 0) {
            $_SESSION['team_id'] = $teamId;
            return $teamId;
        }

        $teamModel = $this->model('TeamModel');

        $hasStatus = false;
        try {
            $statusColumnRows = $teamModel->query("SHOW COLUMNS FROM teams LIKE 'status'");
            $hasStatus = !empty($statusColumnRows);
        } catch (Exception $e) {
            $hasStatus = false;
        }

        if ($hasStatus) {
            $rows = $teamModel->query("SELECT team_id FROM teams WHERE status = 'present' ORDER BY team_id DESC LIMIT 1");
        } else {
            $rows = [];
        }

        if (empty($rows)) {
            $rows = $teamModel->query("SELECT team_id FROM teams ORDER BY team_id DESC LIMIT 1");
        }

        $resolved = (int) ($rows[0]->team_id ?? 0);
        if ($resolved > 0) {
            $_SESSION['team_id'] = $resolved;
        }

        return $resolved;
    }

    private function normalizeInventoryIcon($icon, $itemName)
    {
        $normalizedIcon = strtolower(trim((string) $icon));
        $normalizedName = strtolower(trim((string) $itemName));

        $nameMap = [
            'bibs' => 'checkroom',
            'training bibs' => 'checkroom',
            'footballs' => 'sports_soccer',
            'match footballs' => 'sports_soccer',
            'markers' => 'sports_bar',
            'cones' => 'sports_bar',
            'resistance band' => 'fitness_center',
            'resistance bands' => 'fitness_center',
            'water bottles' => 'sports_bar',
        ];

        $iconMap = [
            'bips' => 'checkroom',
            'bibs' => 'checkroom',
            'checkroom' => 'checkroom',
            'football' => 'sports_soccer',
            'footballs' => 'sports_soccer',
            'sports_soccer' => 'sports_soccer',
            'resistance_band' => 'fitness_center',
            'fitness_center' => 'fitness_center',
            'markers' => 'sports_bar',
            'cones' => 'sports_bar',
            'sports_bar' => 'sports_bar',
            'water_bottle' => 'sports_bar',
        ];

        if ($normalizedIcon === '' || $normalizedIcon === 'inventory.svg' || $normalizedIcon === 'inventory_2') {
            return $nameMap[$normalizedName] ?? 'inventory_2';
        }

        return $iconMap[$normalizedIcon] ?? ($nameMap[$normalizedName] ?? 'inventory_2');
    }

    public function index() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . ROOT . '/login');
            exit();
        }

        $noticeModel = $this->model('NoticeModel');
        $notices = $noticeModel->getForAdmin(8);

        $eventModel = $this->model('EventModel');
        $upcomingEvents = [];

        try {
            // Show nearest upcoming events in the dashboard "What's next" panel.
            $upcomingEvents = $eventModel->getUpcoming(5);
        } catch (Exception $e) {
            error_log('Failed to load upcoming events for admin dashboard: ' . $e->getMessage());
            $upcomingEvents = [];
        }

        $inventoryItems = [];
        try {
            $inventoryModel = $this->model('InventoryModel');
            $teamId = $this->resolveTeamId();
            $inventoryRows = $inventoryModel->getAllItems($teamId);

            foreach (array_slice($inventoryRows, 0, 6) as $row) {
                $totalCount = max(0, (int) ($row->total_count ?? 0));
                $availableCount = max(0, min($totalCount, (int) ($row->available_count ?? 0)));

                $inventoryItems[] = [
                    'name' => (string) ($row->item_name ?? 'Item'),
                    'total_count' => $totalCount,
                    'available_count' => $availableCount,
                    'icon' => $this->normalizeInventoryIcon($row->icon ?? '', $row->item_name ?? ''),
                ];
            }
        } catch (Exception $e) {
            error_log('Failed to load inventory status for admin dashboard: ' . $e->getMessage());
            $inventoryItems = [];
        }

        $userModel = $this->model('User');
        $currentNic = trim((string) ($_SESSION['nic'] ?? ''));
        $currentUserId = trim((string) ($_SESSION['user_id'] ?? ''));

        $adminRows = $userModel->query(
            "SELECT user_id, nic, email, phone_number, first_name, last_name, image
             FROM users
             WHERE nic = :nic OR user_id = :user_id
             LIMIT 1",
            [
                'nic' => $currentNic,
                'user_id' => $currentUserId
            ]
        );

        $adminRow = !empty($adminRows) ? $adminRows[0] : null;
        $firstName = trim((string) ($adminRow->first_name ?? 'Admin'));
        $lastName = trim((string) ($adminRow->last_name ?? ''));
        $fullName = trim($firstName . ' ' . $lastName);
        if ($fullName === '') {
            $fullName = $currentUserId !== '' ? $currentUserId : 'Admin';
        }

        $profileNic = trim((string) ($adminRow->nic ?? $currentNic));
        if ($profileNic === '') {
            $profileNic = $currentUserId;
        }
        $profileUserId = trim((string) ($adminRow->user_id ?? $currentUserId));
        if ($profileUserId === '') {
            $profileUserId = $profileNic;
        }

        $adminImage = $this->normalizeImageUrl($adminRow->image ?? '', $fullName);
        $_SESSION['admin_profile_image'] = $adminImage;
        $_SESSION['admin_profile_name'] = $fullName;

        $this->view('adminDashboard', [
            'notices' => $notices,
            'upcomingEvents' => $upcomingEvents,
            'inventoryItems' => $inventoryItems,
            'admin_name' => $fullName,
            'admin_image' => $adminImage,
            'admin_profile' => [
                'first_name' => $firstName,
                'last_name' => $lastName,
                'id_number' => $profileUserId,
                'nic' => $profileNic,
                'email' => (string) ($adminRow->email ?? ''),
                'phone_number' => (string) ($adminRow->phone_number ?? '')
            ]
        ]);
    }

    public function updateProfile()
    {
        if (!isset($_SESSION['user_id'])) {
            $this->jsonResponse(['success' => false, 'message' => 'Unauthorized']);
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['success' => false, 'message' => 'Invalid request method']);
        }

        try {
            $currentNic = trim((string) ($_SESSION['nic'] ?? ''));
            $currentUserId = trim((string) ($_SESSION['user_id'] ?? ''));
            $firstName = trim((string) ($_POST['first_name'] ?? ''));
            $lastName = trim((string) ($_POST['last_name'] ?? ''));
            $idNumber = trim((string) ($_POST['id_number'] ?? ''));
            $nic = trim((string) ($_POST['nic'] ?? ''));
            $email = trim((string) ($_POST['email'] ?? ''));
            $phoneNumber = trim((string) ($_POST['phone_number'] ?? ''));
            $currentPassword = (string) ($_POST['current_password'] ?? '');
            $newPassword = (string) ($_POST['new_password'] ?? '');
            $confirmPassword = (string) ($_POST['confirm_password'] ?? '');

            if ($firstName === '' || $idNumber === '' || $nic === '') {
                $this->jsonResponse(['success' => false, 'message' => 'First name, ID number and NIC are required']);
            }

            if (!preg_match('/^\d{12}$/', $nic)) {
                $this->jsonResponse(['success' => false, 'message' => 'NIC must contain exactly 12 digits']);
            }

            if ($phoneNumber !== '' && !preg_match('/^\d{10}$/', $phoneNumber)) {
                $this->jsonResponse(['success' => false, 'message' => 'Phone number must contain exactly 10 digits']);
            }

            $userModel = $this->model('User');

            $matchRows = $userModel->query(
                "SELECT nic FROM users WHERE nic = :nic OR user_id = :user_id LIMIT 1",
                [
                    'nic' => $currentNic,
                    'user_id' => $currentUserId
                ]
            );

            $targetNic = !empty($matchRows) ? (string) ($matchRows[0]->nic ?? '') : '';
            if ($targetNic === '') {
                $targetNic = $currentNic !== '' ? $currentNic : $currentUserId;
            }

            $duplicateRows = $userModel->query(
                "SELECT nic FROM users WHERE user_id = :user_id AND nic != :nic LIMIT 1",
                [
                    'user_id' => $idNumber,
                    'nic' => $targetNic
                ]
            );

            if (!empty($duplicateRows)) {
                $this->jsonResponse(['success' => false, 'message' => 'This ID number is already in use']);
            }

            $duplicateNicRows = $userModel->query(
                "SELECT nic FROM users WHERE nic = :nic AND nic != :target_nic LIMIT 1",
                [
                    'nic' => $nic,
                    'target_nic' => $targetNic
                ]
            );

            if (!empty($duplicateNicRows)) {
                $this->jsonResponse(['success' => false, 'message' => 'This NIC is already in use']);
            }

            $wantsPasswordChange = $currentPassword !== '' || $newPassword !== '' || $confirmPassword !== '';
            if ($wantsPasswordChange) {
                if ($currentPassword === '' || $newPassword === '' || $confirmPassword === '') {
                    $this->jsonResponse(['success' => false, 'message' => 'Current password, new password and confirm password are required']);
                }

                if (!$userModel->verifyCurrentPasswordByNic($targetNic, $currentPassword)) {
                    $this->jsonResponse(['success' => false, 'message' => 'Current password is incorrect']);
                }

                if (strlen($newPassword) < 6) {
                    $this->jsonResponse(['success' => false, 'message' => 'Password must be at least 6 characters']);
                }

                if ($newPassword !== $confirmPassword) {
                    $this->jsonResponse(['success' => false, 'message' => 'New password and confirm password do not match']);
                }

                if ($newPassword === '123456') {
                    $this->jsonResponse(['success' => false, 'message' => 'Please choose a password different from the default password']);
                }
            }

            $newImagePath = null;
            if (isset($_FILES['image']) && (int) $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = ROOT_PATH . '/public/uploads/admins/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                $fileName = basename((string) $_FILES['image']['name']);
                $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                $allowedExts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

                if (!in_array($fileExt, $allowedExts, true)) {
                    $this->jsonResponse(['success' => false, 'message' => 'Invalid image format']);
                }

                $newFileName = uniqid('admin_profile_') . '.' . $fileExt;
                $uploadPath = $uploadDir . $newFileName;

                if (!move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
                    $this->jsonResponse(['success' => false, 'message' => 'Failed to upload image']);
                }

                $newImagePath = 'uploads/admins/' . $newFileName;
            }

            $updateFields = [
                'first_name' => $firstName,
                'last_name' => $lastName,
                'user_id' => $idNumber,
                'nic' => $nic,
                'email' => $email,
                'phone_number' => $phoneNumber
            ];

            if ($wantsPasswordChange) {
                $updateFields['password'] = password_hash($newPassword, PASSWORD_BCRYPT);
            }

            if (!empty($newImagePath)) {
                $updateFields['image'] = $newImagePath;
            }

            $userModel->updateByNic($targetNic, $updateFields);
            $_SESSION['user_id'] = $idNumber;
            $_SESSION['nic'] = $nic;

            $updatedRows = $userModel->query(
                "SELECT first_name, last_name, user_id, nic, email, phone_number, image
                 FROM users
                 WHERE nic = :nic
                 LIMIT 1",
                ['nic' => $nic]
            );

            if (empty($updatedRows) && $targetNic !== $nic) {
                $updatedRows = $userModel->query(
                    "SELECT first_name, last_name, user_id, nic, email, phone_number, image
                     FROM users
                     WHERE nic = :nic
                     LIMIT 1",
                    ['nic' => $targetNic]
                );
            }

            $updated = !empty($updatedRows) ? $updatedRows[0] : null;
            $updatedName = trim((string) (($updated->first_name ?? $firstName) . ' ' . ($updated->last_name ?? $lastName)));
            if ($updatedName === '') {
                $updatedName = $updated->user_id ?? $idNumber;
            }

            $profileImageUrl = $this->normalizeImageUrl($updated->image ?? $newImagePath, $updatedName);
            $_SESSION['admin_profile_image'] = $profileImageUrl;
            $_SESSION['admin_profile_name'] = $updatedName;

            $this->jsonResponse([
                'success' => true,
                'message' => 'Profile updated successfully',
                'profile' => [
                    'name' => $updatedName,
                    'first_name' => $updated->first_name ?? $firstName,
                    'last_name' => $updated->last_name ?? $lastName,
                    'id_number' => $updated->user_id ?? $idNumber,
                    'nic' => $updated->nic ?? $nic,
                    'email' => $updated->email ?? $email,
                    'phone_number' => $updated->phone_number ?? $phoneNumber,
                    'image_url' => $profileImageUrl
                ]
            ]);
        } catch (Exception $e) {
            error_log('Admin profile update failed: ' . $e->getMessage());
            $this->jsonResponse(['success' => false, 'message' => 'Profile update failed']);
        }
    }

    public function profileData()
    {
        if (!isset($_SESSION['user_id'])) {
            $this->jsonResponse(['success' => false, 'message' => 'Unauthorized']);
        }

        try {
            $userModel = $this->model('User');
            $currentNic = trim((string) ($_SESSION['nic'] ?? ''));
            $currentUserId = trim((string) ($_SESSION['user_id'] ?? ''));

            $rows = $userModel->query(
                "SELECT user_id, nic, email, phone_number, first_name, last_name, image
                 FROM users
                 WHERE nic = :nic OR user_id = :user_id
                 LIMIT 1",
                [
                    'nic' => $currentNic,
                    'user_id' => $currentUserId
                ]
            );

            $row = !empty($rows) ? $rows[0] : null;
            if ($row === null) {
                $fallbackName = (string) ($_SESSION['admin_profile_name'] ?? ($_SESSION['user_id'] ?? 'Admin'));
                $this->jsonResponse([
                    'success' => true,
                    'profile' => [
                        'name' => $fallbackName,
                        'first_name' => $fallbackName,
                        'last_name' => '',
                        'id_number' => (string) ($_SESSION['user_id'] ?? 'Admin'),
                        'nic' => (string) ($_SESSION['nic'] ?? ''),
                        'email' => '',
                        'phone_number' => '',
                        'image_url' => (string) ($_SESSION['admin_profile_image'] ?? $this->buildInitialsAvatarUrl($fallbackName))
                    ]
                ]);
            }

            $firstName = trim((string) ($row->first_name ?? 'Admin'));
            $lastName = trim((string) ($row->last_name ?? ''));
            $fullName = trim($firstName . ' ' . $lastName);
            if ($fullName === '') {
                $fullName = (string) ($row->user_id ?? 'Admin');
            }

            $imageUrl = $this->normalizeImageUrl($row->image ?? '', $fullName);
            $_SESSION['admin_profile_image'] = $imageUrl;
            $_SESSION['admin_profile_name'] = $fullName;

            $this->jsonResponse([
                'success' => true,
                'profile' => [
                    'name' => $fullName,
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'id_number' => (string) ($row->user_id ?? ''),
                    'nic' => (string) ($row->nic ?? ''),
                    'email' => (string) ($row->email ?? ''),
                    'phone_number' => (string) ($row->phone_number ?? ''),
                    'image_url' => $imageUrl
                ]
            ]);
        } catch (Exception $e) {
            error_log('Admin profile data fetch failed: ' . $e->getMessage());
            $this->jsonResponse(['success' => false, 'message' => 'Failed to load profile']);
        }
    }

    public function recentNotices()
    {
        if (!isset($_SESSION['user_id'])) {
            $this->jsonResponse(['success' => false, 'message' => 'Unauthorized']);
        }

        try {
            $noticeModel = $this->model('NoticeModel');
            $rows = $noticeModel->getForAdmin(5);
            $notices = [];
            foreach ($rows as $row) {
                $notices[] = [
                    'title' => (string) ($row->title ?? 'Notice'),
                    'content' => (string) ($row->content ?? ''),
                    'created_at' => (string) ($row->created_at ?? '')
                ];
            }

            $this->jsonResponse(['success' => true, 'notices' => $notices]);
        } catch (Exception $e) {
            error_log('Admin recent notices fetch failed: ' . $e->getMessage());
            $this->jsonResponse(['success' => false, 'message' => 'Failed to load notices']);
        }
    }

    public function addNotice()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['success' => false, 'message' => 'Invalid request method']);
        }

        $payload = json_decode(file_get_contents('php://input'), true);
        if (!is_array($payload)) {
            $this->jsonResponse(['success' => false, 'message' => 'Invalid payload']);
        }

        $title = trim((string)($payload['title'] ?? ''));
        $content = trim((string)($payload['content'] ?? ''));

        if ($title === '' || $content === '') {
            $this->jsonResponse(['success' => false, 'message' => 'Title and content are required']);
        }

        try {
            $noticeModel = $this->model('NoticeModel');
            $createdBy = trim((string)($_SESSION['user_id'] ?? 'Admin'));
            if ($createdBy === '') {
                $createdBy = 'Admin';
            }

            $noticeModel->createNotice($title, $content, $createdBy, 'present_team');

            $this->jsonResponse(['success' => true, 'message' => 'Notice added successfully']);
        } catch (Exception $e) {
            error_log('Failed to add notice: ' . $e->getMessage());
            $this->jsonResponse(['success' => false, 'message' => 'Failed to add notice']);
        }
    }

    public function updateNotice()
    {
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
            error_log('Failed to update notice: ' . $e->getMessage());
            $this->jsonResponse(['success' => false, 'message' => 'Failed to update notice']);
        }
    }

    public function deleteNotice()
    {
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
            error_log('Failed to delete notice: ' . $e->getMessage());
            $this->jsonResponse(['success' => false, 'message' => 'Failed to delete notice']);
        }
    }
}
