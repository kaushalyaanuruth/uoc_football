<?php

class StoreManagementModel {
    use Model;

    protected $table = 'store_management';
    protected $primaryKey = 'item_id';
    protected $allowedColumns = ['item_name', 'description', 'category', 'price', 'quantity', 'item_image', 'status'];
    private $tableEnsured = false;

    private function ensureTableExists() {
        if ($this->tableEnsured) {
            return;
        }

        $query = "CREATE TABLE IF NOT EXISTS {$this->table} (
            item_id INT AUTO_INCREMENT PRIMARY KEY,
            item_name VARCHAR(255) NOT NULL,
            description TEXT,
            category VARCHAR(100),
            price DECIMAL(10, 2) NOT NULL,
            quantity INT NOT NULL DEFAULT 0,
            item_image VARCHAR(255),
            status ENUM('Available', 'Sold Out') DEFAULT 'Available'
        )";

        $this->query($query);
        $this->tableEnsured = true;
    }

    /**
     * Create a new store item
     */
    public function create($data) {
        $this->ensureTableExists();

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
        $this->ensureTableExists();

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
        $this->ensureTableExists();

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
        try {
            $this->ensureTableExists();
            $query = "SELECT * FROM {$this->table} ORDER BY item_name ASC";
            return $this->query($query);
        } catch (Exception $e) {
            error_log("Get all store items error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get item by ID
     */
    public function getById($item_id) {
        try {
            $this->ensureTableExists();
            $query = "SELECT * FROM {$this->table} WHERE item_id = :item_id";
            $result = $this->query($query, ['item_id' => $item_id]);
            return $result[0] ?? null;
        } catch (Exception $e) {
            error_log("Get store item by id error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Search items by name
     */
    public function searchByName($search) {
        try {
            $this->ensureTableExists();
            $query = "SELECT * FROM {$this->table} WHERE item_name LIKE :search ORDER BY item_name ASC";
            return $this->query($query, ['search' => "%{$search}%"]);
        } catch (Exception $e) {
            error_log("Search store items error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get items by category
     */
    public function getByCategory($category) {
        try {
            $this->ensureTableExists();
            $query = "SELECT * FROM {$this->table} WHERE category = :category ORDER BY item_name ASC";
            return $this->query($query, ['category' => $category]);
        } catch (Exception $e) {
            error_log("Get store items by category error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get available items (not sold out)
     */
    public function getAvailable() {
        try {
            $this->ensureTableExists();
            $query = "SELECT * FROM {$this->table} WHERE status = 'Available' ORDER BY item_name ASC";
            return $this->query($query);
        } catch (Exception $e) {
            error_log("Get available store items error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get items by status
     */
    public function getByStatus($status) {
        try {
            $this->ensureTableExists();
            $query = "SELECT * FROM {$this->table} WHERE status = :status ORDER BY item_name ASC";
            return $this->query($query, ['status' => $status]);
        } catch (Exception $e) {
            error_log("Get store items by status error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Update item status
     */
    public function updateStatus($item_id, $status) {
        $this->ensureTableExists();

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
        $this->ensureTableExists();

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
