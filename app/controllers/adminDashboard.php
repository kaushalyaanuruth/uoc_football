<?php
class adminDashboard extends Controller {

    public function index() {
        $noticeModel = $this->model('NoticeModel');
        $notices = $noticeModel->getForAdmin(8);

        $eventModel = $this->model('EventModel');
        $upcomingEvents = [];

        try {
            // Show nearest upcoming events in the dashboard "What's next" panel.
            $upcomingEvents = $eventModel->getUpcoming(5);
        } catch (Exception $e) {
            error_log('Failed to load upcoming events for admin dashboard: ' . $e->getMessage());
            $upcomingEvents = [];
        }

        $this->view('adminDashboard', [
            'notices' => $notices,
            'upcomingEvents' => $upcomingEvents
        ]);
    }

    public function addNotice()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit;
        }

        $payload = json_decode(file_get_contents('php://input'), true);
        if (!is_array($payload)) {
            echo json_encode(['success' => false, 'message' => 'Invalid payload']);
            exit;
        }

        $title = trim((string)($payload['title'] ?? ''));
        $content = trim((string)($payload['content'] ?? ''));

        if ($title === '' || $content === '') {
            echo json_encode(['success' => false, 'message' => 'Title and content are required']);
            exit;
        }

        try {
            $noticeModel = $this->model('NoticeModel');
            $createdBy = trim((string)($_SESSION['user_id'] ?? 'Admin'));
            if ($createdBy === '') {
                $createdBy = 'Admin';
            }

            $noticeModel->createNotice($title, $content, $createdBy, 'present_team');

            echo json_encode(['success' => true, 'message' => 'Notice added successfully']);
        } catch (Exception $e) {
            error_log('Failed to add notice: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Failed to add notice']);
        }

        exit;
    }
}
