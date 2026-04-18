<?php

require_once __DIR__ . '/User.php';

class PlayerModel
{
    use Model;
    
    protected $table = 'players';
    
    /**
     * Create a new player and link to team
     */
    public function create($data)
    {
        $query = "INSERT INTO {$this->table} (
            position, role, nic
        ) VALUES (
            :position, :role, :nic
        )";
        
        return $this->query($query, $data);
    }
    
    /**
     * Add player to team (create user if needed, then player, then link)
     */
    public function addToTeam($teamId, $userData, $playerData)
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
            
            // Check if player already exists for this NIC
            $existingPlayer = $this->query("SELECT player_id FROM {$this->table} WHERE nic = :nic", ['nic' => $nic]);
            
            if (empty($existingPlayer)) {
                // Create new player
                $playerData['nic'] = $nic;
                $this->create($playerData);
                $playerId = $this->lastInsertId();
            } else {
                // Use existing player ID
                $playerId = $existingPlayer[0]->player_id;
                // Update player details
                $this->update($playerId, $playerData);
            }
            
            if (!$playerId) {
                throw new Exception("Failed to get player ID");
            }
            
            // Check if player is already linked to this team
            $existingLink = $this->query("SELECT * FROM team_players WHERE team_id = :team_id AND player_id = :player_id", [
                'team_id' => $teamId,
                'player_id' => $playerId
            ]);
            
            if (empty($existingLink)) {
                // Link player to team
                $linkQuery = "INSERT INTO team_players (team_id, player_id) VALUES (:team_id, :player_id)";
                $this->query($linkQuery, [
                    'team_id' => $teamId,
                    'player_id' => $playerId
                ]);
            }
            
            return $playerId;
        } catch (Exception $e) {
            error_log("Error adding player to team: " . $e->getMessage());
            throw $e;
        }
    }
    
    public function getAll($limit = null, $offset = 0)
    {
        if ($limit) {
            return $this->query("SELECT * FROM {$this->table} ORDER BY player_id DESC LIMIT :limit OFFSET :offset", [
                'limit' => $limit,
                'offset' => $offset
            ]);
        }
        return $this->query("SELECT * FROM {$this->table} ORDER BY player_id DESC");
    }
    
    public function getById($id)
    {
        $result = $this->query("
            SELECT p.player_id, p.position, p.role, p.nic, u.first_name, u.last_name, u.email, u.phone_number
            FROM {$this->table} p
            LEFT JOIN users u ON p.nic = u.nic
            WHERE p.player_id = :player_id
        ", ['player_id' => $id]);
        return !empty($result) ? $result[0] : null;
    }
    
    /**
     * Get players by team ID
     */
    public function getByTeamId($team_id)
    {
        return $this->query("
            SELECT p.player_id, p.position, p.role, p.nic, u.first_name, u.last_name, u.email, u.phone_number
            FROM {$this->table} p
            JOIN team_players tp ON p.player_id = tp.player_id
            LEFT JOIN users u ON p.nic = u.nic
            WHERE tp.team_id = :team_id 
            ORDER BY p.role DESC
        ", ['team_id' => $team_id]);
    }
    
    /**
     * Update player
     */
    public function update($id, $data)
    {
        $fields = [];
        $params = ['player_id' => $id];
        
        foreach ($data as $key => $value) {
            if ($key !== 'player_id') {
                $fields[] = "{$key} = :{$key}";
                $params[$key] = $value;
            }
        }
        
        $fieldsString = implode(', ', $fields);
        $query = "UPDATE {$this->table} SET {$fieldsString} WHERE player_id = :player_id";
        
        return $this->query($query, $params);
    }
    
    /**
     * Delete player
     */
    public function delete($id)
    {
        // Get player data first to delete image and user account
        $player = $this->getById($id);
        
        if ($player) {
            // Delete image if exists
            if (!empty($player->image) && file_exists($player->image)) {
                unlink($player->image);
            }
            
            // Delete associated user account (if exists)
            try {
                $userQuery = "DELETE FROM users WHERE nic = :nic AND role = 'player'";
                $this->query($userQuery, ['nic' => $player->nic]);
            } catch (Exception $e) {
                error_log("Failed to delete user account for player: " . $e->getMessage());
            }
            
            // Delete from team_players junction table
            $this->query("DELETE FROM team_players WHERE player_id = :player_id", ['player_id' => $id]);
        }
        
        return $this->query("DELETE FROM {$this->table} WHERE player_id = :player_id", ['player_id' => $id]);
    }
    
    /**
     * Check if NIC exists
     */
    public function nicExists($nic, $excludeId = null)
    {
        if ($excludeId) {
            $result = $this->query("SELECT player_id FROM {$this->table} WHERE nic = :nic AND player_id != :player_id", [
                'nic' => $nic,
                'player_id' => $excludeId
            ]);
        } else {
            $result = $this->query("SELECT player_id FROM {$this->table} WHERE nic = :nic", ['nic' => $nic]);
        }
        return !empty($result);
    }
    
    /**
     * Check if jersey number exists in team
     */
    public function jerseyNumberExists($team_id, $jersey_number, $excludeId = null)
    {
        if ($excludeId) {
            $result = $this->query(
                "SELECT id FROM {$this->table} WHERE team_id = :team_id AND jersey_number = :jersey_number AND id != :id",
                [
                    'team_id' => $team_id,
                    'jersey_number' => $jersey_number,
                    'id' => $excludeId
                ]
            );
        } else {
            $result = $this->query(
                "SELECT id FROM {$this->table} WHERE team_id = :team_id AND jersey_number = :jersey_number",
                [
                    'team_id' => $team_id,
                    'jersey_number' => $jersey_number
                ]
            );
        }
        return !empty($result);
    }
    
    /**
     * Get captain of a team
     */
    public function getCaptain($team_id)
    {
        $result = $this->query("SELECT * FROM {$this->table} WHERE team_id = :team_id AND role = 'captain'", [
            'team_id' => $team_id
        ]);
        return !empty($result) ? $result[0] : null;
    }
    
    /**
     * Get vice captain of a team
     */
    public function getViceCaptain($team_id)
    {
        $result = $this->query("SELECT * FROM {$this->table} WHERE team_id = :team_id AND role = 'vice_captain'", [
            'team_id' => $team_id
        ]);
        return !empty($result) ? $result[0] : null;
    }
    
    /**
     * Count players in a team
     */
    public function countByTeam($team_id)
    {
        $result = $this->query("SELECT COUNT(*) as count FROM {$this->table} WHERE team_id = :team_id", [
            'team_id' => $team_id
        ]);
        return $result[0]->count ?? 0;
    }
    
    /**
     * Search players
     */
    public function search($keyword)
    {
        $keyword = "%{$keyword}%";
        return $this->query(
            "SELECT * FROM {$this->table} 
            WHERE full_name LIKE :keyword 
            OR name_with_initials LIKE :keyword 
            OR nic LIKE :keyword 
            OR uni_register_number LIKE :keyword 
            ORDER BY full_name ASC",
            ['keyword' => $keyword]
        );
    }
}
