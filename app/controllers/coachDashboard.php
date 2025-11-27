<?php

class coachDashboard extends Controller {

    public function index() {
        // Check if user is logged in
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'coach') {
            header('Location: ' . ROOT . '/login');
            exit();
        }
        
        // Load coach data if needed
        $data = [
            'username' => $_SESSION['username'] ?? 'Coach',
            'user_id' => $_SESSION['user_id']
        ];
        
        $this->view('coachDashboard', $data);
    }
}
