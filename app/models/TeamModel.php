<?php

class TeamModel
{
    use Model;
    
    protected $table = 'teams';
    
    /**
     * Create a new team
     */
    public function create($data)
    {
        error_log("=== TeamModel::create() DEBUG ===");
        error_log("Data received: " . json_encode($data));
        error_log("created_by isset: " . (isset($data['created_by']) ? 'YES' : 'NO'));
        error_log("created_by !empty: " . (!empty($data['created_by']) ? 'YES' : 'NO'));
        
        // Check if created_by is set and valid
        if (isset($data['created_by']) && !empty($data['created_by'])) {
            error_log("Including created_by in INSERT");
            $query = "INSERT INTO {$this->table} (team_name, season, team_status, created_by) 
                      VALUES (:team_name, :season, :team_status, :created_by)";
        } else {
            error_log("Excluding created_by from INSERT");
            // Don't include created_by if it's null or empty
            unset($data['created_by']);
            $query = "INSERT INTO {$this->table} (team_name, season, team_status) 
                      VALUES (:team_name, :season, :team_status)";
        }
        
        error_log("SQL Query: " . $query);
        error_log("Final data: " . json_encode($data));
        
        return $this->query($query, $data);
    }
    
    /**
     * Get all teams
     */
    public function getAll()
    {
        return $this->query("SELECT * FROM {$this->table} ORDER BY created_at DESC");
    }
    
    /**
     * Get team by ID
     */
    public function getById($id)
    {
        $result = $this->query("SELECT * FROM {$this->table} WHERE id = :id", ['id' => $id]);
        return !empty($result) ? $result[0] : null;
    }
    
    /**
     * Get team with players count
     */
    public function getWithPlayersCount($id)
    {
        $query = "SELECT t.*, COUNT(p.id) as players_count 
                  FROM {$this->table} t 
                  LEFT JOIN players p ON t.id = p.team_id 
                  WHERE t.id = :id 
                  GROUP BY t.id";
        
        $result = $this->query($query, ['id' => $id]);
        return !empty($result) ? $result[0] : null;
    }
    
    /**
     * Get all teams with player counts
     */
    public function getAllWithPlayersCounts()
    {
        $query = "SELECT t.*, COUNT(p.id) as players_count 
                  FROM {$this->table} t 
                  LEFT JOIN players p ON t.id = p.team_id 
                  GROUP BY t.id 
                  ORDER BY t.created_at DESC";
        
        return $this->query($query);
    }
    
    /**
     * Update team
     */
    public function update($id, $data)
    {
        $fields = [];
        $params = ['id' => $id];
        
        foreach ($data as $key => $value) {
            if ($key !== 'id') {
                $fields[] = "{$key} = :{$key}";
                $params[$key] = $value;
            }
        }
        
        $fieldsString = implode(', ', $fields);
        $query = "UPDATE {$this->table} SET {$fieldsString}, updated_at = CURRENT_TIMESTAMP WHERE id = :id";
        
        return $this->query($query, $params);
    }
    
    /**
     * Delete team
     */
    public function delete($id)
    {
        return $this->query("DELETE FROM {$this->table} WHERE id = :id", ['id' => $id]);
    }
    
    /**
     * Get teams by status
     */
    public function getByStatus($status)
    {
        return $this->query("SELECT * FROM {$this->table} WHERE team_status = :status ORDER BY created_at DESC", [
            'status' => $status
        ]);
    }
    
    /**
     * Get teams by season
     */
    public function getBySeason($season)
    {
        return $this->query("SELECT * FROM {$this->table} WHERE season = :season ORDER BY created_at DESC", [
            'season' => $season
        ]);
    }
}
