<?php

require_once __DIR__ . '/CoachBaseController.php';

class coachNotices extends CoachBaseController {

    public function index() {
        $this->ensureCoachAccess();
        $this->view('coachNotices', $this->buildCoachViewData());
    }

    public function addNotice()
    {
        $this->ensureCoachAccess();
        header('Content-Type: application/json');

        if (strtoupper($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit;
        }

        $payload = json_decode(file_get_contents('php://input'), true);
        if (!is_array($payload)) {
            echo json_encode(['success' => false, 'message' => 'Invalid payload']);
            exit;
        }

        $title = trim((string) ($payload['title'] ?? ''));
        $content = trim((string) ($payload['content'] ?? ''));

        if ($title === '' || $content === '') {
            echo json_encode(['success' => false, 'message' => 'Title and content are required']);
            exit;
        }

        try {
            $identity = $this->getCoachIdentity();
            $createdBy = trim((string) ($identity['name'] ?? ''));
            if ($createdBy === '') {
                $createdBy = trim((string) ($_SESSION['user_id'] ?? 'Coach'));
            }
            if ($createdBy === '') {
                $createdBy = 'Coach';
            }

            $noticeModel = $this->model('NoticeModel');
            $noticeModel->createNotice($title, $content, $createdBy, 'present_team');

            echo json_encode(['success' => true, 'message' => 'Notice added successfully']);
        } catch (Throwable $e) {
            error_log('Coach failed to add notice: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Failed to add notice']);
        }

        exit;
    }
}