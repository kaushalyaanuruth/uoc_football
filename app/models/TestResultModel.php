<?php

class TestResultModel
{
    use Model;

    protected $table = 'test_results';
    private $schema = null;

    private function safeQuery($query, $params = [])
    {
        try {
            return $this->query($query, $params);
        } catch (Throwable $e) {
            return [];
        }
    }

    private function loadSchema()
    {
        if (is_array($this->schema)) {
            return $this->schema;
        }

        $cols = $this->safeQuery("SHOW COLUMNS FROM {$this->table}");
        $map = [];
        foreach ($cols as $col) {
            $field = strtolower((string) ($col->Field ?? ''));
            if ($field !== '') {
                $map[$field] = $col->Field;
            }
        }

        $pick = function (array $candidates) use ($map) {
            foreach ($candidates as $candidate) {
                $key = strtolower($candidate);
                if (isset($map[$key])) {
                    return $map[$key];
                }
            }
            return null;
        };

        $this->schema = [
            'id_col' => $pick(['result_id', 'test_id', 'id']),
            'type_col' => $pick(['test_type', 'type', 'test_name']),
            'date_col' => $pick(['date', 'test_date']),
            'score_col' => $pick(['score', 'test_score', 'result', 'result_value', 'value']),
            'notes_col' => $pick(['notes', 'remarks', 'comment', 'description']),
            'player_col' => $pick(['player_id']),
            'team_col' => $pick(['team_id']),
        ];

        return $this->schema;
    }

    private function scoreExpr($alias = 'tr')
    {
        $schema = $this->loadSchema();
        $scoreCol = $schema['score_col'];
        $notesCol = $schema['notes_col'];

        if ($scoreCol && $notesCol && strtolower($scoreCol) !== strtolower($notesCol)) {
            return "COALESCE({$alias}.{$scoreCol}, {$alias}.{$notesCol}, '')";
        }
        if ($scoreCol) {
            return "COALESCE({$alias}.{$scoreCol}, '')";
        }
        if ($notesCol) {
            return "COALESCE({$alias}.{$notesCol}, '')";
        }

        return "''";
    }

    private function notesExpr($alias = 'tr')
    {
        $schema = $this->loadSchema();
        $notesCol = $schema['notes_col'];
        $scoreCol = $schema['score_col'];

        if ($notesCol && $scoreCol && strtolower($notesCol) !== strtolower($scoreCol)) {
            return "COALESCE({$alias}.{$notesCol}, {$alias}.{$scoreCol}, '')";
        }
        if ($notesCol) {
            return "COALESCE({$alias}.{$notesCol}, '')";
        }
        if ($scoreCol) {
            return "COALESCE({$alias}.{$scoreCol}, '')";
        }

        return "''";
    }


    public function create($data)
    {
        $schema = $this->loadSchema();

        $columns = [];
        $params = [];

        if ($schema['type_col']) {
            $columns[] = $schema['type_col'];
            $params['type_value'] = $data['test_type'] ?? '';
        }
        if ($schema['date_col']) {
            $columns[] = $schema['date_col'];
            $params['date_value'] = $data['date'] ?? date('Y-m-d');
        }
        if ($schema['score_col']) {
            $columns[] = $schema['score_col'];
            $params['score_value'] = $data['score'] ?? ($data['notes'] ?? '');
        }
        if ($schema['notes_col'] && strtolower((string) $schema['notes_col']) !== strtolower((string) $schema['score_col'])) {
            $columns[] = $schema['notes_col'];
            $params['notes_value'] = $data['notes'] ?? ($data['score'] ?? '');
        }
        if ($schema['player_col']) {
            $columns[] = $schema['player_col'];
            $params['player_value'] = (int) ($data['player_id'] ?? 0);
        }
        if ($schema['team_col']) {
            $columns[] = $schema['team_col'];
            $params['team_value'] = (int) ($data['team_id'] ?? 0);
        }

        $placeholders = [];
        foreach ($columns as $col) {
            switch (strtolower($col)) {
                case strtolower((string) $schema['type_col']):
                    $placeholders[] = ':type_value';
                    break;
                case strtolower((string) $schema['date_col']):
                    $placeholders[] = ':date_value';
                    break;
                case strtolower((string) $schema['score_col']):
                    $placeholders[] = ':score_value';
                    break;
                case strtolower((string) $schema['notes_col']):
                    $placeholders[] = ':notes_value';
                    break;
                case strtolower((string) $schema['player_col']):
                    $placeholders[] = ':player_value';
                    break;
                case strtolower((string) $schema['team_col']):
                    $placeholders[] = ':team_value';
                    break;
            }
        }

        if (empty($columns) || empty($placeholders)) {
            return [];
        }

        $query = "INSERT INTO {$this->table} (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $placeholders) . ")";
        return $this->query($query, $params);
    }


    public function getAll()
    {
                $schema = $this->loadSchema();
                $idCol = $schema['id_col'] ?: 'result_id';
                $typeCol = $schema['type_col'] ?: 'test_type';
                $dateCol = $schema['date_col'] ?: 'date';
                $playerCol = $schema['player_col'] ?: 'player_id';

                $query = "SELECT
                                        tr.{$idCol} AS result_id,
                                        tr.{$typeCol} AS test_type,
                                        tr.{$dateCol} AS date,
                                        " . $this->scoreExpr('tr') . " AS score,
                                        " . $this->notesExpr('tr') . " AS notes,
                                        tr.{$playerCol} AS player_id,
                                        u.first_name, u.last_name
                                    FROM {$this->table} tr
                                    LEFT JOIN players p ON tr.{$playerCol} = p.player_id
                  LEFT JOIN users u ON p.nic = u.nic
                                    ORDER BY tr.{$dateCol} DESC";
        
        return $this->query($query);
    }


    public function getByTeamId($team_id)
    {
        $schema = $this->loadSchema();
        $idCol = $schema['id_col'] ?: 'result_id';
        $typeCol = $schema['type_col'] ?: 'test_type';
        $dateCol = $schema['date_col'] ?: 'date';
        $playerCol = $schema['player_col'] ?: 'player_id';
        $teamCol = $schema['team_col'];

        $query = "SELECT
                  tr.{$idCol} AS result_id,
                  tr.{$typeCol} AS test_type,
                  tr.{$dateCol} AS date,
                  " . $this->scoreExpr('tr') . " AS score,
                  " . $this->notesExpr('tr') . " AS notes,
                  p.player_id,
                  CONCAT(COALESCE(u.first_name, 'Unknown'), ' ', COALESCE(u.last_name, '')) as player_name,
                  u.first_name, u.last_name";

        if ($teamCol) {
            $query .= ", tr.{$teamCol} AS team_id";
        } else {
            $query .= ", tp.team_id AS team_id";
        }

        $query .= "
                  FROM {$this->table} tr
                  LEFT JOIN players p ON tr.{$playerCol} = p.player_id
                  LEFT JOIN team_players tp ON tp.player_id = p.player_id
                  LEFT JOIN users u ON p.nic = u.nic";

        if ($teamCol) {
            $query .= " WHERE tr.{$teamCol} = :team_id";
        } else {
            $query .= " WHERE tp.team_id = :team_id";
        }

        $query .= " ORDER BY tr.{$dateCol} DESC";
        
        return $this->query($query, ['team_id' => $team_id]);
    }

    /**
     * Get test result by ID
     */
    public function getById($id)
    {
        $schema = $this->loadSchema();
        $idCol = $schema['id_col'] ?: 'result_id';
        $typeCol = $schema['type_col'] ?: 'test_type';
        $dateCol = $schema['date_col'] ?: 'date';
        $playerCol = $schema['player_col'] ?: 'player_id';

        $query = "SELECT
                  tr.{$idCol} AS result_id,
                  tr.{$typeCol} AS test_type,
                  tr.{$dateCol} AS date,
                  " . $this->scoreExpr('tr') . " AS score,
                  " . $this->notesExpr('tr') . " AS notes,
                  tr.{$playerCol} AS player_id,
                  CONCAT(COALESCE(u.first_name, 'Unknown'), ' ', COALESCE(u.last_name, '')) as player_name,
                  u.first_name, u.last_name
                  FROM {$this->table} tr
                  LEFT JOIN players p ON tr.{$playerCol} = p.player_id
                  LEFT JOIN users u ON p.nic = u.nic
                  WHERE tr.{$idCol} = :result_id";
        
        $result = $this->query($query, ['result_id' => $id]);
        return !empty($result) ? $result[0] : null;
    }

    /**
     * Update test result
     */
    public function update($id, $data)
    {
        $schema = $this->loadSchema();
        $idCol = $schema['id_col'] ?: 'result_id';

        $fields = [];
        $params = ['result_id' => $id];

        if (isset($data['test_type']) && $schema['type_col']) {
            $fields[] = "{$schema['type_col']} = :test_type";
            $params['test_type'] = $data['test_type'];
        }
        if (isset($data['date']) && $schema['date_col']) {
            $fields[] = "{$schema['date_col']} = :date_value";
            $params['date_value'] = $data['date'];
        }
        if (isset($data['score'])) {
            if ($schema['score_col']) {
                $fields[] = "{$schema['score_col']} = :score_value";
                $params['score_value'] = $data['score'];
            }
            if ($schema['notes_col'] && strtolower((string) $schema['notes_col']) === strtolower((string) $schema['score_col'])) {
                $fields[] = "{$schema['notes_col']} = :score_value";
            }
        }
        if (isset($data['notes']) && $schema['notes_col'] && strtolower((string) $schema['notes_col']) !== strtolower((string) $schema['score_col'])) {
            $fields[] = "{$schema['notes_col']} = :notes_value";
            $params['notes_value'] = $data['notes'];
        }

        if (empty($fields)) {
            return true;
        }

        $query = "UPDATE {$this->table} SET " . implode(', ', $fields) . " WHERE {$idCol} = :result_id";
        return $this->query($query, $params);
    }

    /**
     * Delete test result
     */
    public function delete($id)
    {
        $schema = $this->loadSchema();
        $idCol = $schema['id_col'] ?: 'result_id';
        $query = "DELETE FROM {$this->table} WHERE {$idCol} = :result_id";
        return $this->query($query, ['result_id' => $id]);
    }

    /**
     * Get test results by player ID
     */
    public function getByPlayerId($player_id)
    {
                $schema = $this->loadSchema();
                $idCol = $schema['id_col'] ?: 'result_id';
                $typeCol = $schema['type_col'] ?: 'test_type';
                $dateCol = $schema['date_col'] ?: 'date';
                $playerCol = $schema['player_col'] ?: 'player_id';

                $query = "SELECT
                                        {$idCol} AS result_id,
                                        {$typeCol} AS test_type,
                                        {$dateCol} AS date,
                                        " . $this->scoreExpr() . " AS score,
                                        " . $this->notesExpr() . " AS notes,
                                        {$playerCol} AS player_id
                                    FROM {$this->table}
                                    WHERE {$playerCol} = :player_id
                                    ORDER BY {$dateCol} DESC";
        return $this->query($query, ['player_id' => $player_id]);
    }

    /**
     * Search test results by player name
     */
    public function searchByPlayerName($team_id, $search_term)
    {
        $schema = $this->loadSchema();
        $idCol = $schema['id_col'] ?: 'result_id';
        $typeCol = $schema['type_col'] ?: 'test_type';
        $dateCol = $schema['date_col'] ?: 'date';
        $playerCol = $schema['player_col'] ?: 'player_id';
        $teamCol = $schema['team_col'];

        $query = "SELECT
                  tr.{$idCol} AS result_id,
                  tr.{$typeCol} AS test_type,
                  tr.{$dateCol} AS date,
                  " . $this->scoreExpr('tr') . " AS score,
                  " . $this->notesExpr('tr') . " AS notes,
                  u.first_name, u.last_name
                  FROM {$this->table} tr
                  LEFT JOIN players p ON tr.{$playerCol} = p.player_id
                  LEFT JOIN team_players tp ON tp.player_id = p.player_id
                  LEFT JOIN users u ON p.nic = u.nic
                  WHERE " . ($teamCol ? "tr.{$teamCol}" : "tp.team_id") . " = :team_id 
                  AND (u.first_name LIKE :search OR u.last_name LIKE :search)
                  ORDER BY tr.{$dateCol} DESC";
        
        $search = "%{$search_term}%";
        return $this->query($query, ['team_id' => $team_id, 'search' => $search]);
    }

    /**
     * Filter test results by date range and test type
     */
    public function filterByDateAndType($team_id, $test_type = null, $date_from = null, $date_to = null)
    {
        $schema = $this->loadSchema();
        $idCol = $schema['id_col'] ?: 'result_id';
        $typeCol = $schema['type_col'] ?: 'test_type';
        $dateCol = $schema['date_col'] ?: 'date';
        $playerCol = $schema['player_col'] ?: 'player_id';
        $teamCol = $schema['team_col'];

        $query = "SELECT
                  tr.{$idCol} AS result_id,
                  tr.{$typeCol} AS test_type,
                  tr.{$dateCol} AS date,
                  " . $this->scoreExpr('tr') . " AS score,
                  " . $this->notesExpr('tr') . " AS notes,
                  u.first_name, u.last_name
                  FROM {$this->table} tr
                  LEFT JOIN players p ON tr.{$playerCol} = p.player_id
                  LEFT JOIN team_players tp ON tp.player_id = p.player_id
                  LEFT JOIN users u ON p.nic = u.nic
                  WHERE " . ($teamCol ? "tr.{$teamCol}" : "tp.team_id") . " = :team_id";
        
        $params = ['team_id' => $team_id];

        if (!empty($test_type)) {
            $query .= " AND tr.{$typeCol} = :test_type";
            $params['test_type'] = $test_type;
        }

        if (!empty($date_from)) {
            $query .= " AND tr.{$dateCol} >= :date_from";
            $params['date_from'] = $date_from;
        }

        if (!empty($date_to)) {
            $query .= " AND tr.{$dateCol} <= :date_to";
            $params['date_to'] = $date_to;
        }

        $query .= " ORDER BY tr.{$dateCol} DESC";
        return $this->query($query, $params);
    }
}
