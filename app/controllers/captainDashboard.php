<?php

class captainDashboard extends Controller {

    public function index() {
        // Check if user is logged in
        // if (session_status() === PHP_SESSION_NONE) {
        //     session_start();
        // }
        
        // if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'player') {
        //     header('Location: ' . ROOT . '/login');
        //     exit();
        // }
        
        // // Check if user is captain or vice-captain
        // if (!isset($_SESSION['player_role']) || 
        //     ($_SESSION['player_role'] !== 'captain' && $_SESSION['player_role'] !== 'vice_captain')) {
        //     // Redirect regular players to player dashboard
        //     header('Location: ' . ROOT . '/playerDashboard');
        //     exit();
        // }
        
        // Load captain/vice-captain data if needed
        $noticeModel = $this->model('NoticeModel');
        $noticeRows = [];
        try {
            $noticeRows = $noticeModel->getRecent(6, 'present_team');
        } catch (Exception $e) {
            $noticeRows = [];
        }

        $notices = [];
        foreach ($noticeRows as $row) {
            $notices[] = [
                'title' => $row->title ?? 'Notice',
                'content' => $row->content ?? '',
                'author' => $row->created_by ?? 'Admin',
                'date' => !empty($row->created_at) ? date('M d, Y h:i A', strtotime($row->created_at)) : ''
            ];
        }

        if (empty($notices)) {
            $notices[] = [
                'title' => 'No notices yet',
                'content' => 'Admin notices for present team members will appear here.',
                'author' => 'System',
                'date' => ''
            ];
        }

        $data = [
            'username' => $_SESSION['username'] ?? 'Captain',
            'notices' => $notices
        ];
        
        $this->view('captain/captainDashboard', $data);
    }
}
