<?php

class PlayerDashboard extends Controller
{
    private function buildInitialsAvatarUrl($displayName)
    {
        $name = trim((string)$displayName);
        if ($name === '') {
            $name = 'Player';
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
            $initials = 'P';
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

        $normalized = str_replace('\\', '/', ltrim((string)$imagePath, '/'));
        return ROOT . '/' . $normalized;
    }

    private function tableExists($model, $tableName)
    {
        $safeTableName = preg_replace('/[^a-zA-Z0-9_]/', '', (string)$tableName);
        if ($safeTableName === '') {
            return false;
        }

        $rows = $model->query("SHOW TABLES LIKE '{$safeTableName}'");
        return !empty($rows);
    }

    public function index()
    {
        if (!isset($_SESSION['user_id'], $_SESSION['nic'])) {
            header('Location: ' . ROOT . '/login');
            exit();
        }

        if (($_SESSION['user_type'] ?? '') !== 'player') {
            header('Location: ' . ROOT . '/login');
            exit();
        }

        if (!empty($_SESSION['must_change_password'])) {
            header('Location: ' . ROOT . '/PasswordChange');
            exit();
        }

        $nic = $_SESSION['nic'];
        $playerModel = $this->model('PlayerModel');

        $playerRows = $playerModel->query(
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

        if (empty($playerRows)) {
            header('Location: ' . ROOT . '/login?error=invalid_credentials');
            exit();
        }

        $player = $playerRows[0];
        $_SESSION['player_id'] = (int)$player->player_id;

        $fullName = trim(($player->first_name ?? '') . ' ' . ($player->last_name ?? ''));
        if ($fullName === '') {
            $fullName = $player->nic;
        }

        $teamId = null;
        $teamRows = $playerModel->query(
            "SELECT t.team_id, t.season
            FROM team_players tp
            JOIN teams t ON t.team_id = tp.team_id
            WHERE tp.player_id = :player_id
            ORDER BY t.team_id DESC
            LIMIT 1",
            ['player_id' => $player->player_id]
        );

        if (!empty($teamRows)) {
            $teamId = (int)$teamRows[0]->team_id;
        }

        $defaultMeals = [
            'Breakfast' => ['Oatmeal with fruits', 'Boiled eggs', 'Green tea', 'Banana'],
            'Lunch' => ['Basmati or Red rice', 'Chicken, Egg, Fish', 'Vegetable(minimam 3)', 'Pala', 'Yogurt', 'Fruits'],
            'Dinner' => ['Grilled chicken breast', 'Steamed vegetables', 'Boiled sweet potato', 'Glass of warm milk']
        ];

        $mealPlan = $defaultMeals;
        if ($teamId !== null && $this->tableExists($playerModel, 'meal_plans')) {
            $mealRows = $playerModel->query(
                "SELECT meal_id
                FROM meal_plans
                WHERE team_id = :team_id
                ORDER BY updated_date DESC
                LIMIT 1",
                ['team_id' => $teamId]
            );

            if (!empty($mealRows)) {
                $mealId = (int)$mealRows[0]->meal_id;

                if ($this->tableExists($playerModel, 'breakfast')) {
                    $breakfastRows = $playerModel->query("SELECT meal FROM breakfast WHERE meal_id = :meal_id", ['meal_id' => $mealId]);
                    $mealPlan['Breakfast'] = array_map(fn($row) => $row->meal, $breakfastRows);
                }

                if ($this->tableExists($playerModel, 'lunch')) {
                    $lunchRows = $playerModel->query("SELECT meal FROM lunch WHERE meal_id = :meal_id", ['meal_id' => $mealId]);
                    $mealPlan['Lunch'] = array_map(fn($row) => $row->meal, $lunchRows);
                }

                if ($this->tableExists($playerModel, 'dinner')) {
                    $dinnerRows = $playerModel->query("SELECT meal FROM dinner WHERE meal_id = :meal_id", ['meal_id' => $mealId]);
                    $mealPlan['Dinner'] = array_map(fn($row) => $row->meal, $dinnerRows);
                }
            }
        }

        foreach (['Breakfast', 'Lunch', 'Dinner'] as $slot) {
            if (empty($mealPlan[$slot])) {
                $mealPlan[$slot] = $defaultMeals[$slot];
            }
        }

        $todayDayName = date('l');
        $mealPlanByDay = [];
        $daysOfWeek = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        foreach ($daysOfWeek as $dayName) {
            // Current schema stores meal types without day columns, so each day maps to the latest team plan.
            $mealPlanByDay[$dayName] = $mealPlan;
        }

        $todayMealPlan = $mealPlanByDay[$todayDayName] ?? $mealPlan;

        $eventModel = $this->model('EventModel');
        $upcomingEvents = [];
        try {
            $upcomingEvents = $eventModel->getUpcoming();
        } catch (Exception $e) {
            $upcomingEvents = [];
        }

        $nextPractice = [
            'date' => 'No training session scheduled',
            'time_of_day' => '',
            'time' => 'TBA'
        ];

        foreach ($upcomingEvents as $eventRow) {
            if (strtolower((string)($eventRow->event_type ?? '')) !== 'training') {
                continue;
            }

            $practiceDate = !empty($eventRow->date) ? strtotime($eventRow->date) : false;
            $practiceTime = !empty($eventRow->event_time) ? strtotime($eventRow->event_time) : false;

            $nextPractice = [
                'date' => $practiceDate ? date('j, F', $practiceDate) : 'Date not set',
                'time_of_day' => $practiceTime ? (date('A', $practiceTime) === 'AM' ? 'morning' : 'evening') : '',
                'time' => $practiceTime ? date('g:i A', $practiceTime) : 'TBA'
            ];
            break;
        }

        $nextEvent = [
            'type' => 'No Upcoming Event',
            'title' => 'No event scheduled',
            'date' => 'TBA',
            'location' => 'Ground'
        ];

        $slugCountdown = 0;
        $nextMatchCountdownTitle = 'No upcoming match';
        if (!empty($upcomingEvents)) {
            $eventDate = !empty($upcomingEvents[0]->date) ? strtotime($upcomingEvents[0]->date) : false;
            if ($eventDate) {
                $dayDiff = (int) floor(($eventDate - strtotime(date('Y-m-d'))) / 86400);
                $slugCountdown = max(0, $dayDiff);
            }

            $eventType = ucfirst(strtolower((string)($upcomingEvents[0]->event_type ?? 'Event')));

            $eventDateText = $eventDate ? date('l, F j', $eventDate) : 'Date not set';
            if (!empty($upcomingEvents[0]->event_time)) {
                $eventDateText .= ' at ' . date('g:i A', strtotime($upcomingEvents[0]->event_time));
            }

            $nextEvent = [
                'type' => $eventType,
                'title' => $upcomingEvents[0]->title ?: 'UOC Football Event',
                'date' => $eventDateText,
                'location' => $upcomingEvents[0]->location ?: 'Ground'
            ];
        }

        // Countdown must use the nearest upcoming match with tolerant type matching.
        $now = new DateTime('now');
        $todayMidnight = new DateTime('today');
        $closestMatchAt = null;
        $closestMatchTitle = 'UOC Football Match';

        foreach ($upcomingEvents as $eventRow) {
            $eventTypeRaw = strtolower(trim((string) ($eventRow->event_type ?? '')));
            $eventTitleRaw = strtolower(trim((string) ($eventRow->title ?? '')));
            $isMatch = (
                $eventTypeRaw === 'match'
                || strpos($eventTypeRaw, 'match') !== false
                || strpos($eventTitleRaw, 'match') !== false
            );
            if (!$isMatch) {
                continue;
            }

            $dateRaw = trim((string) ($eventRow->date ?? ''));
            if ($dateRaw === '') {
                continue;
            }

            $timeRaw = trim((string) ($eventRow->event_time ?? ''));
            $matchAt = DateTime::createFromFormat('Y-m-d H:i:s', $dateRaw . ' ' . ($timeRaw !== '' ? $timeRaw : '00:00:00'));
            if (!$matchAt) {
                $matchAt = DateTime::createFromFormat('Y-m-d H:i', $dateRaw . ' ' . ($timeRaw !== '' ? $timeRaw : '00:00'));
            }
            if (!$matchAt) {
                $matchAt = DateTime::createFromFormat('Y-m-d', $dateRaw);
            }
            if (!$matchAt) {
                continue;
            }

            // Ignore matches that are already in the past.
            $hasTime = $timeRaw !== '';
            if ($hasTime) {
                if ($matchAt < $now) {
                    continue;
                }
            } else {
                if ($matchAt < $todayMidnight) {
                    continue;
                }
            }

            if ($closestMatchAt === null || $matchAt < $closestMatchAt) {
                $closestMatchAt = $matchAt;
                $closestMatchTitle = !empty($eventRow->title)
                    ? (string) $eventRow->title
                    : 'UOC Football Match';
            }
        }

        if ($closestMatchAt !== null) {
            // Show 0 when match is today, 1 for tomorrow, etc.
            $dayDiff = (int) $todayMidnight->diff($closestMatchAt)->format('%r%a');
            $slugCountdown = max(0, $dayDiff);
            $nextMatchCountdownTitle = $closestMatchTitle;
        } else {
            $slugCountdown = 0;
            $nextMatchCountdownTitle = 'No upcoming match';
        }

        $testSummary = [
            'total_tests' => 0,
            'latest_test' => 'No tests recorded yet'
        ];

        if ($this->tableExists($playerModel, 'test_results')) {
            $testCountRows = $playerModel->query(
                "SELECT COUNT(*) AS total_tests
                FROM test_results
                WHERE player_id = :player_id",
                ['player_id' => $player->player_id]
            );

            $latestTestRows = $playerModel->query(
                "SELECT test_type, date
                FROM test_results
                WHERE player_id = :player_id
                ORDER BY date DESC
                LIMIT 1",
                ['player_id' => $player->player_id]
            );

            $testSummary['total_tests'] = (int)($testCountRows[0]->total_tests ?? 0);
            if (!empty($latestTestRows)) {
                $testSummary['latest_test'] = $latestTestRows[0]->test_type . ' - ' . date('M d, Y', strtotime($latestTestRows[0]->date));
            }
        }

        $attendance = [
            'total' => 0,
            'present' => 0,
            'absent' => 0,
            'present_rate' => 0
        ];

        if ($this->tableExists($playerModel, 'attendance')) {
            $attendanceRows = $playerModel->query(
                "SELECT
                    COUNT(*) AS total,
                    SUM(CASE WHEN status = 'Present' THEN 1 ELSE 0 END) AS present,
                    SUM(CASE WHEN status = 'Absent' THEN 1 ELSE 0 END) AS absent
                FROM attendance
                WHERE player_id = :player_id",
                ['player_id' => $player->player_id]
            );

            if (!empty($attendanceRows)) {
                $attendance['total'] = (int)($attendanceRows[0]->total ?? 0);
                $attendance['present'] = (int)($attendanceRows[0]->present ?? 0);
                $attendance['absent'] = (int)($attendanceRows[0]->absent ?? 0);
                if ($attendance['total'] > 0) {
                    $attendance['present_rate'] = (int)round(($attendance['present'] / $attendance['total']) * 100);
                }
            }
        }

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
                'title' => $row->title ?? 'Notice',
                'text' => $row->content ?? '',
                'author' => $row->created_by ?? 'Admin',
                'date' => !empty($row->created_at) ? date('M d, Y h:i A', strtotime($row->created_at)) : ''
            ];
        }

        if (empty($notices)) {
            $notices[] = [
                'title' => 'No notices yet',
                'text' => 'Admin notices for present team members will appear here.',
                'author' => 'System',
                'date' => ''
            ];
        }

        $imagePath = $this->normalizeImageUrl($player->image ?? '', $fullName);

        $data = [
            'player_name' => $fullName,
            'player_role' => $player->role,
            'player_position' => $player->position,
            'player_image' => $imagePath,
            'player_profile' => [
                'first_name' => $player->first_name ?? '',
                'last_name' => $player->last_name ?? '',
                'id_number' => $player->user_id ?? $player->nic,
                'nic' => $player->nic,
                'email' => $player->email ?? '',
                'phone_number' => $player->phone_number ?? ''
            ],
            'date' => date('j, F Y'),
            'time' => date('H:i A'),
            'next_practice' => $nextPractice,
            'next_event' => $nextEvent,
            'slug_countdown' => $slugCountdown,
            'next_match_countdown_title' => $nextMatchCountdownTitle,
            'notices' => $notices,
            'meal_plan' => $mealPlan,
            'meal_plan_by_day' => $mealPlanByDay,
            'today_day_name' => $todayDayName,
            'today_meal_plan' => $todayMealPlan,
            'test_summary' => $testSummary,
            'attendance' => $attendance,
            'team' => $teamRows[0] ?? null
        ];

        $this->view('playerDashboard', $data);
    }

    public function updateProfile()
    {
        header('Content-Type: application/json');

        if (!isset($_SESSION['user_id'], $_SESSION['nic']) || (($_SESSION['user_type'] ?? '') !== 'player')) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit();
        }

        try {
            $currentNic = $_SESSION['nic'];
            $firstName = trim((string)($_POST['first_name'] ?? ''));
            $lastName = trim((string)($_POST['last_name'] ?? ''));
            $idNumber = trim((string)($_POST['id_number'] ?? ''));
            $email = trim((string)($_POST['email'] ?? ''));
            $phoneNumber = trim((string)($_POST['phone_number'] ?? ''));
            $currentPassword = (string) ($_POST['current_password'] ?? '');
            $newPassword = (string) ($_POST['new_password'] ?? '');
            $confirmPassword = (string) ($_POST['confirm_password'] ?? '');

            if ($firstName === '' || $idNumber === '') {
                echo json_encode(['success' => false, 'message' => 'First name and ID number are required']);
                exit();
            }

            if ($phoneNumber !== '' && !preg_match('/^\d{10}$/', $phoneNumber)) {
                echo json_encode(['success' => false, 'message' => 'Phone number must contain exactly 10 digits']);
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

            $wantsPasswordChange = $currentPassword !== '' || $newPassword !== '' || $confirmPassword !== '';
            if ($wantsPasswordChange) {
                if ($currentPassword === '' || $newPassword === '' || $confirmPassword === '') {
                    echo json_encode(['success' => false, 'message' => 'Current password, new password and confirm password are required']);
                    exit();
                }

                if (!$userModel->verifyCurrentPasswordByNic($currentNic, $currentPassword)) {
                    echo json_encode(['success' => false, 'message' => 'Current password is incorrect']);
                    exit();
                }

                if (strlen($newPassword) < 6) {
                    echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters']);
                    exit();
                }

                if ($newPassword !== $confirmPassword) {
                    echo json_encode(['success' => false, 'message' => 'New password and confirm password do not match']);
                    exit();
                }

                if ($newPassword === '123456') {
                    echo json_encode(['success' => false, 'message' => 'Please choose a password different from the default password']);
                    exit();
                }
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

                $newFileName = uniqid('player_profile_') . '.' . $fileExt;
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

            if ($wantsPasswordChange) {
                $updateFields['password'] = password_hash($newPassword, PASSWORD_BCRYPT);
            }

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
            error_log('Player profile update failed: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Profile update failed']);
        }

        exit();
    }

    public function profileData()
    {
        header('Content-Type: application/json');

        if (!isset($_SESSION['user_id'], $_SESSION['nic']) || (($_SESSION['user_type'] ?? '') !== 'player')) {
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
