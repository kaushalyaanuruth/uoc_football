<?php

class GalleryManagement extends Controller
{
    private $galleryModel;

    /**
     * Initializes the controller with the GalleryModel instance used for all DB operations.
     */
    public function __construct()
    {
        $this->galleryModel = $this->model('GalleryModel');
    }

    /**
     * Ensures only authenticated users can access admin gallery operations.
     * When a request is not authenticated, user is redirected to login.
     */
    private function requireAuth()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . ROOT . '/login');
            exit;
        }
    }

    /**
     * Sends a JSON response and exits to keep API responses consistent.
     */
    private function respondJson(array $payload)
    {
        header('Content-Type: application/json');
        echo json_encode($payload);
        exit;
    }

    /**
     * Renders gallery management UI and passes all current images to the view.
     */
    public function index()
    {
        $this->requireAuth();

        $images = $this->galleryModel->getAll();
        $data = [
            'images' => $images,
            'title' => 'Gallery Management'
        ];

        $this->view('galleryManagement', $data);
    }

    /**
     * Handles multiple image uploads from the gallery management page.
     * It validates category and each image file, stores files in uploads/gallery,
     * then inserts a gallery record for every successfully uploaded image.
     */
    public function uploadMultiple()
    {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->respondJson(['success' => false, 'message' => 'Invalid request method']);
        }

        try {
            if (!isset($_FILES['images']) || empty($_FILES['images']['name'])) {
                throw new Exception('No files uploaded');
            }

            $category = strtolower(trim((string) ($_POST['category'] ?? '')));
            $description = trim((string) ($_POST['description'] ?? ''));
            $tags = trim((string) ($_POST['tags'] ?? ''));

            $allowedCategories = ['practice', 'matches', 'events', 'training', 'team', 'other'];
            if (!in_array($category, $allowedCategories, true)) {
                throw new Exception('Category must be practice, matches, events, training, team, or other');
            }

            $uploadDir = dirname(__DIR__, 2) . '/public/uploads/gallery/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $files = $_FILES['images'];
            $uploadedCount = 0;
            $errors = [];

            $allowedTypes = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif'];
            $maxSize = 10 * 1024 * 1024;

            $fileCount = count($files['name']);
            for ($i = 0; $i < $fileCount; $i++) {
                if ($files['error'][$i] !== UPLOAD_ERR_OK) {
                    $errors[] = $files['name'][$i] . ': upload error';
                    continue;
                }

                if ($files['size'][$i] > $maxSize) {
                    $errors[] = $files['name'][$i] . ': exceeds 10MB limit';
                    continue;
                }

                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mimeType = finfo_file($finfo, $files['tmp_name'][$i]);
                finfo_close($finfo);

                if (!isset($allowedTypes[$mimeType])) {
                    $errors[] = $files['name'][$i] . ': invalid type';
                    continue;
                }

                $extension = $allowedTypes[$mimeType];
                $filename = 'gallery_' . uniqid('', true) . '_' . $i . '.' . $extension;
                $relativePath = 'uploads/gallery/' . $filename;

                if (!move_uploaded_file($files['tmp_name'][$i], $uploadDir . $filename)) {
                    $errors[] = $files['name'][$i] . ': failed to store file';
                    continue;
                }

                $this->galleryModel->create([
                    'filename' => $filename,
                    'filepath' => $relativePath,
                    'description' => $description,
                    'category' => $category,
                    'tags' => $tags,
                    'uploaded_by' => $_SESSION['user_id'],
                    'status' => 'published'
                ]);

                $uploadedCount++;
            }

            if ($uploadedCount === 0) {
                throw new Exception(!empty($errors) ? implode(', ', $errors) : 'All uploads failed');
            }

            $this->respondJson([
                'success' => true,
                'message' => $uploadedCount . ' image(s) uploaded successfully',
                'uploaded' => $uploadedCount,
                'failed' => count($errors),
                'errors' => $errors
            ]);
        } catch (Exception $e) {
            $this->respondJson(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Updates description/category/tags for a gallery image record.
     * This endpoint receives JSON body from gallery management JS edit flow.
     */
    public function update()
    {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->respondJson(['success' => false, 'message' => 'Invalid request method']);
        }

        try {
            $input = json_decode(file_get_contents('php://input'), true);
            if (!is_array($input)) {
                $input = [];
            }

            $id = (int) ($input['id'] ?? 0);
            if ($id <= 0) {
                throw new Exception('Image ID is required');
            }

            $data = [];

            if (isset($input['description'])) {
                $data['description'] = trim((string) $input['description']);
            }

            if (isset($input['category'])) {
                $category = strtolower(trim((string) $input['category']));
                $allowedCategories = ['practice', 'matches', 'events', 'training', 'team', 'other'];
                if (!in_array($category, $allowedCategories, true)) {
                    throw new Exception('Category must be practice, matches, events, training, team, or other');
                }
                $data['category'] = $category;
            }

            if (isset($input['tags'])) {
                $data['tags'] = trim((string) $input['tags']);
            }

            if (empty($data)) {
                throw new Exception('No update data provided');
            }

            $this->galleryModel->update($id, $data);

            $this->respondJson([
                'success' => true,
                'message' => 'Image updated successfully'
            ]);
        } catch (Exception $e) {
            $this->respondJson(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Deletes a gallery image record and removes its file from disk.
     * This endpoint receives JSON body containing the image id.
     */
    public function delete()
    {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->respondJson(['success' => false, 'message' => 'Invalid request method']);
        }

        try {
            $input = json_decode(file_get_contents('php://input'), true);
            if (!is_array($input)) {
                $input = [];
            }

            $id = (int) ($input['id'] ?? 0);
            if ($id <= 0) {
                throw new Exception('Image ID is required');
            }

            $image = $this->galleryModel->getById($id);
            if (!$image) {
                throw new Exception('Image not found');
            }

            $fullPath = dirname(__DIR__, 2) . '/public/' . $image->filepath;
            if (file_exists($fullPath)) {
                unlink($fullPath);
            }

            $this->galleryModel->delete($id);

            $this->respondJson([
                'success' => true,
                'message' => 'Image deleted successfully'
            ]);
        } catch (Exception $e) {
            $this->respondJson(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
