<?php

class StoreItemModel {
    use Model;

    protected $table = 'store_items';
    protected $primaryKey = 'item_id';
    protected $allowedColumns = [
        'team_id',
        'item_name',
        'description',
        'category',
        'price',
        'quantity',
        'item_image',
        'status',
        'created_at',
        'updated_at'
    ];

    /**
     * Get all store items for a team
     */
    public function getByTeamId($team_id)
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE team_id = :team_id 
                ORDER BY created_at DESC";
        
        return $this->query($sql, ['team_id' => $team_id]);
    }

    /**
     * Get available items for a team
     */
    public function getAvailableByTeamId($team_id)
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE team_id = :team_id AND status = 'Available'
                ORDER BY created_at DESC";
        
        return $this->query($sql, ['team_id' => $team_id]);
    }

    /**
     * Get single item by ID
     */
    public function getById($item_id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE item_id = :item_id";
        $result = $this->query($sql, ['item_id' => $item_id]);
        return $result[0] ?? null;
    }

    /**
     * Search items by name
     */
    public function searchByName($team_id, $search)
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE team_id = :team_id AND item_name LIKE :search
                ORDER BY created_at DESC";
        
        return $this->query($sql, [
            'team_id' => $team_id,
            'search' => "%{$search}%"
        ]);
    }

    /**
     * Get items by category
     */
    public function getByCategory($team_id, $category)
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE team_id = :team_id AND category = :category
                ORDER BY created_at DESC";
        
        return $this->query($sql, [
            'team_id' => $team_id,
            'category' => $category
        ]);
    }

    /**
     * Update item status
     */
    public function updateStatus($item_id, $status)
    {
        $sql = "UPDATE {$this->table} SET status = :status WHERE item_id = :item_id";
        return $this->query($sql, ['status' => $status, 'item_id' => $item_id]);
    }

    /**
     * Update item quantity
     */
    public function updateQuantity($item_id, $quantity)
    {
        $sql = "UPDATE {$this->table} SET quantity = :quantity WHERE item_id = :item_id";
        return $this->query($sql, ['quantity' => $quantity, 'item_id' => $item_id]);
    }
}
