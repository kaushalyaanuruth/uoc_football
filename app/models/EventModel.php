<?php

class EventModel
{
    use Model;
    
    protected $table = 'events';
    
    /**
     * Create a new event
     */
    public function create($data)
    {
        // Required fields
        if (empty($data['location']) || empty($data['date'])) {
            return false;
        }
        
        // Set data for the events table
        $eventData = [
            'location' => $data['location'],
            'date' => $data['date'],
            'title' => $data['title'] ?? 'UOC Football Event',
            'event_type' => $data['event_type'] ?? 'match',
            'event_time' => $data['event_time'] ?? '15:00:00',
            'description' => $data['description'] ?? ''
        ];
        
        $query = "INSERT INTO {$this->table} (location, `date`, title, event_type, event_time, description) 
                  VALUES (:location, :date, :title, :event_type, :event_time, :description)";
        
        try {
            $this->query($query, $eventData);
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
            $fields[] = "`$key` = :$key";
            $params[$key] = $value;
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

