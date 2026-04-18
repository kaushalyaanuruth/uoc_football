<?php

class TeamModel
{
    use Model;
    
    protected $table = 'teams';

    private $columnCache = null;

    private function getColumns()
    {
        if ($this->columnCache !== null) {
            return $this->columnCache;
        }

        $this->columnCache = [];
        $rows = $this->query("SHOW COLUMNS FROM {$this->table}");

        foreach ($rows as $row) {
            if (isset($row->Field)) {
                $this->columnCache[$row->Field] = true;
            }
        }

        return $this->columnCache;
    }

    private function hasColumn($name)
    {
        $columns = $this->getColumns();
        return isset($columns[$name]);
    }
    
    /** Create a new team*/
    public function create($data)
    {
        // Don't include created_by if it's null or empty
        if (empty($data['created_by'])) {
            unset($data['created_by']);
        }

        if ($this->hasColumn('status')) {
            $query = "INSERT INTO {$this->table} (season, status) VALUES (:season, :status)";
            return $this->query($query, [
                'season' => $data['season'],
                'status' => $data['status'] ?? 'present'
            ]);
        }

        $query = "INSERT INTO {$this->table} (season) VALUES (:season)";
        return $this->query($query, ['season' => $data['season']]);
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
        $statusSelect = $this->hasColumn('status') ? 't.status' : "'present' AS status";

        $query = "SELECT
                    t.team_id,
                    t.season,
                    {$statusSelect}
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
        $hasStatus = $this->hasColumn('status');
        
        foreach ($data as $key => $value) {
            if ($key !== 'team_id') {
                if ($key === 'status' && !$hasStatus) {
                    continue;
                }
                $fields[] = "{$key} = :{$key}";
                $params[$key] = $value;
            }
        }

        if (empty($fields)) {
            return true;
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
        if (!$this->hasColumn('status')) {
            if ($status === 'present') {
                return $this->getAll();
            }
            return [];
        }

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