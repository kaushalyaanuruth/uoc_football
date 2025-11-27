<?php

class GalleryManagement extends Controller
{
    private $galleryModel;
    
    public function __construct()
    {
        // Initialize gallery model
        $this->galleryModel = $this->model('GalleryModel');
    }
    
    /**
     * Default index method - shows gallery management page
     */
    public function index()
    {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . ROOT . '/login');
            exit;
        }
        
        // Get all gallery images
        $images = $this->galleryModel->getAll();
        
        // Prepare data for view
        $data = [
            'images' => $images,
            'title' => 'Gallery Management'
        ];
        
        $this->view('galleryManagement', $data);
    }
    
    /**
     * Upload new image
     */
    public function upload()
    {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // Validate file upload
                if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
                    throw new Exception('No file uploaded or upload error occurred');
                }
                
                $file = $_FILES['image'];
                $description = $_POST['description'] ?? '';
                $category = $_POST['category'] ?? '';
                $tags = $_POST['tags'] ?? '';
                
                // Validate file type
                $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
                if (!in_array($file['type'], $allowedTypes)) {
                    throw new Exception('Invalid file type. Only JPG, PNG, and GIF are allowed');
                }
                
                // Validate file size (10MB max)
                $maxSize = 10 * 1024 * 1024;
                if ($file['size'] > $maxSize) {
                    throw new Exception('File size exceeds 10MB limit');
                }
                
                // Generate unique filename
                $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                $filename = 'gallery_' . uniqid() . '.' . $extension;
                $uploadPath = 'uploads/gallery/' . $filename;
                
                // Create directory if it doesn't exist
                $uploadDir = dirname(__DIR__, 2) . '/public/uploads/gallery/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                
                // Move uploaded file
                if (!move_uploaded_file($file['tmp_name'], $uploadDir . $filename)) {
                    throw new Exception('Failed to move uploaded file');
                }
                
                // Save to database
                $imageData = [
                    'filename' => $filename,
                    'filepath' => $uploadPath,
                    'description' => $description,
                    'category' => $category,
                    'tags' => $tags,
                    'uploaded_by' => $_SESSION['user_id'],
                    'status' => 'draft'
                ];
                $this->galleryModel->create($imageData);
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Image uploaded successfully',
                    'filename' => $filename,
                    'filepath' => $uploadPath
                ]);
            } catch (Exception $e) {
                echo json_encode([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
            }
        }
        exit;
    }
    
    /**
     * Upload multiple images at once
     */
    public function uploadMultiple()
    {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // Debug logging
                error_log('Upload attempt started');
                error_log('FILES: ' . print_r($_FILES, true));
                error_log('POST: ' . print_r($_POST, true));
                
                // Validate file uploads
                if (!isset($_FILES['images']) || empty($_FILES['images']['name'])) {
                    throw new Exception('No files uploaded');
                }
                
                $description = $_POST['description'] ?? '';
                $category = $_POST['category'] ?? '';
                $tags = $_POST['tags'] ?? '';
                
                if (empty($category)) {
                    throw new Exception('Category is required');
                }
                
                $files = $_FILES['images'];
                $uploadedCount = 0;
                $errors = [];
                
                // Create directory if it doesn't exist
                $uploadDir = dirname(__DIR__, 2) . '/public/uploads/gallery/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                
                error_log('Upload directory: ' . $uploadDir);
                
                // Process each file
                $fileCount = count($files['name']);
                error_log('Processing ' . $fileCount . ' files');
                
                for ($i = 0; $i < $fileCount; $i++) {
                    error_log('Processing file ' . $i . ': ' . $files['name'][$i]);
                    
                    // Skip if error
                    if ($files['error'][$i] !== UPLOAD_ERR_OK) {
                        $errorMsg = $files['name'][$i] . ': Upload error (code: ' . $files['error'][$i] . ')';
                        $errors[] = $errorMsg;
                        error_log($errorMsg);
                        continue;
                    }
                    
                    // Validate file type
                    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
                    $finfo = finfo_open(FILEINFO_MIME_TYPE);
                    $mimeType = finfo_file($finfo, $files['tmp_name'][$i]);
                    finfo_close($finfo);
                    
                    if (!in_array($mimeType, $allowedTypes)) {
                        $errors[] = $files['name'][$i] . ': Invalid file type';
                        continue;
                    }
                    
                    // Validate file size (10MB max)
                    $maxSize = 10 * 1024 * 1024;
                    if ($files['size'][$i] > $maxSize) {
                        $errors[] = $files['name'][$i] . ': Exceeds 10MB limit';
                        continue;
                    }
                    
                    // Generate unique filename
                    $extension = pathinfo($files['name'][$i], PATHINFO_EXTENSION);
                    $filename = 'gallery_' . uniqid() . '_' . $i . '.' . $extension;
                    $uploadPath = 'uploads/gallery/' . $filename;
                    
                    // Move uploaded file
                    if (!move_uploaded_file($files['tmp_name'][$i], $uploadDir . $filename)) {
                        $errorMsg = $files['name'][$i] . ': Failed to move file';
                        $errors[] = $errorMsg;
                        error_log($errorMsg);
                        continue;
                    }
                    
                    error_log('File moved successfully: ' . $filename);
                    
                    // Save to database
                    $imageData = [
                        'filename' => $filename,
                        'filepath' => $uploadPath,
                        'description' => $description,
                        'category' => $category,
                        'tags' => $tags,
                        'uploaded_by' => $_SESSION['user_id'],
                        'status' => 'published'
                    ];
                    
                    try {
                        $this->galleryModel->create($imageData);
                        $uploadedCount++;
                        error_log('File saved to database: ' . $filename);
                    } catch (Exception $dbError) {
                        $errorMsg = $files['name'][$i] . ': Database error - ' . $dbError->getMessage();
                        $errors[] = $errorMsg;
                        error_log($errorMsg);
                        // Delete the uploaded file since DB save failed
                        if (file_exists($uploadDir . $filename)) {
                            unlink($uploadDir . $filename);
                        }
                    }
                }
                
                error_log('Upload complete. Uploaded: ' . $uploadedCount . ', Failed: ' . count($errors));
                
                // Prepare response
                if ($uploadedCount > 0) {
                    $message = "$uploadedCount image(s) uploaded successfully";
                    if (!empty($errors)) {
                        $message .= ". " . count($errors) . " file(s) failed";
                    }
                    
                    echo json_encode([
                        'success' => true,
                        'message' => $message,
                        'uploaded' => $uploadedCount,
                        'failed' => count($errors),
                        'errors' => $errors
                    ]);
                } else {
                    $errorMessage = 'All uploads failed';
                    if (!empty($errors)) {
                        $errorMessage .= ': ' . implode(', ', $errors);
                    }
                    throw new Exception($errorMessage);
                }
                
            } catch (Exception $e) {
                error_log('Upload exception: ' . $e->getMessage());
                echo json_encode([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
            }
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Invalid request method'
            ]);
        }
        exit;
    }
    
    /**
     * Update image details
     */
    public function update()
    {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // Get JSON input
                $input = json_decode(file_get_contents('php://input'), true);
                $id = $input['id'] ?? 0;
                
                if (empty($id)) {
                    throw new Exception('Image ID is required');
                }
                
                $data = [];
                if (isset($input['description'])) $data['description'] = $input['description'];
                if (isset($input['category'])) $data['category'] = $input['category'];
                if (isset($input['tags'])) $data['tags'] = $input['tags'];
                
                $this->galleryModel->update($id, $data);
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Image updated successfully'
                ]);
            } catch (Exception $e) {
                echo json_encode([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
            }
        }
        exit;
    }
    
    /**
     * Delete image
     */
    public function delete()
    {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // Get JSON input
                $input = json_decode(file_get_contents('php://input'), true);
                $id = $input['id'] ?? 0;
                
                if (empty($id)) {
                    throw new Exception('Image ID is required');
                }
                
                // Get image details
                $image = $this->galleryModel->getById($id);
                
                // Delete file from server
                $fullPath = dirname(__DIR__, 2) . '/public/' . $image->filepath;
                if ($image && file_exists($fullPath)) {
                    unlink($fullPath);
                }
                
                // Delete from database
                $this->galleryModel->delete($id);
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Image deleted successfully'
                ]);
            } catch (Exception $e) {
                echo json_encode([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
            }
        }
        exit;
    }
    
    /**
     * Toggle image status (publish/draft)
     */
    public function toggleStatus()
    {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // Get JSON input
                $input = json_decode(file_get_contents('php://input'), true);
                $id = $input['id'] ?? 0;
                
                if (empty($id)) {
                    throw new Exception('Image ID is required');
                }
                
                $this->galleryModel->toggleStatus($id);
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Status updated successfully'
                ]);
            } catch (Exception $e) {
                echo json_encode([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
            }
        }
        exit;
    }
    
    /**
     * Bulk delete images
     */
    public function bulkDelete()
    {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // Get JSON input
                $input = json_decode(file_get_contents('php://input'), true);
                $ids = $input['ids'] ?? [];
                
                if (empty($ids)) {
                    throw new Exception('No images selected');
                }
                
                foreach ($ids as $id) {
                    // Get image details
                    $image = $this->galleryModel->getById($id);
                    
                    // Delete file from server
                    $fullPath = dirname(__DIR__, 2) . '/public/' . $image->filepath;
                    if ($image && file_exists($fullPath)) {
                        unlink($fullPath);
                    }
                    
                    // Delete from database
                    $this->galleryModel->delete($id);
                }
                
                echo json_encode([
                    'success' => true,
                    'message' => count($ids) . ' images deleted successfully'
                ]);
            } catch (Exception $e) {
                echo json_encode([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
            }
        }
        exit;
    }
    
    /**
     * Filter images
     */
    public function filter()
    {
        header('Content-Type: application/json');
        
        try {
            $category = $_GET['category'] ?? '';
            $dateRange = $_GET['date_range'] ?? '';
            
            $images = $this->galleryModel->filter($category, $dateRange);
            
            echo json_encode([
                'success' => true,
                'images' => $images
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
        exit;
    }
}
