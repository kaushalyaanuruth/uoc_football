<?php

class MealPlan extends Controller
{
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

        $data = [
            'meals' => [
                'Monday' => [
                    'calories' => 2450,
                    'items' => ['Oatmeal with berries', 'Whole wheat toast', 'Green tea', 'Almonds']
                ],
                'Tuesday' => [
                    'calories' => 2380,
                    'items' => ['Scrambled eggs', 'Whole grain bread', 'Fresh orange juice', 'Mixed nuts']
                ],
                'Wednesday' => [
                    'calories' => 2510,
                    'items' => ['Protein pancakes', 'Banana', 'Honey drizzle', 'Greek yogurt']
                ],
                'Thursday' => [
                    'calories' => 2420,
                    'items' => ['Quinoa bowl', 'Chia seeds', 'Almond butter', 'Berries']
                ],
                'Friday' => [
                    'calories' => 2390,
                    'items' => ['Egg whites omelet', 'Whole wheat toast', 'Fresh fruit', 'Low-fat milk']
                ],
                'Saturday' => [
                    'calories' => 2480,
                    'items' => ['Power smoothie', 'Protein powder', 'Banana', 'Spinach', 'Almond milk']
                ]
            ],
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
