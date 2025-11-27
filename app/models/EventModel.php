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
        if (empty($data['title']) || empty($data['event_date'])) {
            return false;
        }
        
        // Set defaults for optional fields
        $eventData = [
            'title' => $data['title'],
            'description' => $data['description'] ?? '',
            'event_date' => $data['event_date'],
            'event_time' => $data['event_time'] ?? '00:00:00',
            'location' => $data['location'] ?? '',
            'event_type' => $data['event_type'] ?? 'match',
            'event_category' => $data['event_category'] ?? 'general',
            'status' => $data['status'] ?? 'upcoming',
            'image' => $data['image'] ?? null,
            'is_featured' => $data['is_featured'] ?? 0,
            'created_by' => $data['created_by'] ?? null
        ];
        
        $query = "INSERT INTO {$this->table} 
                  (title, description, event_date, event_time, location, event_type, event_category, image, status, is_featured, created_by) 
                  VALUES 
                  (:title, :description, :event_date, :event_time, :location, :event_type, :event_category, :image, :status, :is_featured, :created_by)";
        
        try {
            $this->query($query, $eventData);
            return true; // Query executed successfully
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
        $params = ['id' => $id];
        
        foreach ($data as $key => $value) {
            $fields[] = "$key = :$key";
            $params[$key] = $value;
        }
        
        $fieldString = implode(', ', $fields);
        $query = "UPDATE {$this->table} SET $fieldString WHERE id = :id";
        
        try {
            $this->query($query, $params);
            return true; // Query executed successfully
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
        return $this->query("SELECT * FROM {$this->table} ORDER BY event_date ASC");
    }
    
    /**
     * Get event by ID
     */
    public function getById($id)
    {
        $result = $this->query("SELECT * FROM {$this->table} WHERE id = :id LIMIT 1", ['id' => $id]);
        return $result ? $result[0] : null;
    }
    
    /**
     * Get upcoming events
     */
    public function getUpcoming($limit = null)
    {
        $query = "SELECT * FROM {$this->table} 
                  WHERE event_date >= CURDATE() 
                  ORDER BY event_date ASC, event_time ASC";
        
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
                  WHERE is_featured = 1 AND status = 'upcoming' AND event_date >= CURDATE() 
                  ORDER BY event_date ASC, event_time ASC 
                  LIMIT :limit";
        
        return $this->query($query, ['limit' => $limit]);
    }
    
    /**
     * Get events by status
     */
    public function getByStatus($status)
    {
        return $this->query("SELECT * FROM {$this->table} WHERE status = :status ORDER BY event_date DESC", [
            'status' => $status
        ]);
    }
    
    /**
     * Get events by type
     */
    public function getByType($type)
    {
        return $this->query("SELECT * FROM {$this->table} WHERE event_type = :type ORDER BY event_date ASC", [
            'type' => $type
        ]);
    }
    
    /**
     * Get events by category
     */
    public function getByCategory($category)
    {
        return $this->query("SELECT * FROM {$this->table} WHERE event_category = :category ORDER BY event_date ASC", [
            'category' => $category
        ]);
    }
    
    /**
     * Search events
     */
    public function search($keyword)
    {
        $keyword = '%' . $keyword . '%';
        return $this->query(
            "SELECT * FROM {$this->table} 
             WHERE title LIKE :keyword OR description LIKE :keyword OR location LIKE :keyword
             ORDER BY event_date ASC",
            ['keyword' => $keyword]
        );
    }
    
    /**
     * Delete event
     */
    public function delete($id)
    {
        try {
            $this->query("DELETE FROM {$this->table} WHERE id = :id", ['id' => $id]);
            return true; // Query executed successfully
        } catch (Exception $e) {
            error_log("EventModel::delete() error: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Count events by status
     */
    public function countByStatus($status)
    {
        $result = $this->query(
            "SELECT COUNT(*) as count FROM {$this->table} WHERE status = :status",
            ['status' => $status]
        );
        return $result ? $result[0]->count : 0;
    }
    
    /**
     * Get events in date range
     */
    public function getByDateRange($startDate, $endDate)
    {
        return $this->query(
            "SELECT * FROM {$this->table} 
             WHERE event_date BETWEEN :start_date AND :end_date 
             ORDER BY event_date ASC, event_time ASC",
            [
                'start_date' => $startDate,
                'end_date' => $endDate
            ]
        );
    }
    
    /**
     * Update event status
     */
    public function updateStatus($id, $status)
    {
        return $this->update($id, ['status' => $status]);
    }
    
    /**
     * Toggle featured status
     */
    public function toggleFeatured($id)
    {
        $query = "UPDATE {$this->table} SET is_featured = NOT is_featured WHERE id = :id";
        return $this->query($query, ['id' => $id]);
    }
}
