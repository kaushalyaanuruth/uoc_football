<?php

class Notices extends Controller
{
    private function normalizeImageUrl($imagePath)
    {
        if (empty($imagePath)) {
            return ROOT . '/assets/images/adminDashboard/header/avatar.jpg';
        }

        $normalized = str_replace('\\', '/', ltrim((string)$imagePath, '/'));
        return ROOT . '/' . $normalized;
    }

    private function getPlayerImageBySession()
    {
        $nic = $_SESSION['nic'] ?? '';
        if ($nic === '') {
            return $this->normalizeImageUrl('');
        }

        $playerModel = $this->model('PlayerModel');
        $rows = $playerModel->query(
            "SELECT u.image FROM users u WHERE u.nic = :nic LIMIT 1",
            ['nic' => $nic]
        );

        return $this->normalizeImageUrl($rows[0]->image ?? '');
    }

    public function index()
    {
        if (!isset($_SESSION['user_id'], $_SESSION['nic'])) {
            header('Location: ' . ROOT . '/login');
            exit();
        }

        if (($_SESSION['user_type'] ?? '') !== 'player') {
            header('Location: ' . ROOT . '/login');
            exit();
        }

        $noticeModel = $this->model('NoticeModel');
        $rows = $noticeModel->getRecent(50, 'present_team');

        $notices = [];
        foreach ($rows as $index => $row) {
            $notices[] = [
                'id' => (int)($row->notice_id ?? 0),
                'author' => $row->created_by ?? 'Admin',
                'date' => !empty($row->created_at) ? date('Y-m-d h:i A', strtotime($row->created_at)) : '',
                'title' => $row->title ?? 'Notice',
                'content' => $row->content ?? '',
                'is_new' => $index < 3
            ];
        }

        $data = [
            'notices' => $notices,
            'player_image' => $this->getPlayerImageBySession()
        ];

        $this->view('notices', $data);
    }
}
