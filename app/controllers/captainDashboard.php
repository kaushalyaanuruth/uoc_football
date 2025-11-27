<?php

class captainDashboard extends Controller {

    public function index() {
        // Check if user is logged in
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'player') {
            header('Location: ' . ROOT . '/login');
            exit();
        }
        
        // Check if user is captain or vice-captain
        if (!isset($_SESSION['player_role']) || 
            ($_SESSION['player_role'] !== 'captain' && $_SESSION['player_role'] !== 'vice_captain')) {
            // Redirect regular players to player dashboard
            header('Location: ' . ROOT . '/playerDashboard');
            exit();
        }
        
        // Load captain/vice-captain data if needed
        $data = [
            'username' => $_SESSION['username'] ?? 'Captain',
            'user_id' => $_SESSION['user_id'],
            'player_role' => $_SESSION['player_role']
        ];
        
        $this->view('captainDashboard', $data);
    }
}
