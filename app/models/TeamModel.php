<?php

class TeamModel
{
    use Model;
    
    protected $table = 'teams';

    private $columnCache = null;

    private function normalizeStatus($status)
    {
        $value = strtolower(trim((string) $status));
        return $value === 'past' ? 'past' : 'present';
    }

    private function clearColumnCache()
    {
        $this->columnCache = null;
    }

    private function enforceSinglePresentTeam()
    {
        if (!$this->hasColumn('status')) {
            return;
        }

        $presentRows = $this->query(
            "SELECT team_id FROM {$this->table} WHERE status = 'present' ORDER BY team_id DESC"
        );

        if (!empty($presentRows)) {
            $keepPresentId = (int) ($presentRows[0]->team_id ?? 0);
            if ($keepPresentId > 0) {
                $this->query(
                    "UPDATE {$this->table} SET status = 'past' WHERE status = 'present' AND team_id != :team_id",
                    ['team_id' => $keepPresentId]
                );
            }
            return;
        }

        $latestRows = $this->query("SELECT team_id FROM {$this->table} ORDER BY team_id DESC LIMIT 1");
        if (!empty($latestRows)) {
            $latestTeamId = (int) ($latestRows[0]->team_id ?? 0);
            if ($latestTeamId > 0) {
                $this->query("UPDATE {$this->table} SET status = 'past'");
                $this->query(
                    "UPDATE {$this->table} SET status = 'present' WHERE team_id = :team_id",
                    ['team_id' => $latestTeamId]
                );
            }
        }
    }

    private function getExistingPresentTeamId($excludeTeamId = null)
    {
        if (!$this->hasColumn('status')) {
            return null;
        }

        $query = "SELECT team_id FROM {$this->table} WHERE status = 'present'";
        $params = [];

        if ($excludeTeamId !== null) {
            $query .= " AND team_id != :exclude_team_id";
            $params['exclude_team_id'] = (int) $excludeTeamId;
        }

        $query .= " ORDER BY team_id DESC LIMIT 1";

        $rows = $this->query($query, $params);
        return !empty($rows) ? ((int) ($rows[0]->team_id ?? 0) ?: null) : null;
    }

    private function assertNoOtherPresentTeam($excludeTeamId = null)
    {
        $existingPresentTeamId = $this->getExistingPresentTeamId($excludeTeamId);
        if ($existingPresentTeamId !== null) {
            throw new Exception('A present team already exists. Set it to past before creating/updating another present team.');
        }
    }

    public function ensureStatusColumn()
    {
        if ($this->hasColumn('status')) {
            $this->enforceSinglePresentTeam();
            return true;
        }

        try {
            $this->query("ALTER TABLE {$this->table} ADD COLUMN status ENUM('present','past') NOT NULL DEFAULT 'past' AFTER season");
            $this->clearColumnCache();

            if ($this->hasColumn('status')) {
                $latestRows = $this->query("SELECT team_id FROM {$this->table} ORDER BY team_id DESC LIMIT 1");
                if (!empty($latestRows)) {
                    $latestTeamId = (int) ($latestRows[0]->team_id ?? 0);
                    if ($latestTeamId > 0) {
                        $this->query("UPDATE {$this->table} SET status = 'past'");
                        $this->query("UPDATE {$this->table} SET status = 'present' WHERE team_id = :team_id", ['team_id' => $latestTeamId]);
                    }
                }
            }
        } catch (Exception $e) {
            return false;
        }

        if ($this->hasColumn('status')) {
            $this->enforceSinglePresentTeam();
        }

        return $this->hasColumn('status');
    }

    private function markOtherTeamsAsPast($presentTeamId)
    {
        if (!$this->hasColumn('status') || (int) $presentTeamId <= 0) {
            return;
        }

        $this->query(
            "UPDATE {$this->table} SET status = 'past' WHERE team_id != :team_id AND status = 'present'",
            ['team_id' => (int) $presentTeamId]
        );
    }

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
        $this->ensureStatusColumn();

        // Don't include created_by if it's null or empty
        if (empty($data['created_by'])) {
            unset($data['created_by']);
        }

        if ($this->hasColumn('status')) {
            $status = $this->normalizeStatus($data['status'] ?? 'present');

            if ($status === 'present') {
                $this->assertNoOtherPresentTeam();
            }

            $query = "INSERT INTO {$this->table} (season, status) VALUES (:season, :status)";
            return $this->query($query, [
                'season' => $data['season'],
                'status' => $status
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
        $this->ensureStatusColumn();

        $fields = [];
        $params = ['team_id' => $id];
        $hasStatus = $this->hasColumn('status');
        
        foreach ($data as $key => $value) {
            if ($key !== 'team_id') {
                if ($key === 'status' && !$hasStatus) {
                    continue;
                }
                $fields[] = "{$key} = :{$key}";
                if ($key === 'status') {
                    $params[$key] = $this->normalizeStatus($value);
                } else {
                    $params[$key] = $value;
                }
            }
        }

        if (empty($fields)) {
            return true;
        }

        if ($hasStatus && isset($params['status']) && $params['status'] === 'present') {
            $this->assertNoOtherPresentTeam((int) $id);
        }
        
        $fieldsString = implode(', ', $fields);
        $query = "UPDATE {$this->table} SET {$fieldsString} WHERE team_id = :team_id";
        
        $saved = $this->query($query, $params);

        return $saved;
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
        $this->ensureStatusColumn();

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

    public function getPresentTeamId()
    {
        $this->ensureStatusColumn();

        if ($this->hasColumn('status')) {
            $rows = $this->query("SELECT team_id FROM {$this->table} WHERE status = 'present' ORDER BY team_id DESC LIMIT 1");
            if (!empty($rows)) {
                return (int) ($rows[0]->team_id ?? 0) ?: null;
            }
        }

        $rows = $this->query("SELECT team_id FROM {$this->table} ORDER BY team_id DESC LIMIT 1");
        return !empty($rows) ? ((int) ($rows[0]->team_id ?? 0) ?: null) : null;
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