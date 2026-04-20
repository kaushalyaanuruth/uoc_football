<?php

class CoachMealplanModel
{
    use Model;

    protected $table = 'meal_plans';
    private $mealDayColumnSupport = [];

    private function weekDays()
    {
        return ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
    }

    private function normalizeDayName($dayName)
    {
        $raw = strtolower(trim((string) $dayName));
        $allowed = [
            'monday' => 'Monday',
            'tuesday' => 'Tuesday',
            'wednesday' => 'Wednesday',
            'thursday' => 'Thursday',
            'friday' => 'Friday',
            'saturday' => 'Saturday',
            'sunday' => 'Sunday',
        ];

        return $allowed[$raw] ?? 'Monday';
    }

    private function emptyDayPlan()
    {
        return [
            'breakfast' => [],
            'lunch' => [],
            'dinner' => [],
        ];
    }

    private function emptyWeeklyPlan()
    {
        $plan = [];
        foreach ($this->weekDays() as $dayName) {
            $plan[strtolower($dayName)] = $this->emptyDayPlan();
        }

        return $plan;
    }

    private function tableExists($tableName)
    {
        $safeTableName = preg_replace('/[^a-zA-Z0-9_]/', '', (string) $tableName);
        if ($safeTableName === '') {
            return false;
        }

        $rows = $this->query("SHOW TABLES LIKE '{$safeTableName}'");
        return !empty($rows);
    }

    private function hasTeamsStatusColumn()
    {
        if (!$this->tableExists('teams')) {
            return false;
        }

        $rows = $this->query("SHOW COLUMNS FROM teams LIKE 'status'");
        return !empty($rows);
    }

    private function hasMealDayColumn($tableName)
    {
        $safeTableName = preg_replace('/[^a-zA-Z0-9_]/', '', (string) $tableName);
        if ($safeTableName === '') {
            return false;
        }

        if (array_key_exists($safeTableName, $this->mealDayColumnSupport)) {
            return $this->mealDayColumnSupport[$safeTableName];
        }

        if (!$this->tableExists($safeTableName)) {
            $this->mealDayColumnSupport[$safeTableName] = false;
            return false;
        }

        $rows = $this->query("SHOW COLUMNS FROM {$safeTableName} LIKE 'meal_day'");
        $hasColumn = !empty($rows);
        $this->mealDayColumnSupport[$safeTableName] = $hasColumn;

        return $hasColumn;
    }

    private function ensureMealDayColumn($tableName)
    {
        $safeTableName = preg_replace('/[^a-zA-Z0-9_]/', '', (string) $tableName);
        if ($safeTableName === '' || !$this->tableExists($safeTableName)) {
            return false;
        }

        if ($this->hasMealDayColumn($safeTableName)) {
            return true;
        }

        try {
            $this->query(
                "ALTER TABLE {$safeTableName} ADD COLUMN meal_day VARCHAR(12) NOT NULL DEFAULT 'Monday' AFTER meal"
            );
        } catch (Throwable $e) {
            // Keep backward compatibility if schema migration cannot run here.
        }

        unset($this->mealDayColumnSupport[$safeTableName]);
        return $this->hasMealDayColumn($safeTableName);
    }

    public function getTeamIdByCoachNic($nic)
    {
        if (!$nic || !$this->tableExists('team_coaches') || !$this->tableExists('coaches')) {
            return null;
        }

        $hasStatus = $this->hasTeamsStatusColumn();
        $statusJoin = $hasStatus ? " JOIN teams t ON t.team_id = tc.team_id " : "";
        $statusWhere = $hasStatus ? " AND t.status = 'present'" : "";

        $rows = $this->query(
            "SELECT tc.team_id
             FROM coaches c
             JOIN team_coaches tc ON tc.coach_id = c.coach_id
             {$statusJoin}
             WHERE c.nic = :nic
             {$statusWhere}
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

        if ($this->hasTeamsStatusColumn()) {
            $presentRows = $this->query(
                "SELECT team_id
                 FROM teams
                 WHERE status = 'present'
                 ORDER BY team_id DESC
                 LIMIT 1"
            );

            if (!empty($presentRows)) {
                return (int) ($presentRows[0]->team_id ?? 0) ?: null;
            }
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

        $hasStatus = $this->hasTeamsStatusColumn();
        $statusJoin = $hasStatus ? " JOIN teams t ON t.team_id = tp.team_id " : "";
        $statusWhere = $hasStatus ? " AND t.status = 'present'" : "";

        $rows = $this->query(
            "SELECT tp.team_id
             FROM players p
             JOIN team_players tp ON tp.player_id = p.player_id
             {$statusJoin}
             WHERE p.nic = :nic
             {$statusWhere}
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

    private function getWeeklyItemsFromMealTable($table, $mealId)
    {
        $weeklyItems = [];
        foreach ($this->weekDays() as $dayName) {
            $weeklyItems[strtolower($dayName)] = [];
        }

        if (!$this->tableExists($table)) {
            return $weeklyItems;
        }

        if ($this->hasMealDayColumn($table)) {
            $rows = $this->query(
                "SELECT meal, meal_day
                 FROM {$table}
                 WHERE meal_id = :meal_id",
                ['meal_id' => $mealId]
            );

            foreach ($rows as $row) {
                $value = trim((string) ($row->meal ?? ''));
                if ($value === '') {
                    continue;
                }

                $dayKey = strtolower($this->normalizeDayName($row->meal_day ?? 'Monday'));
                $weeklyItems[$dayKey][] = $value;
            }

            foreach ($weeklyItems as $dayKey => $items) {
                $weeklyItems[$dayKey] = $this->normalizeItems($items);
            }

            return $weeklyItems;
        }

        // Legacy schema: no day column means one shared plan for all days.
        $sharedItems = $this->getItemsFromMealTable($table, $mealId);
        foreach ($weeklyItems as $dayKey => $_items) {
            $weeklyItems[$dayKey] = $sharedItems;
        }

        return $weeklyItems;
    }

    public function getWeeklyTeamMealPlan($teamId)
    {
        $plan = $this->emptyWeeklyPlan();

        if (!$teamId) {
            return $plan;
        }

        $mealId = $this->getLatestMealIdByTeam($teamId);
        if ($mealId === null) {
            return $plan;
        }

        foreach (['breakfast', 'lunch', 'dinner'] as $mealType) {
            $itemsByDay = $this->getWeeklyItemsFromMealTable($mealType, $mealId);

            foreach ($plan as $dayKey => $dayPlan) {
                $plan[$dayKey][$mealType] = $itemsByDay[$dayKey] ?? [];
            }
        }

        return $plan;
    }

    public function getTeamMealPlan($teamId, $dayName = null)
    {
        $weeklyPlan = $this->getWeeklyTeamMealPlan($teamId);
        $resolvedDay = $this->normalizeDayName($dayName ?: date('l'));
        $dayKey = strtolower($resolvedDay);

        return $weeklyPlan[$dayKey] ?? $this->emptyDayPlan();
    }

    public function saveTeamMealType($teamId, $mealType, $items, $dayName = 'Monday')
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

        $resolvedDay = $this->normalizeDayName($dayName);
        $hasMealDayColumn = $this->ensureMealDayColumn($table);

        if ($hasMealDayColumn) {
            $this->query(
                "DELETE FROM {$table} WHERE meal_id = :meal_id AND meal_day = :meal_day",
                [
                    'meal_id' => $mealId,
                    'meal_day' => $resolvedDay,
                ]
            );
        } else {
            $this->query("DELETE FROM {$table} WHERE meal_id = :meal_id", ['meal_id' => $mealId]);
        }

        foreach ($normalizedItems as $item) {
            if ($hasMealDayColumn) {
                $this->query(
                    "INSERT INTO {$table} (meal, meal_day, amount, meal_id)
                     VALUES (:meal, :meal_day, :amount, :meal_id)",
                    [
                        'meal' => $item,
                        'meal_day' => $resolvedDay,
                        'amount' => '1 serving',
                        'meal_id' => $mealId,
                    ]
                );
            } else {
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
