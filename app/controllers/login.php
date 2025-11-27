<?php

require_once __DIR__ . '/../models/User.php';

class login extends Controller {

    public function index() {
        $this->view('login');
    }

    public function authenticate() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'];
            $password = $_POST['password'];

            $userModel = new User();
            $userModel->loginUser($username, $password);
        }
    }
    
    public function logout() {
        // Destroy session
        session_start();
        session_unset();
        session_destroy();
        
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'Logged out successfully']);
            exit;
        }
        
        // Otherwise redirect to landing page
        header('Location: ' . ROOT . '/LandingPage');
        exit;
    }

    private function generateUserId() {
        return 'user_' . bin2hex(random_bytes(8));
    }
}
