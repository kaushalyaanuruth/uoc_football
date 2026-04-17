<?php
class MealplanModel extends Model {

    // ─── Meal Plans ──────────────────────────────────────────────────────────

    /**
     * Get the meal plan record for a given team.
     */
    public function getMealPlanByTeam(string $teamId) {
        $sql = "SELECT * FROM meal_plans WHERE team_id = :team_id LIMIT 1";
        return $this->db->single($sql, [':team_id' => $teamId]);
    }

    /**
     * Create a new meal plan for a team and return the new meal_id.
     */
    public function createMealPlan(string $teamId): int {
        $sql = "INSERT INTO meal_plans (team_id, updated_date)
                VALUES (:team_id, CURDATE())";
        $this->db->execute($sql, [':team_id' => $teamId]);
        return (int)$this->db->lastInsertId();
    }

    /**
     * Touch the updated_date on the meal plan (call after any item change).
     */
    public function touchMealPlan(int $mealPlanId): void {
        $sql = "UPDATE meal_plans SET updated_date = CURDATE() WHERE meal_id = :id";
        $this->db->execute($sql, [':id' => $mealPlanId]);
    }

    // ─── Meal Items ──────────────────────────────────────────────────────────

    /**
     * Fetch all items for a specific meal type (breakfast|lunch|dinner).
     * Returns an array of stdClass objects with {id, meal, amount}.
     */
    public function getMealItems(string $mealType, int $mealPlanId): array {
        $idCol = $mealType . '_id';
        $sql   = "SELECT {$idCol} AS id, meal, amount
                  FROM {$mealType}
                  WHERE {$idCol} = :meal_plan_id";
        return $this->db->resultSet($sql, [':meal_plan_id' => $mealPlanId]) ?: [];
    }

    /**
     * Add a new item to a meal table.
     * Returns the new row ID on success, or 0 on failure.
     */
    public function addMealItem(string $mealType, int $mealPlanId, string $meal, string $amount): int {
        $idCol = $mealType . '_id';
        $sql   = "INSERT INTO {$mealType} ({$idCol}, meal, amount)
                  VALUES (:meal_plan_id, :meal, :amount)";

        $result = $this->db->execute($sql, [
            ':meal_plan_id' => $mealPlanId,
            ':meal'         => $meal,
            ':amount'       => $amount,
        ]);

        if ($result) {
            $this->touchMealPlan($mealPlanId);
            return (int)$this->db->lastInsertId();
        }
        return 0;
    }

    /**
     * Update an existing meal item.
     */
    public function updateMealItem(string $mealType, int $itemId, string $meal, string $amount): bool {
        $idCol = $mealType . '_id';
        $sql   = "UPDATE {$mealType}
                  SET meal = :meal, amount = :amount
                  WHERE {$idCol} = :item_id";

        $result = $this->db->execute($sql, [
            ':meal'    => $meal,
            ':amount'  => $amount,
            ':item_id' => $itemId,
        ]);

        if ($result) {
            // Refresh the parent meal plan's updated_date
            $mealPlan = $this->getMealPlanIdByItemId($mealType, $itemId);
            if ($mealPlan) {
                $this->touchMealPlan($mealPlan);
            }
        }
        return (bool)$result;
    }

    /**
     * Delete a meal item by its primary key.
     */
    public function deleteMealItem(string $mealType, int $itemId): bool {
        $idCol = $mealType . '_id';
        $sql   = "DELETE FROM {$mealType} WHERE {$idCol} = :item_id";
        return (bool)$this->db->execute($sql, [':item_id' => $itemId]);
    }

    /**
     * Helper — retrieve the meal_plan (meal_id) that owns a given item row.
     * Because the FK col is the same value as meal_plans.meal_id, this is
     * just a direct lookup.
     */
    private function getMealPlanIdByItemId(string $mealType, int $itemId): ?int {
        $idCol = $mealType . '_id';
        $sql   = "SELECT {$idCol} AS plan_id FROM {$mealType} WHERE {$idCol} = :item_id LIMIT 1";
        $row   = $this->db->single($sql, [':item_id' => $itemId]);
        return $row ? (int)$row->plan_id : null;
    }
}