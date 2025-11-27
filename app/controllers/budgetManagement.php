<?php

class BudgetManagement extends Controller
{
    public function index()
    {
        // Check if user is logged in and is admin
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
            redirect('login');
            return;
        }

        $data['username'] = $_SESSION['username'] ?? 'Admin';
        $data['user_id'] = $_SESSION['user_id'];

        $this->view('budgetManagement', $data);
    }
}
