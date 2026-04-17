<?php

class CaptainInventory extends Controller
{
    public function index()
    {
        $inventoryModel = new InventoryModel();

        $items = $inventoryModel->getAllItems();

        $stats = $inventoryModel->getStats();

        $data = [
            'total' => $stats->total,
            'in_use' => $stats->in_use,
            'available' => $stats->available,
            'damaged' => $stats->damaged,
            'inventory' => $items
        ];

        $this->view('captain/CaptainInventory', $data);
    }
    public function store()
    {
        $model = new InventoryModel();

        $data = [
            'item_name' => $_POST['item_name'],
            'category' => $_POST['category'],
            'total_count' => $_POST['quantity'],
            'available_count' => $_POST['quantity'],
            'status' => $_POST['status'],
            'updated_by' => $_SESSION['user_nic'] ?? null
        ];

        $model->addItem($data);

        echo json_encode(["status" => "success"]);
    }
public function update()
{
    $model = new InventoryModel();

    $data = [
        'item_id' => $_POST['item_id'],
        'item_name' => $_POST['item_name'],
        'category' => $_POST['category'],
        'total_count' => $_POST['quantity'],
        'available_count' => $_POST['quantity'],
        'status' => $_POST['status']
    ];

    $model->updateItem($data);

    echo json_encode(["status" => "success"]);
}
public function delete()
{
    $model = new InventoryModel();

    $id = $_POST['item_id'];

    $model->deleteItem($id);

    echo json_encode(["status" => "success"]);
}
public function getChartData()
{
    $model = new InventoryModel();

    $data = $model->getCategoryStats();

    echo json_encode($data);
}
}
