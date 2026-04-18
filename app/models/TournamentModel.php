<?php

class TournamentModel
{
    use Model;
    
    protected $table = 'tournaments';
    
    /**
     * Create a new tournament
     */
    public function create($data)
    {
        $query = "INSERT INTO {$this->table} (team_id, name) 
                  VALUES (:team_id, :name)";
        
        return $this->query($query, $data);
    }
    
    /**
     * Get tournaments by team
     */
    public function getByTeamId($team_id)
    {
        return $this->query("SELECT * FROM {$this->table} WHERE team_id = :team_id", [
            'team_id' => $team_id
        ]);
    }
    
    /**
     * Delete tournament
     */
    public function delete($id)
    {
        return $this->query("DELETE FROM {$this->table} WHERE id = :id", ['id' => $id]);
    }
    
    /**
     * Delete all tournaments by team ID
     */
    public function deleteByTeamId($team_id)
    {
        return $this->query("DELETE FROM {$this->table} WHERE team_id = :team_id", ['team_id' => $team_id]);
    }
}
