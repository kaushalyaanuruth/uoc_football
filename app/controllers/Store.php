<?php

class Store extends Controller {
    private $storeModel;
    private $teamModel;

    public function __construct() {
        parent::__construct();
        $this->storeModel = $this->model('StoreItemModel');
        $this->teamModel = $this->model('Team');
    }

    /**
     * Display all store items for the team
     */
    public function index() {
        $this->requireAuth();
        $team_id = $this->getPresentTeamId();

        $storeItems = $this->storeModel->getByTeamId($team_id);

        // Get categories
        $categories = ['Jersey', 'Merchandise', 'Equipment', 'Accessories', 'Other'];

        $this->view('store', [
            'storeItems' => $storeItems,
            'categories' => $categories
        ]);
    }

    /**
     * Add store item - AJAX endpoint
     */
    public function addStoreItem() {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->respondJson(['success' => false, 'message' => 'Invalid request method']);
        }

        try {
            $team_id = $this->getPresentTeamId();

            $data = [
                'team_id' => $team_id,
                'item_name' => trim($_POST['item_name'] ?? ''),
                'description' => trim($_POST['description'] ?? ''),
                'category' => trim($_POST['category'] ?? ''),
                'price' => (float)($_POST['price'] ?? 0),
                'quantity' => (int)($_POST['quantity'] ?? 0),
                'status' => $_POST['status'] ?? 'Available'
            ];

            // Validate required fields
            if (empty($data['item_name']) || empty($data['category']) || $data['price'] <= 0) {
                throw new Exception('Item name, category, and price are required');
            }

            // Handle image upload
            if (isset($_FILES['item_image']) && $_FILES['item_image']['error'] === UPLOAD_ERR_OK) {
                $uploadedPath = $this->handleImageUpload($_FILES['item_image'], 'store');
                if ($uploadedPath) {
                    $data['item_image'] = $uploadedPath;
                }
            }

            $this->storeModel->create($data);

            $this->respondJson([
                'success' => true,
                'message' => 'Store item added successfully'
            ]);
        } catch (Exception $e) {
            $this->respondJson([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get single store item - AJAX endpoint
     */
    public function getStoreItem() {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            $this->respondJson(['success' => false, 'item' => null]);
        }

        try {
            $item_id = $_GET['item_id'] ?? null;
            if (!$item_id) {
                throw new Exception('Item ID is required');
            }

            $item = $this->storeModel->getById($item_id);

            if (!$item) {
                throw new Exception('Item not found');
            }

            $this->respondJson([
                'success' => true,
                'item' => $item
            ]);
        } catch (Exception $e) {
            $this->respondJson([
                'success' => false,
                'message' => $e->getMessage(),
                'item' => null
            ]);
        }
    }

    /**
     * Edit store item - AJAX endpoint
     */
    public function editStoreItem() {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->respondJson(['success' => false, 'message' => 'Invalid request method']);
        }

        try {
            $item_id = $_POST['item_id'] ?? null;
            if (!$item_id) {
                throw new Exception('Item ID is required');
            }

            $data = [
                'item_name' => trim($_POST['item_name'] ?? ''),
                'description' => trim($_POST['description'] ?? ''),
                'category' => trim($_POST['category'] ?? ''),
                'price' => (float)($_POST['price'] ?? 0),
                'quantity' => (int)($_POST['quantity'] ?? 0),
                'status' => $_POST['status'] ?? 'Available'
            ];

            // Validate required fields
            if (empty($data['item_name']) || empty($data['category']) || $data['price'] <= 0) {
                throw new Exception('Item name, category, and price are required');
            }

            // Handle image upload
            if (isset($_FILES['item_image']) && $_FILES['item_image']['error'] === UPLOAD_ERR_OK) {
                $uploadedPath = $this->handleImageUpload($_FILES['item_image'], 'store');
                if ($uploadedPath) {
                    $data['item_image'] = $uploadedPath;
                }
            }

            $this->storeModel->update($item_id, $data);

            $this->respondJson([
                'success' => true,
                'message' => 'Store item updated successfully'
            ]);
        } catch (Exception $e) {
            $this->respondJson([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Delete store item - AJAX endpoint
     */
    public function deleteStoreItem() {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->respondJson(['success' => false, 'message' => 'Invalid request method']);
        }

        try {
            $item_id = $_POST['item_id'] ?? null;
            if (!$item_id) {
                throw new Exception('Item ID is required');
            }

            $this->storeModel->delete($item_id);

            $this->respondJson([
                'success' => true,
                'message' => 'Store item deleted successfully'
            ]);
        } catch (Exception $e) {
            $this->respondJson([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Update item status (Available/Sold Out)
     */
    public function updateItemStatus() {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->respondJson(['success' => false, 'message' => 'Invalid request method']);
        }

        try {
            $item_id = $_POST['item_id'] ?? null;
            $status = $_POST['status'] ?? null;

            if (!$item_id || !$status) {
                throw new Exception('Item ID and status are required');
            }

            if (!in_array($status, ['Available', 'Sold Out'])) {
                throw new Exception('Invalid status');
            }

            $this->storeModel->updateStatus($item_id, $status);

            $this->respondJson([
                'success' => true,
                'message' => "Item status updated to {$status}"
            ]);
        } catch (Exception $e) {
            $this->respondJson([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle image upload
     */
    private function handleImageUpload($file, $folder) {
        $uploadDir = ROOT . '/assets/images/' . $folder . '/';
        
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $fileName = uniqid() . '_' . basename($file['name']);
        $filePath = $uploadDir . $fileName;

        if (move_uploaded_file($file['tmp_name'], $filePath)) {
            return $folder . '/' . $fileName;
        }

        throw new Exception('Failed to upload image');
    }
}
