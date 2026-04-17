<?php

class NewsManagement extends Controller
{
    private $newsModel;
    
    public function __construct()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $this->newsModel = $this->model('NewsModel');
    }
    
    private function jsonInput()
    {
        $input = json_decode(file_get_contents('php://input'), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Invalid JSON: ' . json_last_error_msg());
        }

        return is_array($input) ? $input : [];
    }

    private function respond(array $payload)
    {
        header('Content-Type: application/json');
        echo json_encode($payload);
        exit;
    }

    private function saveBase64Image($base64String)
    {
        if (!preg_match('/^data:image\/(\w+);base64,/', $base64String, $matches)) {
            return false;
        }

        $extension = $matches[1];
        $base64String = substr($base64String, strpos($base64String, ',') + 1);
        $imageData = base64_decode($base64String);

        if ($imageData === false) {
            return false;
        }

        $filename = 'news_' . time() . '_' . uniqid() . '.' . $extension;
        $uploadDir = __DIR__ . '/../../public/uploads/news_images/';

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        if (file_put_contents($uploadDir . $filename, $imageData)) {
            return $filename;
        }

        return false;
    }

    public function index()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . ROOT . '/login');
            exit;
        }

        $news = $this->newsModel->getAll();

        $data = [
            'news' => $news,
            'title' => 'News Management'
        ];
        
        $this->view('newsManagement', $data);
    }

    public function add()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->respond(['success' => false, 'message' => 'Invalid request method']);
        }

        try {
            $input = $this->jsonInput();

            if (empty(trim($input['title'] ?? ''))) {
                throw new Exception('Title is required');
            }

            if (empty(trim($input['discription'] ?? ''))) {
                throw new Exception('Description is required');
            }

            $imageName = null;
            if (!empty($input['image_data'])) {
                $imageName = $this->saveBase64Image($input['image_data']);
                if (!$imageName) {
                    throw new Exception('Failed to save image');
                }
            }

            $newsData = [
                'title' => trim($input['title'] ?? ''),
                'date' => $input['date'] ?? date('Y-m-d'),
                'discription' => trim($input['discription'] ?? ''),
                'image' => $imageName
            ];

            $result = $this->newsModel->create($newsData);

            if (!$result) {
                throw new Exception('Failed to add news article');
            }

            $this->respond([
                'success' => true,
                'message' => 'News article added successfully'
            ]);
        } catch (Exception $e) {
            $this->respond([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->respond(['success' => false, 'message' => 'Invalid request method']);
        }

        try {
            $input = $this->jsonInput();
            $id = (int) ($input['id'] ?? 0);

            if (!$id) {
                throw new Exception('News ID is required');
            }

            $imageName = null;
            if (!empty($input['image_data'])) {
                $imageName = $this->saveBase64Image($input['image_data']);
                if (!$imageName) {
                    throw new Exception('Failed to save image');
                }
            } elseif (isset($input['existing_image'])) {
                $imageName = $input['existing_image'];
            }

            $data = [
                'title' => trim($input['title'] ?? ''),
                'date' => $input['date'] ?? null,
                'discription' => trim($input['discription'] ?? '')
            ];

            if ($imageName !== null) {
                $data['image'] = $imageName;
            }

            if (!$this->newsModel->update($id, $data)) {
                throw new Exception('Failed to update news article');
            }

            $this->respond([
                'success' => true,
                'message' => 'News article updated successfully'
            ]);
        } catch (Exception $e) {
            $this->respond([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function delete()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->respond(['success' => false, 'message' => 'Invalid request method']);
        }

        try {
            $input = $this->jsonInput();
            $id = (int) ($input['id'] ?? 0);

            if (!$id) {
                throw new Exception('News ID is required');
            }

            if (!$this->newsModel->delete($id)) {
                throw new Exception('Failed to delete news article');
            }

            $this->respond([
                'success' => true,
                'message' => 'News article deleted successfully'
            ]);
        } catch (Exception $e) {
            $this->respond([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function get()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            $this->respond(['success' => false, 'message' => 'Invalid request method']);
        }

        try {
            $id = (int) ($_GET['id'] ?? 0);

            if (!$id) {
                throw new Exception('News ID is required');
            }

            $news = $this->newsModel->getById($id);

            if (!$news) {
                throw new Exception('News article not found');
            }

            $this->respond([
                'success' => true,
                'data' => $news
            ]);
        } catch (Exception $e) {
            $this->respond([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}
