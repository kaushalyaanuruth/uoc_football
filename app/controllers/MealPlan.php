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
        $defaults = $this->defaultMealItemsByType();
        $mealMap = $defaults;

        $playerModel = $this->model('PlayerModel');
        if (!$this->tableExists($playerModel, 'players') || !$this->tableExists($playerModel, 'team_players')) {
            return $mealMap;
        }

        $teamRows = $playerModel->query(
            "SELECT tp.team_id
             FROM players p
             JOIN team_players tp ON tp.player_id = p.player_id
             WHERE p.nic = :nic
             ORDER BY tp.team_id DESC
             LIMIT 1",
            ['nic' => $nic]
        );

        if (empty($teamRows)) {
            return $mealMap;
        }

        $teamId = (int) ($teamRows[0]->team_id ?? 0);
        if ($teamId <= 0 || !$this->tableExists($playerModel, 'meal_plans')) {
            return $mealMap;
        }

        $mealRows = $playerModel->query(
            "SELECT meal_id
             FROM meal_plans
             WHERE team_id = :team_id
             ORDER BY updated_date DESC, meal_id DESC
             LIMIT 1",
            ['team_id' => $teamId]
        );

        if (empty($mealRows)) {
            return $mealMap;
        }

        $mealId = (int) ($mealRows[0]->meal_id ?? 0);
        if ($mealId <= 0) {
            return $mealMap;
        }

        $tableMap = [
            'Breakfast' => 'breakfast',
            'Lunch' => 'lunch',
            'Dinner' => 'dinner',
        ];

        foreach ($tableMap as $slot => $tableName) {
            if (!$this->tableExists($playerModel, $tableName)) {
                continue;
            }

            $rows = $playerModel->query(
                "SELECT meal
                 FROM {$tableName}
                 WHERE meal_id = :meal_id",
                ['meal_id' => $mealId]
            );

            $items = [];
            foreach ($rows as $row) {
                $value = trim((string) ($row->meal ?? ''));
                if ($value !== '') {
                    $items[] = $value;
                }
            }

            if (!empty($items)) {
                $mealMap[$slot] = array_values(array_unique($items));
            }
        }

        return $mealMap;
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

        $mealItemsByType = $this->getPlayerTeamMealMap((string) ($_SESSION['nic'] ?? ''));

        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        $mealsByDay = [];
        foreach ($days as $day) {
            $mealsByDay[$day] = [
                'Breakfast' => [
                    'items' => $mealItemsByType['Breakfast'],
                    'calories' => $this->estimateCalories($mealItemsByType['Breakfast']),
                ],
                'Lunch' => [
                    'items' => $mealItemsByType['Lunch'],
                    'calories' => $this->estimateCalories($mealItemsByType['Lunch']),
                ],
                'Dinner' => [
                    'items' => $mealItemsByType['Dinner'],
                    'calories' => $this->estimateCalories($mealItemsByType['Dinner']),
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
            'player_image' => $this->getPlayerImageBySession()
        ];

        $this->view('mealPlan', $data);
    }
}
