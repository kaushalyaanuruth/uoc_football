<?php

class GalleryModel
{
    use Model;
    
    protected $table = 'gallery';
    
    /**
     * Create a new gallery image
     */
    public function create($data)
    {
        $defaults = [
            'category' => 'other',
            'status' => 'draft',
            'tags' => '',
            'description' => '',
            'uploaded_by' => null
        ];
        
        $data = array_merge($defaults, $data);
        
        $query = "INSERT INTO {$this->table} 
                  (filename, filepath, description, category, tags, status, uploaded_by) 
                  VALUES 
                  (:filename, :filepath, :description, :category, :tags, :status, :uploaded_by)";
        
        return $this->query($query, $data);
    }
    
    /**
     * Update gallery image
     */
    public function update($id, $data)
    {
        $fields = [];
        $params = ['id' => $id];
        
        foreach ($data as $key => $value) {
            $fields[] = "$key = :$key";
            $params[$key] = $value;
        }
        
        $fieldString = implode(', ', $fields);
        $query = "UPDATE {$this->table} SET $fieldString WHERE id = :id";
        
        return $this->query($query, $params);
    }
    
    /**
     * Get all gallery images
     */
    public function getAll()
    {
        return $this->query("SELECT g.*, u.username as uploader_name 
                            FROM {$this->table} g 
                            LEFT JOIN users u ON g.uploaded_by = u.id 
                            ORDER BY g.created_at DESC");
    }
    
    /**
     * Get image by ID
     */
    public function getById($id)
    {
        $result = $this->query("SELECT * FROM {$this->table} WHERE id = :id LIMIT 1", ['id' => $id]);
        return $result ? $result[0] : null;
    }
    
    /**
     * Get published images only
     */
    public function getPublished()
    {
        return $this->query("SELECT * FROM {$this->table} WHERE status = 'published' ORDER BY created_at DESC");
    }
    
    /**
     * Get images by status
     */
    public function getByStatus($status)
    {
        return $this->query("SELECT * FROM {$this->table} WHERE status = :status ORDER BY created_at DESC", [
            'status' => $status
        ]);
    }
    
    /**
     * Get images by category
     */
    public function getByCategory($category)
    {
        return $this->query("SELECT * FROM {$this->table} WHERE category = :category ORDER BY created_at DESC", [
            'category' => $category
        ]);
    }
    
    /**
     * Filter images
     */
    public function filter($category = '', $dateRange = '')
    {
        $query = "SELECT * FROM {$this->table} WHERE 1=1";
        $params = [];
        
        if (!empty($category)) {
            $query .= " AND category = :category";
            $params['category'] = $category;
        }
        
        if (!empty($dateRange)) {
            switch ($dateRange) {
                case 'today':
                    $query .= " AND DATE(created_at) = CURDATE()";
                    break;
                case 'week':
                    $query .= " AND created_at >= DATE_SUB(NOW(), INTERVAL 1 WEEK)";
                    break;
                case 'month':
                    $query .= " AND created_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH)";
                    break;
            }
        }
        
        $query .= " ORDER BY created_at DESC";
        
        return $this->query($query, $params);
    }
    
    /**
     * Search images
     */
    public function search($keyword)
    {
        $keyword = '%' . $keyword . '%';
        return $this->query(
            "SELECT * FROM {$this->table} 
             WHERE description LIKE :keyword OR tags LIKE :keyword
             ORDER BY created_at DESC",
            ['keyword' => $keyword]
        );
    }
    
    /**
     * Delete image
     */
    public function delete($id)
    {
        return $this->query("DELETE FROM {$this->table} WHERE id = :id", ['id' => $id]);
    }
    
    /**
     * Toggle status
     */
    public function toggleStatus($id)
    {
        $query = "UPDATE {$this->table} 
                  SET status = CASE 
                      WHEN status = 'published' THEN 'draft' 
                      ELSE 'published' 
                  END 
                  WHERE id = :id";
        return $this->query($query, ['id' => $id]);
    }
    
    /**
     * Count images by status
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
     * Get recent uploads
     */
    public function getRecent($limit = 10)
    {
        return $this->query(
            "SELECT * FROM {$this->table} ORDER BY created_at DESC LIMIT :limit",
            ['limit' => $limit]
        );
    }
    
    /**
     * Get images with pagination
     */
    public function getPaginated($page = 1, $perPage = 40, $category = null)
    {
        $offset = ($page - 1) * $perPage;
        
        $query = "SELECT * FROM {$this->table} WHERE status = 'published'";
        $params = [];
        
        if ($category) {
            $query .= " AND category = :category";
            $params['category'] = $category;
        }
        
        $query .= " ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
        $params['limit'] = $perPage;
        $params['offset'] = $offset;
        
        return $this->query($query, $params);
    }
    
    /**
     * Get total count of images
     */
    public function getTotalCount($category = null)
    {
        $query = "SELECT COUNT(*) as total FROM {$this->table} WHERE status = 'published'";
        $params = [];
        
        if ($category) {
            $query .= " AND category = :category";
            $params['category'] = $category;
        }
        
        $result = $this->query($query, $params);
        return $result ? $result[0]->total : 0;
    }
}
