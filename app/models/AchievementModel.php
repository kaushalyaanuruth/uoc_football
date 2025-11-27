<?php

class AchievementModel
{
    use Model;
    
    protected $table = 'achievements';
    
    /**
     * Create a new achievement
     */
    public function create($data)
    {
        // Set defaults for optional fields
        $defaults = [
            'achievement_date' => null,
            'description' => null
        ];
        
        $data = array_merge($defaults, $data);
        
        $query = "INSERT INTO {$this->table} (team_id, achievement_title, achievement_date, description) 
                  VALUES (:team_id, :achievement_title, :achievement_date, :description)";
        
        return $this->query($query, $data);
    }
    
    /**
     * Get achievements by team
     */
    public function getByTeamId($team_id)
    {
        return $this->query("SELECT * FROM {$this->table} WHERE team_id = :team_id ORDER BY achievement_date DESC", [
            'team_id' => $team_id
        ]);
    }
    
    /**
     * Delete achievement
     */
    public function delete($id)
    {
        return $this->query("DELETE FROM {$this->table} WHERE id = :id", ['id' => $id]);
    }
    
    /**
     * Delete all achievements by team ID
     */
    public function deleteByTeamId($team_id)
    {
        return $this->query("DELETE FROM {$this->table} WHERE team_id = :team_id", ['team_id' => $team_id]);
    }
}
