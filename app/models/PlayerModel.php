<?php

class PlayerModel
{
    use Model;
    
    protected $table = 'players';
    
    /**
     * Create a new player
     */
    public function create($data)
    {
        $query = "INSERT INTO {$this->table} (
            team_id, full_name, name_with_initials, faculty, position, 
            role, jersey_number, nic, uni_register_number, mobile_number, 
            address, height, weight, image
        ) VALUES (
            :team_id, :full_name, :name_with_initials, :faculty, :position, 
            :role, :jersey_number, :nic, :uni_register_number, :mobile_number, 
            :address, :height, :weight, :image
        )";
        
        return $this->query($query, $data);
    }
    
    /**
     * Get all players
     */
    public function getAll($limit = null, $offset = 0)
    {
        if ($limit) {
            return $this->query("SELECT * FROM {$this->table} ORDER BY created_at DESC LIMIT :limit OFFSET :offset", [
                'limit' => $limit,
                'offset' => $offset
            ]);
        }
        return $this->query("SELECT * FROM {$this->table} ORDER BY created_at DESC");
    }
    
    /**
     * Get player by ID
     */
    public function getById($id)
    {
        $result = $this->query("SELECT * FROM {$this->table} WHERE id = :id", ['id' => $id]);
        return !empty($result) ? $result[0] : null;
    }
    
    /**
     * Get players by team ID
     */
    public function getByTeamId($team_id)
    {
        return $this->query("SELECT * FROM {$this->table} WHERE team_id = :team_id ORDER BY role DESC, jersey_number ASC", [
            'team_id' => $team_id
        ]);
    }
    
    /**
     * Update player
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
                $userQuery = "DELETE FROM users WHERE username = :nic AND role = 'player'";
                $this->query($userQuery, ['nic' => $player->nic]);
            } catch (Exception $e) {
                error_log("Failed to delete user account for player: " . $e->getMessage());
            }
        }
        
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
