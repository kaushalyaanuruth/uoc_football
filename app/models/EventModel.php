<?php

class EventModel
{
    use Model;
    
    protected $table = 'events';
    private $columnCache = null;

    private function getColumns()
    {
        if (is_array($this->columnCache)) {
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

    private function hasColumn($column)
    {
        $columns = $this->getColumns();
        return isset($columns[$column]);
    }
    
    /**
     * Create a new event
     */
    public function create($data)
    {
        // Required fields
        if (empty($data['location']) || empty($data['date'])) {
            return false;
        }

        $insertData = [
            'location' => $data['location'],
            'date' => $data['date'],
            'title' => $data['title'] ?? 'UOC Football Event',
            'event_type' => $data['event_type'] ?? 'match',
            'event_time' => $data['event_time'] ?? '15:00:00',
            'description' => $data['description'] ?? ''
        ];

        if ($this->hasColumn('status')) {
            $insertData['status'] = $data['status'] ?? 'upcoming';
        }

        if ($this->hasColumn('image')) {
            $insertData['image'] = $data['image'] ?? null;
        }

        $columns = [];
        $placeholders = [];
        $params = [];

        foreach ($insertData as $key => $value) {
            if (!$this->hasColumn($key)) {
                continue;
            }

            $columns[] = ($key === 'date') ? "`date`" : "`{$key}`";
            $placeholders[] = ":{$key}";
            $params[$key] = $value;
        }

        if (empty($columns)) {
            throw new Exception('No compatible columns found for events insert');
        }

        $query = "INSERT INTO {$this->table} (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $placeholders) . ")";
        
        try {
            $this->query($query, $params);
            return true;
        } catch (Exception $e) {
            error_log("EventModel::create() error: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Update event
     */
    public function update($id, $data)
    {
        if (empty($data)) {
            return false;
        }
        
        $fields = [];
        $params = ['event_id' => $id];
        
        foreach ($data as $key => $value) {
            if (!$this->hasColumn($key)) {
                continue;
            }

            $fields[] = "`$key` = :$key";
            $params[$key] = $value;
        }

        if (empty($fields)) {
            return true;
        }
        
        $fieldString = implode(', ', $fields);
        $query = "UPDATE {$this->table} SET $fieldString WHERE event_id = :event_id";
        
        try {
            $this->query($query, $params);
            return true;
        } catch (Exception $e) {
            error_log("EventModel::update() error: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Get all events
     */
    public function getAll()
    {
        return $this->query("SELECT * FROM {$this->table} ORDER BY `date` ASC");
    }
    
    /**
     * Get event by ID
     */
    public function getById($id)
    {
        $result = $this->query("SELECT * FROM {$this->table} WHERE event_id = :id LIMIT 1", ['id' => $id]);
        return $result ? $result[0] : null;
    }
    
    /**
     * Get upcoming events
     */
    public function getUpcoming($limit = null)
    {
        $query = "SELECT * FROM {$this->table} 
                  WHERE `date` >= CURDATE() 
                  ORDER BY `date` ASC";
        
        if ($limit) {
            $query .= " LIMIT " . (int)$limit;
        }
        
        return $this->query($query);
    }
    
    /**
     * Get featured events (for landing page)
     */
    public function getFeatured($limit = 5)
    {
        $query = "SELECT * FROM {$this->table} 
                  WHERE `date` >= CURDATE() 
                  ORDER BY `date` ASC 
                  LIMIT :limit";
        
        return $this->query($query, ['limit' => $limit]);
    }
    
    /**
     * Delete event
     */
    public function delete($id)
    {
        try {
            $this->query("DELETE FROM {$this->table} WHERE event_id = :id", ['id' => $id]);
            return true;
        } catch (Exception $e) {
            error_log("EventModel::delete() error: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Get events by location
     */
    public function getByLocation($location)
    {
        return $this->query(
            "SELECT * FROM {$this->table} WHERE location LIKE :location ORDER BY `date` ASC",
            ['location' => '%' . $location . '%']
        );
    }
    
    /**
     * Get events in date range
     */
    public function getByDateRange($startDate, $endDate)
    {
        return $this->query(
            "SELECT * FROM {$this->table} 
             WHERE `date` BETWEEN :start_date AND :end_date 
             ORDER BY `date` ASC",
            [
                'start_date' => $startDate,
                'end_date' => $endDate
            ]
        );
    }
}

