<?php
class coachMealplan extends Controller {

    private $mealplanModel;

    public function __construct() {
        $this->mealplanModel = $this->model('MealplanModel');

        // Ensure coach is logged in
        /*if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'coach') {
            redirect('coach');
        }*/
    }

    /**
     * INDEX — Display current meal plan
     */
    public function index() {
        $teamId = $_SESSION['team_id'];

        // Fetch or create meal plan for the team
        $mealPlan = $this->mealplanModel->getMealPlanByTeam($teamId);

        if (!$mealPlan) {
            // Auto-create an empty meal plan for this team
            $mealPlanId = $this->mealplanModel->createMealPlan($teamId);
        } else {
            $mealPlanId = $mealPlan->meal_id;
        }

        $data = [
            'meal_plan_id' => $mealPlanId,
            'updated_date' => $mealPlan ? $mealPlan->updated_date : date('Y-m-d'),
            'breakfast'    => $this->mealplanModel->getMealItems('breakfast', $mealPlanId),
            'lunch'        => $this->mealplanModel->getMealItems('lunch', $mealPlanId),
            'dinner'       => $this->mealplanModel->getMealItems('dinner', $mealPlanId),
        ];

        $this->view('coachMealPlan', $data);
    }

    /**
     * ADD — Add a new meal item via AJAX
     * POST: meal_type, meal, amount
     */
    public function add() {
        $this->ajaxOnly();

        $mealType = $this->sanitize($_POST['meal_type'] ?? '');
        $meal     = $this->sanitize($_POST['meal']      ?? '');
        $amount   = $this->sanitize($_POST['amount']    ?? '');
        $teamId   = $_SESSION['team_id'];

        if (!$this->isValidMealType($mealType) || empty($meal) || empty($amount)) {
            $this->jsonResponse(['success' => false, 'message' => 'Invalid input data.']);
        }

        // Ensure meal plan exists
        $mealPlan = $this->mealplanModel->getMealPlanByTeam($teamId);
        if (!$mealPlan) {
            $mealPlanId = $this->mealplanModel->createMealPlan($teamId);
        } else {
            $mealPlanId = $mealPlan->meal_id;
        }

        $newId = $this->mealplanModel->addMealItem($mealType, $mealPlanId, $meal, $amount);

        if ($newId) {
            $this->jsonResponse([
                'success' => true,
                'message' => ucfirst($mealType) . ' item added successfully.',
                'item' => [
                    'id'     => $newId,
                    'meal'   => $meal,
                    'amount' => $amount
                ]
            ]);
        } else {
            $this->jsonResponse(['success' => false, 'message' => 'Failed to add meal item.']);
        }
    }

    /**
     * UPDATE — Update an existing meal item via AJAX
     * POST: meal_type, item_id, meal, amount
     */
    public function update() {
        $this->ajaxOnly();

        $mealType = $this->sanitize($_POST['meal_type'] ?? '');
        $itemId   = (int)($_POST['item_id']   ?? 0);
        $meal     = $this->sanitize($_POST['meal']      ?? '');
        $amount   = $this->sanitize($_POST['amount']    ?? '');

        if (!$this->isValidMealType($mealType) || $itemId <= 0 || empty($meal) || empty($amount)) {
            $this->jsonResponse(['success' => false, 'message' => 'Invalid input data.']);
        }

        $result = $this->mealplanModel->updateMealItem($mealType, $itemId, $meal, $amount);

        if ($result) {
            $this->jsonResponse([
                'success' => true,
                'message' => 'Meal item updated successfully.',
                'item' => [
                    'id'     => $itemId,
                    'meal'   => $meal,
                    'amount' => $amount
                ]
            ]);
        } else {
            $this->jsonResponse(['success' => false, 'message' => 'Failed to update meal item.']);
        }
    }

    /**
     * DELETE — Delete a meal item via AJAX
     * POST: meal_type, item_id
     */
    public function delete() {
        $this->ajaxOnly();

        $mealType = $this->sanitize($_POST['meal_type'] ?? '');
        $itemId   = (int)($_POST['item_id'] ?? 0);

        if (!$this->isValidMealType($mealType) || $itemId <= 0) {
            $this->jsonResponse(['success' => false, 'message' => 'Invalid input data.']);
        }

        $result = $this->mealplanModel->deleteMealItem($mealType, $itemId);

        if ($result) {
            $this->jsonResponse(['success' => true, 'message' => 'Meal item deleted successfully.']);
        } else {
            $this->jsonResponse(['success' => false, 'message' => 'Failed to delete meal item.']);
        }
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    private function isValidMealType(string $type): bool {
        return in_array($type, ['breakfast', 'lunch', 'dinner']);
    }

    private function sanitize(string $value): string {
        return htmlspecialchars(strip_tags(trim($value)));
    }

    private function ajaxOnly(): void {
        if (empty($_SERVER['HTTP_X_REQUESTED_WITH']) ||
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest') {
            http_response_code(403);
            exit('Forbidden');
        }
    }

    private function jsonResponse(array $data): void {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}