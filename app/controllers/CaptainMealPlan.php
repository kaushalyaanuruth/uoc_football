<?php

class CaptainMealPlan extends Controller
{
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

    private function getCaptainNotices($limit = 6)
    {
        $noticeModel = $this->model('NoticeModel');
        $rows = [];

        try {
            $rows = $noticeModel->getRecent($limit, 'present_team');
        } catch (Exception $e) {
            $rows = [];
        }

        $notices = [];
        foreach ($rows as $row) {
            $notices[] = [
                'id' => (int) ($row->notice_id ?? 0),
                'title' => $row->title ?? 'Notice',
                'content' => $row->content ?? '',
                'author' => $row->created_by ?? 'Admin',
                'date' => !empty($row->created_at) ? date('M d, Y h:i A', strtotime($row->created_at)) : '',
            ];
        }

        return $notices;
    }

    private function tableExists($model, $tableName)
    {
        $safeTableName = preg_replace('/[^a-zA-Z0-9_]/', '', (string) $tableName);
        if ($safeTableName === '') {
            return false;
        }

        $rows = $model->query("SHOW TABLES LIKE '{$safeTableName}'");
        return !empty($rows);
    }

    private function defaultMealItemsByType()
    {
        return [
            'Breakfast' => ['Oatmeal with fruits', 'Boiled eggs', 'Green tea', 'Banana'],
            'Lunch' => ['Basmati or Red rice', 'Chicken, Egg, Fish', 'Vegetable(minimum 3)', 'Paip', 'Yogurt', 'Fruits'],
            'Dinner' => ['Grilled chicken breast', 'Steamed vegetables', 'Boiled sweet potato', 'Glass of warm milk'],
        ];
    }

    private function estimateCalories($items)
    {
        $count = is_array($items) ? count($items) : 0;
        return 1900 + ($count * 130);
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

    private function getPlayerTeamMealMap($nic)
    {
        $defaultsByType = $this->defaultMealItemsByType();
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

        $weeklyMap = [];
        foreach ($days as $day) {
            $weeklyMap[$day] = $defaultsByType;
        }

        $mealModel = $this->model('CoachMealplanModel');
        if (!$mealModel || $nic === '') {
            return $weeklyMap;
        }

        $teamId = $mealModel->getTeamIdByPlayerNic($nic);
        if (!$teamId) {
            return $weeklyMap;
        }

        $dbWeekly = $mealModel->getWeeklyTeamMealPlan($teamId);
        foreach ($days as $day) {
            $dayKey = strtolower($day);
            $dbDay = $dbWeekly[$dayKey] ?? [];

            foreach (['Breakfast' => 'breakfast', 'Lunch' => 'lunch', 'Dinner' => 'dinner'] as $slot => $mealType) {
                $items = $dbDay[$mealType] ?? [];
                if (is_array($items) && !empty($items)) {
                    $weeklyMap[$day][$slot] = array_values(array_unique(array_filter(array_map('trim', $items))));
                }
            }
        }

        return $weeklyMap;
    }

    private function normalizeImageUrl($imagePath, $displayName = '')
    {
        if (empty($imagePath)) {
            return $this->buildInitialsAvatarUrl($displayName);
        }

        $normalized = str_replace('\\', '/', ltrim((string) $imagePath, '/'));
        return ROOT . '/' . $normalized;
    }

    private function getCaptainImageBySession()
    {
        $nic = $_SESSION['nic'] ?? '';
        if ($nic === '') {
            return $this->normalizeImageUrl('', 'Captain');
        }

        $playerModel = $this->model('PlayerModel');
        $rows = $playerModel->query(
            "SELECT u.image, u.first_name, u.last_name, u.user_id FROM users u WHERE u.nic = :nic LIMIT 1",
            ['nic' => $nic]
        );

        $row = $rows[0] ?? null;
        $fullName = trim((($row->first_name ?? '') . ' ' . ($row->last_name ?? '')));
        if ($fullName === '') {
            $fullName = (string) ($row->user_id ?? $nic);
        }

        return $this->normalizeImageUrl($row->image ?? '', $fullName);
    }

    public function index()
    {
        $this->ensureCaptainAccess();

        $mealItemsByDay = $this->getPlayerTeamMealMap((string) ($_SESSION['nic'] ?? ''));

        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        $mealsByDay = [];
        foreach ($days as $day) {
            $dayMeals = $mealItemsByDay[$day] ?? $this->defaultMealItemsByType();
            $mealsByDay[$day] = [
                'Breakfast' => [
                    'items' => $dayMeals['Breakfast'] ?? [],
                    'calories' => $this->estimateCalories($dayMeals['Breakfast'] ?? []),
                ],
                'Lunch' => [
                    'items' => $dayMeals['Lunch'] ?? [],
                    'calories' => $this->estimateCalories($dayMeals['Lunch'] ?? []),
                ],
                'Dinner' => [
                    'items' => $dayMeals['Dinner'] ?? [],
                    'calories' => $this->estimateCalories($dayMeals['Dinner'] ?? []),
                ],
            ];
        }

        $data = [
            'meals' => $mealsByDay,
            'nutrition' => [
                'calories' => 2440,
                'protein' => 185,
                'carbs' => 310,
                'fat' => 55,
            ],
            'hydration_tip' => 'Drink 500ml of water before and after training sessions for optimal hydration.',
            'coach_updates' => [
                [
                    'type' => 'warning',
                    'title' => 'Increase Protein Intake',
                    'message' => 'Your recent performance metrics show you need more protein recovery. Aim for 2g per kg body weight daily.',
                ],
                [
                    'type' => 'success',
                    'title' => 'Great Progress!',
                    'message' => 'Your meal plan compliance is excellent. Keep maintaining this consistency for better results.',
                ],
            ],
            'captain_image' => $this->getCaptainImageBySession(),
            'notices' => $this->getCaptainNotices(),
        ];

        $this->view('captain/mealPlan', $data);
    }
}
