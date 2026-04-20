<?php

require_once __DIR__ . '/CoachBaseController.php';

class coachMealplan extends CoachBaseController {

    private function defaultTeamMealPlan()
    {
        return [
            'breakfast' => [
                'Basmati or Red Rice',
                'Chicken, Egg, Fish',
                'Vegetable(minimum 3)',
                'Paip',
                'Yogurt',
                'Fruits'
            ],
            'lunch' => [
                'Basmati or Red Rice',
                'Chicken, Egg, Fish',
                'Vegetable(minimum 3)',
                'Paip',
                'Yogurt',
                'Fruits'
            ],
            'dinner' => [
                'Basmati or Red Rice',
                'Chicken, Egg, Fish',
                'Vegetable(minimum 3)',
                'Paip',
                'Yogurt',
                'Fruits'
            ],
        ];
    }

    private function defaultWeeklyTeamMealPlan()
    {
        $weekly = [];
        $dayPlan = $this->defaultTeamMealPlan();

        foreach (['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day) {
            $weekly[$day] = $dayPlan;
        }

        return $weekly;
    }

    private function normalizeDayKey($day)
    {
        $value = strtolower(trim((string) $day));
        $allowed = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

        return in_array($value, $allowed, true) ? $value : 'monday';
    }

    private function resolveCoachTeamContext()
    {
        $mealModel = $this->model('CoachMealplanModel');
        $nic = (string) ($_SESSION['nic'] ?? '');
        $teamId = $mealModel ? $mealModel->getTeamIdByCoachNic($nic) : null;

        if ($teamId === null && $mealModel) {
            $teamId = $mealModel->getAnyTeamId();
        }

        return [
            'mealModel' => $mealModel,
            'teamId' => $teamId,
        ];
    }

    private function responseJson($payload, $status = 200)
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($payload);
        exit();
    }

    private function getTeamMealPlanPayload($mealModel, $teamId)
    {
        $defaults = $this->defaultWeeklyTeamMealPlan();
        $dbPlan = ($mealModel && $teamId) ? $mealModel->getWeeklyTeamMealPlan($teamId) : [];

        foreach ($defaults as $day => $defaultDayPlan) {
            if (empty($dbPlan[$day]) || !is_array($dbPlan[$day])) {
                $dbPlan[$day] = $defaultDayPlan;
                continue;
            }

            foreach (['breakfast', 'lunch', 'dinner'] as $mealType) {
                if (empty($dbPlan[$day][$mealType]) || !is_array($dbPlan[$day][$mealType])) {
                    $dbPlan[$day][$mealType] = $defaultDayPlan[$mealType];
                }
            }
        }

        return $dbPlan;
    }

    public function index() {
        $this->ensureCoachAccess();

        $context = $this->resolveCoachTeamContext();
        $plan = $this->getTeamMealPlanPayload($context['mealModel'], $context['teamId']);

        $this->view('coachMealPlan', $this->buildCoachViewData([
            'team_meal_plan' => $plan,
        ]));
    }

    public function data()
    {
        $this->ensureCoachAccess();

        try {
            $context = $this->resolveCoachTeamContext();
            if (!$context['mealModel'] || !$context['teamId']) {
                $this->responseJson([
                    'success' => true,
                    'plan' => $this->defaultWeeklyTeamMealPlan(),
                ]);
            }

            $plan = $this->getTeamMealPlanPayload($context['mealModel'], $context['teamId']);
            $this->responseJson([
                'success' => true,
                'plan' => $plan,
            ]);
        } catch (Exception $e) {
            $this->responseJson([
                'success' => false,
                'message' => 'Unable to load meal plan data',
            ], 500);
        }
    }

    public function save()
    {
        $this->ensureCoachAccess();

        if (strtoupper($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            $this->responseJson(['success' => false, 'message' => 'Method not allowed'], 405);
        }

        try {
            $context = $this->resolveCoachTeamContext();
            if (!$context['mealModel'] || !$context['teamId']) {
                $this->responseJson(['success' => false, 'message' => 'Coach team not found'], 400);
            }

            $raw = file_get_contents('php://input');
            $payload = json_decode($raw, true);

            $mealType = strtolower(trim((string) ($payload['mealType'] ?? '')));
            $day = $this->normalizeDayKey($payload['day'] ?? 'monday');
            $items = $payload['items'] ?? [];

            if (!in_array($mealType, ['breakfast', 'lunch', 'dinner'], true) || !is_array($items)) {
                $this->responseJson(['success' => false, 'message' => 'Invalid payload'], 422);
            }

            $saved = $context['mealModel']->saveTeamMealType($context['teamId'], $mealType, $items, $day);
            if (!$saved) {
                $this->responseJson(['success' => false, 'message' => 'Unable to save meal plan'], 500);
            }

            $plan = $this->getTeamMealPlanPayload($context['mealModel'], $context['teamId']);
            $this->responseJson([
                'success' => true,
                'plan' => $plan,
                'message' => 'Meal plan saved successfully',
            ]);
        } catch (Exception $e) {
            $this->responseJson([
                'success' => false,
                'message' => 'Database save failed',
            ], 500);
        }
    }

    public function reset()
    {
        $this->ensureCoachAccess();

        if (strtoupper($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            $this->responseJson(['success' => false, 'message' => 'Method not allowed'], 405);
        }

        try {
            $context = $this->resolveCoachTeamContext();
            if (!$context['mealModel'] || !$context['teamId']) {
                $this->responseJson(['success' => false, 'message' => 'Coach team not found'], 400);
            }

            $defaults = $this->defaultTeamMealPlan();
            foreach (['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day) {
                foreach ($defaults as $mealType => $items) {
                    $saved = $context['mealModel']->saveTeamMealType($context['teamId'], $mealType, $items, $day);
                    if (!$saved) {
                        $this->responseJson(['success' => false, 'message' => 'Unable to reset meal plan'], 500);
                    }
                }
            }

            $plan = $this->getTeamMealPlanPayload($context['mealModel'], $context['teamId']);
            $this->responseJson([
                'success' => true,
                'plan' => $plan,
                'message' => 'Meal plan reset successfully',
            ]);
        } catch (Exception $e) {
            $this->responseJson([
                'success' => false,
                'message' => 'Database reset failed',
            ], 500);
        }
    }
}