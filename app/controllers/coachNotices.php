<?php

require_once __DIR__ . '/CoachBaseController.php';

class coachNotices extends CoachBaseController {

    private function respondJson($payload)
    {
        header('Content-Type: application/json');
        echo json_encode($payload);
        exit;
    }

    public function index() {
        $this->ensureCoachAccess();

        $viewData = $this->buildCoachViewData();
        $noticeModel = $this->model('NoticeModel');
        $rows = $noticeModel->getRecent(50, 'present_team');

        $notices = [];
        foreach ($rows as $row) {
            $notices[] = [
                'id' => (int) ($row->notice_id ?? 0),
                'title' => $row->title ?? 'Notice',
                'content' => $row->content ?? '',
                'author' => $row->created_by ?? 'Admin',
                'date' => !empty($row->created_at) ? date('M d, Y h:i A', strtotime($row->created_at)) : ''
            ];
        }

        $viewData['notices'] = $notices;
        $this->view('coachNotices', $viewData);
    }

    public function addNotice()
    {
        $this->ensureCoachAccess();

        if (strtoupper($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            $this->respondJson(['success' => false, 'message' => 'Invalid request method']);
        }

        $payload = json_decode(file_get_contents('php://input'), true);
        if (!is_array($payload)) {
            $this->respondJson(['success' => false, 'message' => 'Invalid payload']);
        }

        $title = trim((string) ($payload['title'] ?? ''));
        $content = trim((string) ($payload['content'] ?? ''));

        if ($title === '' || $content === '') {
            $this->respondJson(['success' => false, 'message' => 'Title and content are required']);
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

            $this->respondJson(['success' => true, 'message' => 'Notice added successfully']);
        } catch (Throwable $e) {
            error_log('Coach failed to add notice: ' . $e->getMessage());
            $this->respondJson(['success' => false, 'message' => 'Failed to add notice']);
        }
    }

    public function updateNotice()
    {
        $this->ensureCoachAccess();

        if (strtoupper($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            $this->respondJson(['success' => false, 'message' => 'Invalid request method']);
        }

        $payload = json_decode(file_get_contents('php://input'), true);
        if (!is_array($payload)) {
            $this->respondJson(['success' => false, 'message' => 'Invalid payload']);
        }

        $noticeId = (int) ($payload['notice_id'] ?? 0);
        $title = trim((string) ($payload['title'] ?? ''));
        $content = trim((string) ($payload['content'] ?? ''));

        if ($noticeId <= 0 || $title === '' || $content === '') {
            $this->respondJson(['success' => false, 'message' => 'Notice ID, title and content are required']);
        }

        try {
            $noticeModel = $this->model('NoticeModel');
            $existing = $noticeModel->getById($noticeId);
            if ($existing === null) {
                $this->respondJson(['success' => false, 'message' => 'Notice not found']);
            }

            $noticeModel->updateNotice($noticeId, $title, $content);
            $this->respondJson(['success' => true, 'message' => 'Notice updated successfully']);
        } catch (Throwable $e) {
            error_log('Coach failed to update notice: ' . $e->getMessage());
            $this->respondJson(['success' => false, 'message' => 'Failed to update notice']);
        }
    }

    public function deleteNotice()
    {
        $this->ensureCoachAccess();

        if (strtoupper($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            $this->respondJson(['success' => false, 'message' => 'Invalid request method']);
        }

        $payload = json_decode(file_get_contents('php://input'), true);
        if (!is_array($payload)) {
            $this->respondJson(['success' => false, 'message' => 'Invalid payload']);
        }

        $noticeId = (int) ($payload['notice_id'] ?? 0);
        if ($noticeId <= 0) {
            $this->respondJson(['success' => false, 'message' => 'Notice ID is required']);
        }

        try {
            $noticeModel = $this->model('NoticeModel');
            $existing = $noticeModel->getById($noticeId);
            if ($existing === null) {
                $this->respondJson(['success' => false, 'message' => 'Notice not found']);
            }

            $noticeModel->deleteNotice($noticeId);
            $this->respondJson(['success' => true, 'message' => 'Notice deleted successfully']);
        } catch (Throwable $e) {
            error_log('Coach failed to delete notice: ' . $e->getMessage());
            $this->respondJson(['success' => false, 'message' => 'Failed to delete notice']);
        }
    }
}