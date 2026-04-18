<?php

class TeamModel
{
    use Model;
    
    protected $table = 'teams';
    
    /** Create a new team*/
    public function create($data)
    {
        // Don't include created_by if it's null or empty
        if (empty($data['created_by'])) {
            unset($data['created_by']);
        }
        
        $query = "INSERT INTO {$this->table} (season, status) 
                  VALUES (:season, :status)";
        
        return $this->query($query, $data);
    }
    
    /**
     * Get all teams
     */
    public function getAll()
    {
        return $this->query("SELECT * FROM {$this->table} ORDER BY team_id DESC");
    }
    
    /**
     * Get team by ID
     */
    public function getById($id)
    {
        $result = $this->query("SELECT * FROM {$this->table} WHERE team_id = :team_id", ['team_id' => $id]);
        return !empty($result) ? $result[0] : null;
    }
    
    /**
     * Get team with players count
     */
    public function getWithPlayersCount($id)
    {
        $query = "SELECT t.*, COUNT(DISTINCT tp.player_id) as players_count 
                  FROM {$this->table} t 
                  LEFT JOIN team_players tp ON t.team_id = tp.team_id 
                  WHERE t.team_id = :team_id 
                  GROUP BY t.team_id";
        
        $result = $this->query($query, ['team_id' => $id]);
        return !empty($result) ? $result[0] : null;
    }
    
    /**
     * Get all teams with player counts, coaches, and tournaments
     */
    public function getAllWithPlayersCounts()
    {
        $query = "SELECT 
                    t.team_id,
                    t.season,
                    t.status
                FROM {$this->table} t
                ORDER BY t.team_id DESC";
        
        return $this->query($query);
    }
    
    /**
     * Update team
     */
    public function update($id, $data)
    {
        $fields = [];
        $params = ['team_id' => $id];
        
        foreach ($data as $key => $value) {
            if ($key !== 'team_id') {
                $fields[] = "{$key} = :{$key}";
                $params[$key] = $value;
            }
        }
        
        $fieldsString = implode(', ', $fields);
        $query = "UPDATE {$this->table} SET {$fieldsString} WHERE team_id = :team_id";
        
        return $this->query($query, $params);
    }
    
    /**
     * Delete team
     */
    public function delete($id)
    {
        return $this->query("DELETE FROM {$this->table} WHERE team_id = :team_id", ['team_id' => $id]);
    }
    
    /**
     * Get teams by status
     */
    public function getByStatus($status)
    {
        return $this->query("SELECT * FROM {$this->table} WHERE status = :status ORDER BY team_id DESC", [
            'status' => $status
        ]);
    }
    
    /**
     * Get teams by season
     */
    public function getBySeason($season)
    {
        return $this->query("SELECT * FROM {$this->table} WHERE season = :season ORDER BY team_id DESC", [
            'season' => $season
        ]);
    }
}