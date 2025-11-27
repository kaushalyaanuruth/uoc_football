<?php

class playerDashboard extends Controller {

    public function index() {
        // Check if user is logged in
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'player') {
            header('Location: ' . ROOT . '/login');
            exit();
        }
        
        // Load player data if needed
        $data = [
            'username' => $_SESSION['username'] ?? 'Player',
            'user_id' => $_SESSION['user_id']
        ];
        
        $this->view('playerDashboard', $data);
    }
}
