<?php

class EventManagement extends Controller
{
    private $eventModel;
    
    public function __construct()
    {
        // Initialize event model
        $this->eventModel = $this->model('EventModel');
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
                // Get JSON input
                $input = json_decode(file_get_contents('php://input'), true);
                
                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new Exception('Invalid JSON: ' . json_last_error_msg());
                }
                
                // Validate required fields
                if (empty($input['title'])) {
                    throw new Exception('Event title is required');
                }
                
                if (empty($input['event_date'])) {
                    throw new Exception('Event date is required');
                }
                
                // Split datetime into date and time
                try {
                    $datetime = new DateTime($input['event_date']);
                    $eventDate = $datetime->format('Y-m-d');
                    $eventTime = $datetime->format('H:i:s');
                } catch (Exception $e) {
                    throw new Exception('Invalid date format: ' . $e->getMessage());
                }
                
                // Map category to event_type (match, training, meeting, etc.)
                $eventType = $input['category'] ?? 'match';
                
                // Prepare event data matching database schema
                $eventData = [
                    'title' => $input['title'],
                    'description' => $input['description'] ?? '',
                    'event_date' => $eventDate,
                    'event_time' => $eventTime,
                    'location' => $input['location'] ?? '',
                    'event_type' => $eventType,
                    'event_category' => 'general', // Default category
                    'status' => $input['status'] ?? 'upcoming'
                ];
                
                // Create event
                $result = $this->eventModel->create($eventData);
                
                if ($result) {
                    echo json_encode([
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
                echo json_encode([
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
                // Get JSON input
                $input = json_decode(file_get_contents('php://input'), true);
                
                $id = $input['id'] ?? 0;
                
                if (empty($id)) {
                    throw new Exception('Event ID is required');
                }
                
                // Prepare update data
                $data = [];
                if (isset($input['title'])) $data['title'] = $input['title'];
                if (isset($input['description'])) $data['description'] = $input['description'];
                
                // Handle datetime splitting
                if (isset($input['event_date'])) {
                    $datetime = new DateTime($input['event_date']);
                    $data['event_date'] = $datetime->format('Y-m-d');
                    $data['event_time'] = $datetime->format('H:i:s');
                }
                
                if (isset($input['location'])) $data['location'] = $input['location'];
                if (isset($input['category'])) $data['event_type'] = $input['category'];
                if (isset($input['status'])) $data['status'] = $input['status'];
                
                // Update event
                $result = $this->eventModel->update($id, $data);
                
                if ($result) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Event updated successfully'
                    ]);
                } else {
                    throw new Exception('Failed to update event');
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
     * Delete event
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
                    throw new Exception('Event ID is required');
                }
                
                // Delete event
                $result = $this->eventModel->delete($id);
                
                if ($result) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Event deleted successfully'
                    ]);
                } else {
                    throw new Exception('Failed to delete event');
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
                    echo json_encode([
                        'success' => true,
                        'data' => $event
                    ]);
                } else {
                    throw new Exception('Event not found');
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
}
