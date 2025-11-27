<?php

class CoachModel
{
    use Model;
    
    protected $table = 'coaches';
    
    /**
     * Create a new coach
     */
    public function create($data)
    {
        $query = "INSERT INTO {$this->table} (
            full_name, name_with_initials, age, nic, phone_number, address, licence, image
        ) VALUES (
            :full_name, :name_with_initials, :age, :nic, :phone_number, :address, :licence, :image
        )";
        
        return $this->query($query, $data);
    }
    
    /**
     * Get all coaches
     */
    public function getAll()
    {
        return $this->query("SELECT * FROM {$this->table} ORDER BY created_at DESC");
    }
    
    /**
     * Get coach by ID
     */
    public function getById($id)
    {
        $result = $this->query("SELECT * FROM {$this->table} WHERE id = :id", ['id' => $id]);
        return !empty($result) ? $result[0] : null;
    }
    
    /**
     * Update coach
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
     * Delete coach
     */
    public function delete($id)
    {
        // Get coach data first to delete image and user account
        $coach = $this->getById($id);
        
        if ($coach) {
            // Delete image if exists
            if (!empty($coach->image) && file_exists($coach->image)) {
                unlink($coach->image);
            }
            
            // Delete associated user account (if exists)
            try {
                $userQuery = "DELETE FROM users WHERE username = :nic AND role = 'coach'";
                $this->query($userQuery, ['nic' => $coach->nic]);
            } catch (Exception $e) {
                error_log("Failed to delete user account for coach: " . $e->getMessage());
            }
        }
        
        // Delete coach record (will also delete team_coaches relationships due to CASCADE)
        return $this->query("DELETE FROM {$this->table} WHERE id = :id", ['id' => $id]);
    }
    
    /**
     * Check if NIC exists
     */
    public function nicExists($nic, $excludeId = null)
    {
        if ($excludeId) {
            $result = $this->query("SELECT id FROM {$this->table} WHERE nic = :nic AND id != :id", [
                'nic' => $nic,
                'id' => $excludeId
            ]);
        } else {
            $result = $this->query("SELECT id FROM {$this->table} WHERE nic = :nic", ['nic' => $nic]);
        }
        return !empty($result);
    }
    
    /**
     * Assign coach to team
     */
    public function assignToTeam($coach_id, $team_id)
    {
        $query = "INSERT INTO team_coaches (team_id, coach_id) VALUES (:team_id, :coach_id)";
        return $this->query($query, [
            'team_id' => $team_id,
            'coach_id' => $coach_id
        ]);
    }
    
    /**
     * Remove coach from team
     */
    public function removeFromTeam($coach_id, $team_id)
    {
        $query = "DELETE FROM team_coaches WHERE team_id = :team_id AND coach_id = :coach_id";
        return $this->query($query, [
            'team_id' => $team_id,
            'coach_id' => $coach_id
        ]);
    }
    
    /**
     * Get coaches by team
     */
    public function getByTeamId($team_id)
    {
        $query = "SELECT c.* FROM {$this->table} c 
                  INNER JOIN team_coaches tc ON c.id = tc.coach_id 
                  WHERE tc.team_id = :team_id 
                  ORDER BY c.full_name ASC";
        
        return $this->query($query, ['team_id' => $team_id]);
    }
}
