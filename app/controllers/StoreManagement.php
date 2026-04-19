<?php

class StoreManagement extends Controller {
    private $storeModel;

    public function __construct() {
        $this->storeModel = $this->model('StoreManagementModel');
    }

    /**
     * Ensure user is authenticated
     */
    private function requireAuth() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . ROOT . '/login');
            exit;
        }
    }

    /**
     * Send JSON response
     */
    private function respondJson(array $payload) {
        if (ob_get_length()) {
            ob_clean();
        }
        header('Content-Type: application/json');
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');
        echo json_encode($payload);
        exit;
    }

    /**
     * Handle image upload
     */
    private function handleImageUpload($file, $folder) {
        if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
            error_log("Image upload error: File error code " . ($file['error'] ?? 'no file'));
            return '';
        }
        
        try {
            // The script runs from public/index.php, so uploadDir is relative to public/
            $uploadDir = 'uploads/' . $folder . '/';
            
            error_log("Attempting upload to: " . $uploadDir);
            
            // Create directory if it doesn't exist
            if (!file_exists($uploadDir)) {
                if (!@mkdir($uploadDir, 0777, true)) {
                    error_log("Failed to create directory: " . $uploadDir);
                    return '';
                }
                error_log("Created directory: " . $uploadDir);
            }
            
            $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $fileName = uniqid($folder . '_') . '.' . $fileExtension;
            $uploadPath = $uploadDir . $fileName;
            
            error_log("File info - Name: " . $file['name'] . ", Size: " . $file['size'] . ", Tmp: " . $file['tmp_name']);
            error_log("Target path: " . $uploadPath);
            
            if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
                error_log("File uploaded successfully: " . $uploadPath);
                // Return path relative to ROOT (which is public/)
                return 'uploads/' . $folder . '/' . $fileName;
            } else {
                error_log("move_uploaded_file failed for: " . $uploadPath);
            }
        } catch (Exception $e) {
            error_log("Image upload exception: " . $e->getMessage());
        }
        
        return '';
    }

    /**
     * Display all store items
     */
    public function index() {
        $this->requireAuth();

        $items = $this->storeModel->getAll();
        $categories = ['Jersey', 'Merchandise', 'Equipment', 'Accessories', 'Other'];

        $this->view('storeManagement', [
            'items' => $items,
            'categories' => $categories
        ]);
    }

    /**
     * Add store item - AJAX endpoint
     */
    public function addItem() {
        $this->requireAuth();

        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->respondJson(['success' => false, 'message' => 'Invalid request method']);
        }

        try {
            $data = [
                'item_name' => trim($_POST['item_name'] ?? ''),
                'description' => trim($_POST['description'] ?? ''),
                'category' => trim($_POST['category'] ?? ''),
                'price' => (float)($_POST['price'] ?? 0),
                'quantity' => (int)($_POST['quantity'] ?? 0),
                'status' => $_POST['status'] ?? 'Available',
                'item_image' => ''
            ];

            // Validate required fields
            if (empty($data['item_name'])) {
                $this->respondJson(['success' => false, 'message' => 'Item name is required']);
            }
            if (empty($data['category'])) {
                $this->respondJson(['success' => false, 'message' => 'Category is required']);
            }
            if ($data['price'] <= 0) {
                $this->respondJson(['success' => false, 'message' => 'Price must be greater than 0']);
            }

            // Handle image upload
            if (isset($_FILES['item_image']) && $_FILES['item_image']['error'] === UPLOAD_ERR_OK) {
                $uploadedPath = $this->handleImageUpload($_FILES['item_image'], 'store');
                if ($uploadedPath) {
                    $data['item_image'] = $uploadedPath;
                }
            }

            $result = $this->storeModel->create($data);
            
            if (!$result) {
                $this->respondJson(['success' => false, 'message' => 'Failed to add item to database']);
            }

            $this->respondJson([
                'success' => true,
                'message' => 'Store item added successfully'
            ]);
        } catch (Exception $e) {
            error_log("Add item error: " . $e->getMessage());
            $this->respondJson([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get single store item - AJAX endpoint
     */
    public function getItem() {
        $this->requireAuth();

        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            $this->respondJson(['success' => false, 'item' => null, 'message' => 'Invalid request method']);
        }

        try {
            $item_id = $_GET['item_id'] ?? null;
            if (!$item_id) {
                $this->respondJson(['success' => false, 'item' => null, 'message' => 'Item ID is required']);
            }

            $item = $this->storeModel->getById($item_id);

            if (!$item) {
                $this->respondJson(['success' => false, 'item' => null, 'message' => 'Item not found']);
            }

            $this->respondJson([
                'success' => true,
                'item' => $item
            ]);
        } catch (Exception $e) {
            error_log("Get item error: " . $e->getMessage());
            $this->respondJson([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
                'item' => null
            ]);
        }
    }

    /**
     * Edit store item - AJAX endpoint
     */
    public function editItem() {
        $this->requireAuth();

        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->respondJson(['success' => false, 'message' => 'Invalid request method']);
        }

        try {
            $item_id = $_POST['item_id'] ?? null;
            if (!$item_id) {
                $this->respondJson(['success' => false, 'message' => 'Item ID is required']);
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
            if (empty($data['item_name'])) {
                $this->respondJson(['success' => false, 'message' => 'Item name is required']);
            }
            if (empty($data['category'])) {
                $this->respondJson(['success' => false, 'message' => 'Category is required']);
            }
            if ($data['price'] <= 0) {
                $this->respondJson(['success' => false, 'message' => 'Price must be greater than 0']);
            }

            // Handle image upload
            if (isset($_FILES['item_image']) && $_FILES['item_image']['error'] === UPLOAD_ERR_OK) {
                $uploadedPath = $this->handleImageUpload($_FILES['item_image'], 'store');
                if ($uploadedPath) {
                    $data['item_image'] = $uploadedPath;
                }
            }

            $result = $this->storeModel->update($item_id, $data);
            
            if (!$result) {
                $this->respondJson(['success' => false, 'message' => 'Failed to update item']);
            }

            $this->respondJson([
                'success' => true,
                'message' => 'Store item updated successfully'
            ]);
        } catch (Exception $e) {
            error_log("Edit item error: " . $e->getMessage());
            $this->respondJson([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Delete store item - AJAX endpoint
     */
    public function deleteItem() {
        $this->requireAuth();

        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->respondJson(['success' => false, 'message' => 'Invalid request method']);
        }

        try {
            $item_id = $_POST['item_id'] ?? null;
            if (!$item_id) {
                $this->respondJson(['success' => false, 'message' => 'Item ID is required']);
            }

            $result = $this->storeModel->delete($item_id);
            
            if (!$result) {
                $this->respondJson(['success' => false, 'message' => 'Failed to delete item']);
            }

            $this->respondJson([
                'success' => true,
                'message' => 'Store item deleted successfully'
            ]);
        } catch (Exception $e) {
            error_log("Delete item error: " . $e->getMessage());
            $this->respondJson([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Update item status (Available/Sold Out)
     */
    public function updateStatus() {
        $this->requireAuth();

        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->respondJson(['success' => false, 'message' => 'Invalid request method']);
        }

        try {
            $item_id = $_POST['item_id'] ?? null;
            $status = $_POST['status'] ?? null;

            if (!$item_id || !$status) {
                $this->respondJson(['success' => false, 'message' => 'Item ID and status are required']);
            }

            if (!in_array($status, ['Available', 'Sold Out'])) {
                $this->respondJson(['success' => false, 'message' => 'Invalid status']);
            }

            $result = $this->storeModel->updateStatus($item_id, $status);
            
            if (!$result) {
                $this->respondJson(['success' => false, 'message' => 'Failed to update item status']);
            }

            $this->respondJson([
                'success' => true,
                'message' => "Item status updated to {$status}"
            ]);
        } catch (Exception $e) {
            error_log("Update status error: " . $e->getMessage());
            $this->respondJson([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }
}
