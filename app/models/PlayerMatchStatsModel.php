<?php

class PlayerMatchStatsModel
{
    use Model;

    protected $table = 'player_match_stats';

    /**
     * Create player match statistics
     */
    public function create($data)
    {
        $query = "INSERT INTO {$this->table} (
            match_id, player_id, position_played, minutes_played, substitution_status,
            goals_scored, assists, shots_on_target, shots_off_target, key_passes, successful_dribbles,
            completed_passes, line_breaking_passes,
            tackles_won, interceptions, defensive_duels_won, aerial_duels_won,
            yellow_cards, red_cards, fouls_committed, fouls_won,
            notes
        ) VALUES (
            :match_id, :player_id, :position_played, :minutes_played, :substitution_status,
            :goals_scored, :assists, :shots_on_target, :shots_off_target, :key_passes, :successful_dribbles,
            :completed_passes, :line_breaking_passes,
            :tackles_won, :interceptions, :defensive_duels_won, :aerial_duels_won,
            :yellow_cards, :red_cards, :fouls_committed, :fouls_won,
            :notes
        )";

        return $this->query($query, $data);
    }

    /**
     * Get all match statistics for a specific match
     */
    public function getByMatchId($match_id)
    {
        $query = "SELECT pms.*, p.player_id, CONCAT(u.first_name, ' ', u.last_name) as player_name, u.image as player_image
                  FROM {$this->table} pms
                  JOIN players p ON pms.player_id = p.player_id
                  JOIN users u ON p.nic = u.nic
                  WHERE pms.match_id = :match_id
                  ORDER BY pms.minutes_played DESC";
        
        return $this->query($query, ['match_id' => $match_id]);
    }

    /**
     * Get player match statistics by ID
     */
    public function getById($id)
    {
        $query = "SELECT pms.*, p.player_id, CONCAT(u.first_name, ' ', u.last_name) as player_name
                  FROM {$this->table} pms
                  JOIN players p ON pms.player_id = p.player_id
                  JOIN users u ON p.nic = u.nic
                  WHERE pms.stat_id = :stat_id";
        
        $result = $this->query($query, ['stat_id' => $id]);
        return !empty($result) ? $result[0] : null;
    }

    /**
     * Update player match statistics
     */
    public function update($id, $data)
    {
        $fields = [];
        $params = ['stat_id' => $id];

        foreach ($data as $key => $value) {
            if ($key !== 'stat_id' && $key !== 'match_id' && $key !== 'player_id') {
                $fields[] = "{$key} = :{$key}";
                $params[$key] = $value;
            }
        }

        if (empty($fields)) {
            return true;
        }

        $query = "UPDATE {$this->table} SET " . implode(', ', $fields) . " WHERE stat_id = :stat_id";
        return $this->query($query, $params);
    }

    /**
     * Delete player match statistics
     */
    public function delete($id)
    {
        $query = "DELETE FROM {$this->table} WHERE stat_id = :stat_id";
        return $this->query($query, ['stat_id' => $id]);
    }

    /**
     * Delete all stats for a match
     */
    public function deleteByMatchId($match_id)
    {
        $query = "DELETE FROM {$this->table} WHERE match_id = :match_id";
        return $this->query($query, ['match_id' => $match_id]);
    }

    /**
     * Check if player stats already exist for a match
     */
    public function exists($match_id, $player_id)
    {
        $query = "SELECT stat_id FROM {$this->table} 
                  WHERE match_id = :match_id AND player_id = :player_id";
        
        $result = $this->query($query, ['match_id' => $match_id, 'player_id' => $player_id]);
        return !empty($result);
    }
}
?>
