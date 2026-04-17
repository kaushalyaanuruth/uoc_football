<?php

class InventoryModel
{
    use Model;

    protected $table = 'inventory_items';
    private $columnCache = null;

    private function getColumns()
    {
        if ($this->columnCache !== null) {
            return $this->columnCache;
        }

        $this->columnCache = [];
        $columns = $this->query("SHOW COLUMNS FROM inventory_items");

        foreach ($columns as $column) {
            if (isset($column->Field)) {
                $this->columnCache[$column->Field] = true;
            }
        }

        return $this->columnCache;
    }

    private function hasColumn($name)
    {
        $columns = $this->getColumns();
        return isset($columns[$name]);
    }

    private function statusExpression()
    {
        if ($this->hasColumn('status')) {
            return "status";
        }

        return "CASE
            WHEN (total_count - available_count) > 0 THEN 'In Use'
            ELSE 'Available'
        END";
    }

    private function categoryExpression()
    {
        if ($this->hasColumn('category')) {
            return "category";
        }

        return "'General'";
    }

    public function addItem($data)
    {
        $columns = ['item_name', 'total_count', 'available_count', 'updated_by'];
        $values = [':item_name', ':total_count', ':available_count', ':updated_by'];
        $params = [
            'item_name' => $data['item_name'],
            'total_count' => $data['total_count'],
            'available_count' => $data['available_count'],
            'updated_by' => $data['updated_by']
        ];

        if ($this->hasColumn('category')) {
            $columns[] = 'category';
            $values[] = ':category';
            $params['category'] = $data['category'] ?? 'General';
        }

        if ($this->hasColumn('status')) {
            $columns[] = 'status';
            $values[] = ':status';
            $params['status'] = $data['status'] ?? 'Available';
        }

        $query = "INSERT INTO inventory_items (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $values) . ")";

        return $this->query($query, $params);
    }

    public function getAllItems()
    {
        $categoryExpr = $this->categoryExpression();
        $statusExpr = $this->statusExpression();

        $query = "SELECT
            item_id,
            item_name,
            {$categoryExpr} AS category,
            total_count,
            available_count,
            {$statusExpr} AS status,
            last_updated,
            updated_by
            FROM inventory_items
            ORDER BY last_updated DESC";

        return $this->query($query);
    }

    public function getStats()
    {
        if ($this->hasColumn('status')) {
            $query = "SELECT
                COALESCE(SUM(total_count), 0) as total,
                COALESCE(SUM(CASE WHEN status='In Use' THEN total_count ELSE 0 END), 0) as in_use,
                COALESCE(SUM(CASE WHEN status='Available' THEN total_count ELSE 0 END), 0) as available,
                COALESCE(SUM(CASE WHEN status='Damaged' THEN total_count ELSE 0 END), 0) as damaged
                FROM inventory_items";
        } else {
            $query = "SELECT
                COALESCE(SUM(total_count), 0) as total,
                COALESCE(SUM(total_count - available_count), 0) as in_use,
                COALESCE(SUM(available_count), 0) as available,
                0 as damaged
                FROM inventory_items";
        }

        $rows = $this->query($query);
        return $rows[0] ?? (object) ['total' => 0, 'in_use' => 0, 'available' => 0, 'damaged' => 0];
    }

    public function updateItem($data)
    {
        $set = [
            'item_name = :item_name',
            'total_count = :total_count',
            'available_count = :available_count'
        ];

        $params = [
            'item_id' => $data['item_id'],
            'item_name' => $data['item_name'],
            'total_count' => $data['total_count'],
            'available_count' => $data['available_count']
        ];

        if ($this->hasColumn('category')) {
            $set[] = 'category = :category';
            $params['category'] = $data['category'] ?? 'General';
        }

        if ($this->hasColumn('status')) {
            $set[] = 'status = :status';
            $params['status'] = $data['status'] ?? 'Available';
        }

        $query = "UPDATE inventory_items SET " . implode(', ', $set) . " WHERE item_id = :item_id";

        return $this->query($query, $params);
    }

    public function deleteItem($id)
    {
        $query = "DELETE FROM inventory_items WHERE item_id = :id";
        return $this->query($query, ['id' => $id]);
    }

    public function getCategoryStats()
    {
        $categoryExpr = $this->categoryExpression();

        if ($this->hasColumn('status')) {
            $query = "SELECT
                {$categoryExpr} AS category,
                COALESCE(SUM(CASE WHEN status='In Use' THEN total_count ELSE 0 END), 0) as in_use,
                COALESCE(SUM(CASE WHEN status='Available' THEN total_count ELSE 0 END), 0) as available,
                COALESCE(SUM(CASE WHEN status='Damaged' THEN total_count ELSE 0 END), 0) as damaged
            FROM inventory_items
            GROUP BY {$categoryExpr}";
        } else {
            $query = "SELECT
                {$categoryExpr} AS category,
                COALESCE(SUM(total_count - available_count), 0) as in_use,
                COALESCE(SUM(available_count), 0) as available,
                0 as damaged
            FROM inventory_items
            GROUP BY {$categoryExpr}";
        }

        return $this->query($query);
    }
}