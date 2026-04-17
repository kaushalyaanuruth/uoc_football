<?php

class Logout extends Controller {

    public function index() {
        // Destroy session
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        session_unset();
        session_destroy();
        
        // Check if JSON request (AJAX)
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'Logged out successfully']);
            exit;
        }
        
        // Otherwise redirect to login page
        header('Location: ' . ROOT . '/login');
        exit;
    }
}
