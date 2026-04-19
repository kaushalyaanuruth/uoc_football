<?php

class StoreManagementModel {
    use Model;

    protected $table = 'store_management';
    protected $primaryKey = 'item_id';
    protected $allowedColumns = ['item_name', 'description', 'category', 'price', 'quantity', 'item_image', 'status'];

    /**
     * Create a new store item
     */
    public function create($data) {
        $query = "INSERT INTO {$this->table} (
            item_name, description, category, price, quantity, item_image, status
        ) VALUES (
            :item_name, :description, :category, :price, :quantity, :item_image, :status
        )";

        try {
            $this->query($query, $data);
            return true;
        } catch (Exception $e) {
            error_log("Create item error: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Update a store item
     */
    public function update($item_id, $data) {
        // Filter to only allowed columns
        $allowed = array_intersect_key($data, array_flip($this->allowedColumns));
        
        if (empty($allowed)) {
            return false;
        }

        $setParts = [];
        foreach ($allowed as $col => $val) {
            $setParts[] = "$col = :$col";
        }

        $query = "UPDATE {$this->table} SET " . implode(', ', $setParts) . " WHERE item_id = :item_id";
        $allowed['item_id'] = $item_id;

        try {
            $this->query($query, $allowed);
            return true;
        } catch (Exception $e) {
            error_log("Update item error: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Delete a store item by ID
     */
    public function delete($item_id) {
        $query = "DELETE FROM {$this->table} WHERE item_id = :item_id";
        
        try {
            $this->query($query, ['item_id' => $item_id]);
            return true;
        } catch (Exception $e) {
            error_log("Delete item error: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get all store items
     */
    public function getAll() {
        $query = "SELECT * FROM {$this->table} ORDER BY item_name ASC";
        return $this->query($query);
    }

    /**
     * Get item by ID
     */
    public function getById($item_id) {
        $query = "SELECT * FROM {$this->table} WHERE item_id = :item_id";
        $result = $this->query($query, ['item_id' => $item_id]);
        return $result[0] ?? null;
    }

    /**
     * Search items by name
     */
    public function searchByName($search) {
        $query = "SELECT * FROM {$this->table} WHERE item_name LIKE :search ORDER BY item_name ASC";
        return $this->query($query, ['search' => "%{$search}%"]);
    }

    /**
     * Get items by category
     */
    public function getByCategory($category) {
        $query = "SELECT * FROM {$this->table} WHERE category = :category ORDER BY item_name ASC";
        return $this->query($query, ['category' => $category]);
    }

    /**
     * Get available items (not sold out)
     */
    public function getAvailable() {
        $query = "SELECT * FROM {$this->table} WHERE status = 'Available' ORDER BY item_name ASC";
        return $this->query($query);
    }

    /**
     * Get items by status
     */
    public function getByStatus($status) {
        $query = "SELECT * FROM {$this->table} WHERE status = :status ORDER BY item_name ASC";
        return $this->query($query, ['status' => $status]);
    }

    /**
     * Update item status
     */
    public function updateStatus($item_id, $status) {
        $query = "UPDATE {$this->table} SET status = :status WHERE item_id = :item_id";
        try {
            $this->query($query, ['item_id' => $item_id, 'status' => $status]);
            return true;
        } catch (Exception $e) {
            error_log("Update status error: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Update item quantity
     */
    public function updateQuantity($item_id, $quantity) {
        $query = "UPDATE {$this->table} SET quantity = :quantity WHERE item_id = :item_id";
        try {
            $this->query($query, ['item_id' => $item_id, 'quantity' => $quantity]);
            return true;
        } catch (Exception $e) {
            error_log("Update quantity error: " . $e->getMessage());
            throw $e;
        }
    }
}
