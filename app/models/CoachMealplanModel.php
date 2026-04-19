<?php

class CoachMealplanModel
{
    use Model;

    protected $table = 'meal_plans';

    private function tableExists($tableName)
    {
        $safeTableName = preg_replace('/[^a-zA-Z0-9_]/', '', (string) $tableName);
        if ($safeTableName === '') {
            return false;
        }

        $rows = $this->query("SHOW TABLES LIKE '{$safeTableName}'");
        return !empty($rows);
    }

    public function getTeamIdByCoachNic($nic)
    {
        if (!$nic || !$this->tableExists('team_coaches') || !$this->tableExists('coaches')) {
            return null;
        }

        $rows = $this->query(
            "SELECT tc.team_id
             FROM coaches c
             JOIN team_coaches tc ON tc.coach_id = c.coach_id
             WHERE c.nic = :nic
             ORDER BY tc.team_id DESC
             LIMIT 1",
            ['nic' => $nic]
        );

        if (empty($rows)) {
            return null;
        }

        return (int) ($rows[0]->team_id ?? 0) ?: null;
    }

    public function getAnyTeamId()
    {
        if (!$this->tableExists('teams')) {
            return null;
        }

        $rows = $this->query(
            "SELECT team_id
             FROM teams
             ORDER BY team_id DESC
             LIMIT 1"
        );

        if (empty($rows)) {
            return null;
        }

        return (int) ($rows[0]->team_id ?? 0) ?: null;
    }

    public function getTeamIdByPlayerNic($nic)
    {
        if (!$nic || !$this->tableExists('team_players') || !$this->tableExists('players')) {
            return null;
        }

        $rows = $this->query(
            "SELECT tp.team_id
             FROM players p
             JOIN team_players tp ON tp.player_id = p.player_id
             WHERE p.nic = :nic
             ORDER BY tp.team_id DESC
             LIMIT 1",
            ['nic' => $nic]
        );

        if (empty($rows)) {
            return null;
        }

        return (int) ($rows[0]->team_id ?? 0) ?: null;
    }

    private function getLatestMealIdByTeam($teamId)
    {
        if (!$this->tableExists('meal_plans')) {
            return null;
        }

        $rows = $this->query(
            "SELECT meal_id
             FROM meal_plans
             WHERE team_id = :team_id
             ORDER BY updated_date DESC, meal_id DESC
             LIMIT 1",
            ['team_id' => $teamId]
        );

        if (empty($rows)) {
            return null;
        }

        return (int) ($rows[0]->meal_id ?? 0) ?: null;
    }

    private function ensureLatestMealIdByTeam($teamId)
    {
        $mealId = $this->getLatestMealIdByTeam($teamId);
        if ($mealId !== null) {
            return $mealId;
        }

        if (!$this->tableExists('meal_plans')) {
            return null;
        }

        $this->query(
            "INSERT INTO meal_plans (team_id) VALUES (:team_id)",
            ['team_id' => $teamId]
        );

        $inserted = $this->lastInsertId();
        return $inserted ? (int) $inserted : $this->getLatestMealIdByTeam($teamId);
    }

    private function tableNameForMealType($mealType)
    {
        $type = strtolower((string) $mealType);
        if (in_array($type, ['breakfast', 'lunch', 'dinner'], true)) {
            return $type;
        }

        return null;
    }

    private function normalizeItems($items)
    {
        if (!is_array($items)) {
            return [];
        }

        $normalized = [];
        $seen = [];

        foreach ($items as $item) {
            $value = trim((string) $item);
            if ($value === '') {
                continue;
            }

            $key = strtolower($value);
            if (isset($seen[$key])) {
                continue;
            }

            $seen[$key] = true;
            $normalized[] = function_exists('mb_substr')
                ? mb_substr($value, 0, 255)
                : substr($value, 0, 255);
        }

        return $normalized;
    }

    private function getItemsFromMealTable($table, $mealId)
    {
        if (!$this->tableExists($table)) {
            return [];
        }

        $rows = $this->query(
            "SELECT meal
             FROM {$table}
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

        return $this->normalizeItems($items);
    }

    public function getTeamMealPlan($teamId)
    {
        $plan = [
            'breakfast' => [],
            'lunch' => [],
            'dinner' => [],
        ];

        if (!$teamId) {
            return $plan;
        }

        $mealId = $this->getLatestMealIdByTeam($teamId);
        if ($mealId === null) {
            return $plan;
        }

        foreach (['breakfast', 'lunch', 'dinner'] as $mealType) {
            $plan[$mealType] = $this->getItemsFromMealTable($mealType, $mealId);
        }

        return $plan;
    }

    public function saveTeamMealType($teamId, $mealType, $items)
    {
        $table = $this->tableNameForMealType($mealType);
        if (!$teamId || $table === null || !$this->tableExists($table)) {
            return false;
        }

        $mealId = $this->ensureLatestMealIdByTeam($teamId);
        if ($mealId === null) {
            return false;
        }

        $normalizedItems = $this->normalizeItems($items);

        $this->query("DELETE FROM {$table} WHERE meal_id = :meal_id", ['meal_id' => $mealId]);

        foreach ($normalizedItems as $item) {
            $this->query(
                "INSERT INTO {$table} (meal, amount, meal_id)
                 VALUES (:meal, :amount, :meal_id)",
                [
                    'meal' => $item,
                    'amount' => '1 serving',
                    'meal_id' => $mealId,
                ]
            );
        }

        $this->query(
            "UPDATE meal_plans
             SET updated_date = CURRENT_TIMESTAMP
             WHERE meal_id = :meal_id",
            ['meal_id' => $mealId]
        );

        return true;
    }
}
