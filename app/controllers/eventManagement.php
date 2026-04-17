<?php

class EventManagement extends Controller
{
    private $eventModel;
    
    public function __construct()
    {
        $this->eventModel = $this->model('EventModel');
    }

    private function getJsonInput()
    {
        $input = json_decode(file_get_contents('php://input'), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Invalid JSON: ' . json_last_error_msg());
        }

        return is_array($input) ? $input : [];
    }

    private function respondJson(array $payload)
    {
        echo json_encode($payload);
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

        $filename = 'event_' . time() . '_' . uniqid() . '.' . $extension;
        $uploadDir = __DIR__ . '/../../public/uploads/event_images/';

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        if (file_put_contents($uploadDir . $filename, $imageData)) {
            return $filename;
        }

        return false;
    }
    
    /**
     * Default index method - shows event management page
     */
    public function index()
    {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . ROOT . '/login');
            exit;
        }
        
        // Get all events
        $events = $this->eventModel->getAll();
        
        // Prepare data for view
        $data = [
            'events' => $events,
            'title' => 'Event Management'
        ];
        
        $this->view('eventManagement', $data);
    }
    
    /**
     * Add new event
     */
    public function add()
    {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $input = $this->getJsonInput();

                if (empty($input['title'])) {
                    throw new Exception('Event title is required');
                }
                
                if (empty($input['event_date'])) {
                    throw new Exception('Event date is required');
                }
                
                try {
                    $datetime = new DateTime($input['event_date']);
                    $eventDate = $datetime->format('Y-m-d');
                    $eventTime = $datetime->format('H:i:s');
                } catch (Exception $e) {
                    throw new Exception('Invalid date format: ' . $e->getMessage());
                }
                
                $eventType = $input['category'] ?? 'match';
                
                $imageName = null;
                if (!empty($input['image_data'])) {
                    $imageName = $this->saveBase64Image($input['image_data']);
                    if (!$imageName) {
                        throw new Exception('Failed to upload image');
                    }
                }
                
                $eventData = [
                    'title' => $input['title'],
                    'description' => $input['description'] ?? '',
                    'date' => $eventDate,
                    'event_time' => $eventTime,
                    'location' => $input['location'] ?? '',
                    'event_type' => $eventType,
                    'image' => $imageName,
                    'status' => $input['status'] ?? 'upcoming'
                ];
                
                $result = $this->eventModel->create($eventData);
                
                if ($result) {
                    $this->respondJson([
                        'success' => true,
                        'message' => 'Event added successfully'
                    ]);
                } else {
                    throw new Exception('Failed to add event to database');
                }
                
            } catch (Exception $e) {
                error_log("Event creation error: " . $e->getMessage());
                error_log("Stack trace: " . $e->getTraceAsString());
                if (isset($eventData)) {
                    error_log("Event data: " . json_encode($eventData));
                }
                $this->respondJson([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
            }
        }
        exit;
    }
    
    /**
     * Update event
     */
    public function update()
    {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $input = $this->getJsonInput();
                
                $id = $input['id'] ?? 0;
                
                if (empty($id)) {
                    throw new Exception('Event ID is required');
                }
                
                $data = [];
                if (isset($input['title'])) $data['title'] = $input['title'];
                if (isset($input['description'])) $data['description'] = $input['description'];
                
                if (isset($input['event_date'])) {
                    $datetime = new DateTime($input['event_date']);
                    $data['date'] = $datetime->format('Y-m-d');
                    $data['event_time'] = $datetime->format('H:i:s');
                }
                
                if (isset($input['location'])) $data['location'] = $input['location'];
                if (isset($input['category'])) $data['event_type'] = $input['category'];
                if (isset($input['status'])) $data['status'] = $input['status'];
                
                // Handle image upload
                if (!empty($input['image_data'])) {
                    $imageName = $this->saveBase64Image($input['image_data']);
                    if (!$imageName) {
                        throw new Exception('Failed to upload image');
                    }
                    $data['image'] = $imageName;
                }
                
                $result = $this->eventModel->update($id, $data);
                
                if ($result) {
                    $this->respondJson([
                        'success' => true,
                        'message' => 'Event updated successfully'
                    ]);
                } else {
                    throw new Exception('Failed to update event');
                }
                
            } catch (Exception $e) {
                $this->respondJson([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
            }
        }
        exit;
    }
    
    /**
     * Delete event
     */
    public function delete()
    {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $input = $this->getJsonInput();
                $id = $input['id'] ?? 0;
                
                if (empty($id)) {
                    throw new Exception('Event ID is required');
                }
                
                $result = $this->eventModel->delete($id);
                
                if ($result) {
                    $this->respondJson([
                        'success' => true,
                        'message' => 'Event deleted successfully'
                    ]);
                } else {
                    throw new Exception('Failed to delete event');
                }
                
            } catch (Exception $e) {
                $this->respondJson([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
            }
        }
        exit;
    }
    
    /**
     * Get single event
     */
    public function get()
    {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            try {
                $id = $_GET['id'] ?? 0;
                
                if (empty($id)) {
                    throw new Exception('Event ID is required');
                }
                
                $event = $this->eventModel->getById($id);
                
                if ($event) {
                    $this->respondJson([
                        'success' => true,
                        'data' => $event
                    ]);
                } else {
                    throw new Exception('Event not found');
                }
                
            } catch (Exception $e) {
                $this->respondJson([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
            }
        }
        exit;
    }
}
