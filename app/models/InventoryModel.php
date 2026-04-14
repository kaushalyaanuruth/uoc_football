<?php

class InventoryModel
{
    use Model;

    protected $table = 'inventory_items';

   public function addItem($data)
{
    $query = "INSERT INTO inventory_items 
    (item_name, category, total_count, available_count, status, updated_by) 
    VALUES (:item_name, :category, :total_count, :available_count, :status, :updated_by)";

    return $this->query($query, $data);
}

public function getAllItems()
{
    $query = "SELECT * FROM inventory_items ORDER BY last_updated DESC";
    return $this->query($query);
}

public function getStats()
{
    $query = "SELECT 
        SUM(total_count) as total,
        SUM(CASE WHEN status='In Use' THEN total_count ELSE 0 END) as in_use,
        SUM(CASE WHEN status='Available' THEN total_count ELSE 0 END) as available,
        SUM(CASE WHEN status='Damaged' THEN total_count ELSE 0 END) as damaged
        FROM inventory_items";

    return $this->query($query)[0];
}
public function updateItem($data)
{
    $query = "UPDATE inventory_items 
              SET item_name = :item_name,
                  category = :category,
                  total_count = :total_count,
                  available_count = :available_count,
                  status = :status
              WHERE item_id = :item_id";

    return $this->query($query, $data);
}
public function deleteItem($id)
{
    $query = "DELETE FROM inventory_items WHERE item_id = :id";
    return $this->query($query, ['id' => $id]);
}
public function getCategoryStats()
{
    $query = "SELECT 
        category,
        SUM(CASE WHEN status='In Use' THEN total_count ELSE 0 END) as in_use,
        SUM(CASE WHEN status='Available' THEN total_count ELSE 0 END) as available,
        SUM(CASE WHEN status='Damaged' THEN total_count ELSE 0 END) as damaged
    FROM inventory_items
    GROUP BY category";

    return $this->query($query);
}
}