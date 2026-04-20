<?php

class MealPlan extends Controller
{
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

    private function normalizeImageUrl($imagePath)
    {
        if (empty($imagePath)) {
            return ROOT . '/assets/images/adminDashboard/header/avatar.jpg';
        }

        $normalized = str_replace('\\', '/', ltrim((string)$imagePath, '/'));
        return ROOT . '/' . $normalized;
    }

    private function getPlayerImageBySession()
    {
        $nic = $_SESSION['nic'] ?? '';
        if ($nic === '') {
            return $this->normalizeImageUrl('');
        }

        $playerModel = $this->model('PlayerModel');
        $rows = $playerModel->query(
            "SELECT u.image FROM users u WHERE u.nic = :nic LIMIT 1",
            ['nic' => $nic]
        );

        return $this->normalizeImageUrl($rows[0]->image ?? '');
    }

    private function getPlayerNotices($limit = 6)
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
                'fat' => 55
            ],
            'hydration_tip' => 'Drink 500ml of water before and after training sessions for optimal hydration.',
            'coach_updates' => [
                [
                    'type' => 'warning',
                    'title' => 'Increase Protein Intake',
                    'message' => 'Your recent performance metrics show you need more protein recovery. Aim for 2g per kg body weight daily.'
                ],
                [
                    'type' => 'success',
                    'title' => 'Great Progress!',
                    'message' => 'Your meal plan compliance is excellent. Keep maintaining this consistency for better results.'
                ]
            ],
            'player_image' => $this->getPlayerImageBySession(),
            'notices' => $this->getPlayerNotices(),
        ];

        $this->view('mealPlan', $data);
    }
}
