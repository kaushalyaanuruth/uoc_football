<?php

class MatchResultModel
{
    use Model;

    protected $table = 'match_results';

    /**
     * Create a new match result
     */
    public function create($data)
    {
        $query = "INSERT INTO {$this->table} (
            opponent_team, result, goals_scored, goals_conceded, shots, shots_on_target, 
            possession, passes, passes_accuracy, corners, date, notes, team_id
        ) VALUES (
            :opponent_team, :result, :goals_scored, :goals_conceded, :shots, :shots_on_target,
            :possession, :passes, :passes_accuracy, :corners, :date, :notes, :team_id
        )";

        return $this->query($query, $data);
    }

    /**
     * Get all match results
     */
    public function getAll()
    {
        $query = "SELECT * FROM {$this->table} ORDER BY date DESC";
        return $this->query($query);
    }

    /**
     * Get match results by team ID
     */
    public function getByTeamId($team_id)
    {
        $query = "SELECT * FROM {$this->table} 
                  WHERE team_id = :team_id
                  ORDER BY date DESC";
        
        return $this->query($query, ['team_id' => $team_id]);
    }

    /**
     * Get match result by team ID with formatted fields
     */
    public function getByTeamIdFormatted($team_id)
    {
        $query = "SELECT mr.* FROM {$this->table} mr
                  WHERE mr.team_id = :team_id
                  ORDER BY mr.date DESC";
        
        return $this->query($query, ['team_id' => $team_id]);
    }

    /**
     * Get match result by ID
     */
    public function getById($id)
    {
        $query = "SELECT * FROM {$this->table} WHERE result_id = :result_id";
        
        $result = $this->query($query, ['result_id' => $id]);
        return !empty($result) ? $result[0] : null;
    }

    /**
     * Update match result
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
     * Delete match result
     */
    public function delete($id)
    {
        $query = "DELETE FROM {$this->table} WHERE result_id = :result_id";
        return $this->query($query, ['result_id' => $id]);
    }

    /**
     * Search match results by opponent team
     */
    public function searchByOpponent($team_id, $search_term)
    {
        $query = "SELECT * FROM {$this->table}
                  WHERE team_id = :team_id 
                  AND opponent_team LIKE :search
                  ORDER BY date DESC";
        
        $search = "%{$search_term}%";
        return $this->query($query, ['team_id' => $team_id, 'search' => $search]);
    }

    /**
     * Filter match results by date range
     */
    public function filterByDateRange($team_id, $date_from = null, $date_to = null)
    {
        $query = "SELECT * FROM {$this->table}
                  WHERE team_id = :team_id";
        
        $params = ['team_id' => $team_id];

        if (!empty($date_from)) {
            $query .= " AND date >= :date_from";
            $params['date_from'] = $date_from;
        }

        if (!empty($date_to)) {
            $query .= " AND date <= :date_to";
            $params['date_to'] = $date_to;
        }

        $query .= " ORDER BY date DESC";
        return $this->query($query, $params);
    }

    /**
     * Get match statistics for a team
     */
    public function getStats($team_id)
    {
        $query = "SELECT 
                    COUNT(*) as total_matches,
                    SUM(CASE WHEN result = 'Won' THEN 1 ELSE 0 END) as wins,
                    SUM(CASE WHEN result = 'Draw' THEN 1 ELSE 0 END) as draws,
                    SUM(CASE WHEN result = 'Lost' THEN 1 ELSE 0 END) as losses
                  FROM {$this->table}
                  WHERE team_id = :team_id";
        
        $result = $this->query($query, ['team_id' => $team_id]);
        return !empty($result) ? $result[0] : null;
    }
}
