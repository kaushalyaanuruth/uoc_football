<?php

require_once __DIR__ . '/User.php';

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
            nic, license
        ) VALUES (
            :nic, :license
        )";
        
        return $this->query($query, $data);
    }
    
    /**
     * Add coach to team (create user if needed, then coach, then link)
     */
    public function addToTeam($teamId, $userData, $coachData)
    {
        try {
            $userModel = new User();
            $nic = $userData['nic'];
            
            // Check if user already exists
            $existingUser = $userModel->query("SELECT * FROM users WHERE nic = :nic", ['nic' => $nic]);
            
            if (empty($existingUser)) {
                // Create new user
                $userData['user_id'] = $nic;
                $userData['password'] = password_hash('123456', PASSWORD_BCRYPT);
                
                $userQuery = "INSERT INTO users (
                    nic, user_id, password, first_name, last_name, email, phone_number, image
                ) VALUES (
                    :nic, :user_id, :password, :first_name, :last_name, :email, :phone_number, :image
                )";
                $userModel->query($userQuery, $userData);
            } else {
                // Update existing user with new details
                $updateData = [
                    'first_name' => $userData['first_name'],
                    'last_name' => $userData['last_name'],
                    'email' => $userData['email'],
                    'phone_number' => $userData['phone_number']
                ];
                $userModel->updateByNic($nic, $updateData);
            }
            
            // Check if coach already exists for this NIC
            $existingCoach = $this->query("SELECT coach_id FROM {$this->table} WHERE nic = :nic", ['nic' => $nic]);
            
            if (empty($existingCoach)) {
                // Create new coach
                $coachData['nic'] = $nic;
                $this->create($coachData);
                $coachId = $this->lastInsertId();
            } else {
                // Use existing coach ID
                $coachId = $existingCoach[0]->coach_id;
                // Update coach details
                $this->update($coachId, $coachData);
            }
            
            if (!$coachId) {
                throw new Exception("Failed to get coach ID");
            }
            
            // Check if coach is already linked to this team
            $existingLink = $this->query("SELECT * FROM team_coaches WHERE team_id = :team_id AND coach_id = :coach_id", [
                'team_id' => $teamId,
                'coach_id' => $coachId
            ]);
            
            if (empty($existingLink)) {
                // Link coach to team
                $linkQuery = "INSERT INTO team_coaches (team_id, coach_id) VALUES (:team_id, :coach_id)";
                $this->query($linkQuery, [
                    'team_id' => $teamId,
                    'coach_id' => $coachId
                ]);
            }
            
            return $coachId;
        } catch (Exception $e) {
            error_log("Error adding coach to team: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Get all coaches
     */
    public function getAll()
    {
        return $this->query("SELECT * FROM {$this->table} ORDER BY coach_id DESC");
    }
    
    /**
     * Get coach by ID
     */
    public function getById($id)
    {
        $result = $this->query("
            SELECT c.coach_id, c.license, c.nic, u.first_name, u.last_name, u.email, u.phone_number
            FROM {$this->table} c
            LEFT JOIN users u ON c.nic = u.nic
            WHERE c.coach_id = :coach_id
        ", ['coach_id' => $id]);
        return !empty($result) ? $result[0] : null;
    }
    
    /**
     * Update coach
     */
    public function update($id, $data)
    {
        $fields = [];
        $params = ['coach_id' => $id];
        
        foreach ($data as $key => $value) {
            if ($key !== 'coach_id') {
                $fields[] = "{$key} = :{$key}";
                $params[$key] = $value;
            }
        }
        
        $fieldsString = implode(', ', $fields);
        $query = "UPDATE {$this->table} SET {$fieldsString} WHERE coach_id = :coach_id";
        
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
                $userQuery = "DELETE FROM users WHERE nic = :nic AND role = 'coach'";
                $this->query($userQuery, ['nic' => $coach->nic]);
            } catch (Exception $e) {
                error_log("Failed to delete user account for coach: " . $e->getMessage());
            }
            
            // Delete from team_coaches junction table
            $this->query("DELETE FROM team_coaches WHERE coach_id = :coach_id", ['coach_id' => $id]);
        }
        
        // Delete coach record
        return $this->query("DELETE FROM {$this->table} WHERE coach_id = :coach_id", ['coach_id' => $id]);
    }
    
    /**
     * Check if NIC exists
     */
    public function nicExists($nic, $excludeId = null)
    {
        if ($excludeId) {
            $result = $this->query("SELECT coach_id FROM {$this->table} WHERE nic = :nic AND coach_id != :coach_id", [
                'nic' => $nic,
                'coach_id' => $excludeId
            ]);
        } else {
            $result = $this->query("SELECT coach_id FROM {$this->table} WHERE nic = :nic", ['nic' => $nic]);
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
        $query = "SELECT c.coach_id, c.license, c.nic, u.first_name, u.last_name, u.email, u.phone_number 
                  FROM {$this->table} c 
                  INNER JOIN team_coaches tc ON c.coach_id = tc.coach_id 
                  LEFT JOIN users u ON c.nic = u.nic
                  WHERE tc.team_id = :team_id 
                  ORDER BY u.first_name ASC";
        
        return $this->query($query, ['team_id' => $team_id]);
    }
}
