<?php

class NewsManagement extends Controller
{
    private $newsModel;
    
    public function __construct()
    {
        // Initialize news model
        $this->newsModel = $this->model('NewsModel');
    }
    
    /**
     * Default index method - shows news management page
     */
    public function index()
    {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . ROOT . '/login');
            exit;
        }
        
        // Get all news articles
        $news = $this->newsModel->getAll();
        
        // Prepare data for view
        $data = [
            'news' => $news,
            'title' => 'News Management'
        ];
        
        $this->view('newsManagement', $data);
    }
    
    /**
     * Add new news article
     */
    public function add()
    {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // Get JSON input
                $input = json_decode(file_get_contents('php://input'), true);
                
                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new Exception('Invalid JSON: ' . json_last_error_msg());
                }
                
                // Validate required fields
                if (empty($input['news_heading'])) {
                    throw new Exception('News heading is required');
                }
                
                // Handle image upload if provided
                $imageName = null;
                if (!empty($input['image_data'])) {
                    $imageName = $this->saveBase64Image($input['image_data']);
                    if (!$imageName) {
                        throw new Exception('Failed to save image');
                    }
                }
                
                // Prepare news data
                $newsData = [
                    'news_heading' => $input['news_heading'],
                    'news_body' => $input['news_body'] ?? '',
                    'news_date' => $input['news_date'] ?? date('Y-m-d'),
                    'status' => $input['status'] ?? 'published',
                    'image_name' => $imageName
                ];
                
                // Create news article
                $result = $this->newsModel->create($newsData);
                
                if ($result) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'News article added successfully'
                    ]);
                } else {
                    throw new Exception('Failed to add news article to database');
                }
                
            } catch (Exception $e) {
                error_log("News creation error: " . $e->getMessage());
                error_log("Stack trace: " . $e->getTraceAsString());
                if (isset($newsData)) {
                    error_log("News data: " . json_encode($newsData));
                }
                echo json_encode([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
            }
        }
        exit;
    }
    
    /**
     * Update news article
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
                    throw new Exception('News ID is required');
                }
                
                // Handle image upload if provided
                $imageName = null;
                if (!empty($input['image_data'])) {
                    $imageName = $this->saveBase64Image($input['image_data']);
                    if (!$imageName) {
                        throw new Exception('Failed to save image');
                    }
                } else if (isset($input['existing_image'])) {
                    // Keep existing image if no new image uploaded
                    $imageName = $input['existing_image'];
                }
                
                // Prepare update data
                $data = [];
                if (isset($input['news_heading'])) $data['news_heading'] = $input['news_heading'];
                if (isset($input['news_body'])) $data['news_body'] = $input['news_body'];
                if (isset($input['news_date'])) $data['news_date'] = $input['news_date'];
                if (isset($input['status'])) $data['status'] = $input['status'];
                if ($imageName !== null) $data['image_name'] = $imageName;
                
                // Update news article
                $result = $this->newsModel->update($id, $data);
                
                if ($result) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'News article updated successfully'
                    ]);
                } else {
                    throw new Exception('Failed to update news article');
                }
                
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
     * Delete news article
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
                    throw new Exception('News ID is required');
                }
                
                // Delete news article
                $result = $this->newsModel->delete($id);
                
                if ($result) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'News article deleted successfully'
                    ]);
                } else {
                    throw new Exception('Failed to delete news article');
                }
                
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
     * Get single news article
     */
    public function get()
    {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            try {
                $id = $_GET['id'] ?? 0;
                
                if (empty($id)) {
                    throw new Exception('News ID is required');
                }
                
                $news = $this->newsModel->getById($id);
                
                if ($news) {
                    echo json_encode([
                        'success' => true,
                        'data' => $news
                    ]);
                } else {
                    throw new Exception('News article not found');
                }
                
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
     * Save base64 image to uploads directory
     */
    private function saveBase64Image($base64String)
    {
        try {
            // Remove data URI prefix if present
            if (preg_match('/^data:image\/(\w+);base64,/', $base64String, $matches)) {
                $extension = $matches[1];
                $base64String = substr($base64String, strpos($base64String, ',') + 1);
            } else {
                return false;
            }
            
            // Decode base64
            $imageData = base64_decode($base64String);
            
            if ($imageData === false) {
                return false;
            }
            
            // Generate unique filename
            $filename = 'news_' . time() . '_' . uniqid() . '.' . $extension;
            
            // Define upload directory
            $uploadDir = '../public/uploads/news_images/';
            
            // Create directory if it doesn't exist
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            // Save file
            $filepath = $uploadDir . $filename;
            
            if (file_put_contents($filepath, $imageData)) {
                return $filename;
            }
            
            return false;
            
        } catch (Exception $e) {
            error_log("Image save error: " . $e->getMessage());
            return false;
        }
    }
}
