<?php

class TestResultModel
{
    use Model;

    protected $table = 'test_results';


    public function create($data)
    {
        $query = "INSERT INTO {$this->table} (
            test_type, date, score, notes, player_id, team_id
        ) VALUES (
            :test_type, :date, :score, :notes, :player_id, :team_id
        )";

        return $this->query($query, $data);
    }


    public function getAll()
    {
        $query = "SELECT tr.*, u.first_name, u.last_name 
                  FROM {$this->table} tr
                  LEFT JOIN players p ON tr.player_id = p.player_id
                  LEFT JOIN users u ON p.nic = u.nic
                  ORDER BY tr.date DESC";
        
        return $this->query($query);
    }


    public function getByTeamId($team_id)
    {
        $query = "SELECT tr.*, p.player_id,
                  CONCAT(COALESCE(u.first_name, 'Unknown'), ' ', COALESCE(u.last_name, '')) as player_name,
                  u.first_name, u.last_name
                  FROM {$this->table} tr
                  LEFT JOIN players p ON tr.player_id = p.player_id
                  LEFT JOIN users u ON p.nic = u.nic
                  WHERE tr.team_id = :team_id
                  ORDER BY tr.date DESC";
        
        return $this->query($query, ['team_id' => $team_id]);
    }

    /**
     * Get test result by ID
     */
    public function getById($id)
    {
        $query = "SELECT tr.*, 
                  CONCAT(COALESCE(u.first_name, 'Unknown'), ' ', COALESCE(u.last_name, '')) as player_name,
                  u.first_name, u.last_name
                  FROM {$this->table} tr
                  LEFT JOIN players p ON tr.player_id = p.player_id
                  LEFT JOIN users u ON p.nic = u.nic
                  WHERE tr.result_id = :result_id";
        
        $result = $this->query($query, ['result_id' => $id]);
        return !empty($result) ? $result[0] : null;
    }

    /**
     * Update test result
     */
    public function update($id, $data)
    {
        $fields = [];
        $params = ['result_id' => $id];

        foreach ($data as $key => $value) {
            if ($key !== 'result_id') {
                $fields[] = "{$key} = :{$key}";
                $params[$key] = $value;
            }
        }

        if (empty($fields)) {
            return true;
        }

        $query = "UPDATE {$this->table} SET " . implode(', ', $fields) . " WHERE result_id = :result_id";
        return $this->query($query, $params);
    }

    /**
     * Delete test result
     */
    public function delete($id)
    {
        $query = "DELETE FROM {$this->table} WHERE result_id = :result_id";
        return $this->query($query, ['result_id' => $id]);
    }

    /**
     * Get test results by player ID
     */
    public function getByPlayerId($player_id)
    {
        $query = "SELECT * FROM {$this->table} WHERE player_id = :player_id ORDER BY date DESC";
        return $this->query($query, ['player_id' => $player_id]);
    }

    /**
     * Search test results by player name
     */
    public function searchByPlayerName($team_id, $search_term)
    {
        $query = "SELECT tr.*, u.first_name, u.last_name
                  FROM {$this->table} tr
                  LEFT JOIN players p ON tr.player_id = p.player_id
                  LEFT JOIN users u ON p.nic = u.nic
                  WHERE tr.team_id = :team_id 
                  AND (u.first_name LIKE :search OR u.last_name LIKE :search)
                  ORDER BY tr.date DESC";
        
        $search = "%{$search_term}%";
        return $this->query($query, ['team_id' => $team_id, 'search' => $search]);
    }

    /**
     * Filter test results by date range and test type
     */
    public function filterByDateAndType($team_id, $test_type = null, $date_from = null, $date_to = null)
    {
        $query = "SELECT tr.*, u.first_name, u.last_name
                  FROM {$this->table} tr
                  LEFT JOIN players p ON tr.player_id = p.player_id
                  LEFT JOIN users u ON p.nic = u.nic
                  WHERE tr.team_id = :team_id";
        
        $params = ['team_id' => $team_id];

        if (!empty($test_type)) {
            $query .= " AND tr.test_type = :test_type";
            $params['test_type'] = $test_type;
        }

        if (!empty($date_from)) {
            $query .= " AND tr.date >= :date_from";
            $params['date_from'] = $date_from;
        }

        if (!empty($date_to)) {
            $query .= " AND tr.date <= :date_to";
            $params['date_to'] = $date_to;
        }

        $query .= " ORDER BY tr.date DESC";
        return $this->query($query, $params);
    }
}
