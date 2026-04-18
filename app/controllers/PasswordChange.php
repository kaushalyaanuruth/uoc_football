<?php

class PasswordChange extends Controller
{
    private function requireLogin()
    {
        if (!isset($_SESSION['user_id'], $_SESSION['nic'])) {
            header('Location: ' . ROOT . '/login');
            exit();
        }
    }

    private function redirectByRole()
    {
        $userType = $_SESSION['user_type'] ?? '';
        $playerRole = $_SESSION['player_role'] ?? '';

        if ($userType === 'admin') {
            header('Location: ' . ROOT . '/adminDashboard');
            exit();
        }

        if ($userType === 'coach') {
            header('Location: ' . ROOT . '/coachDashboard');
            exit();
        }

        if ($userType === 'player' && ($playerRole === 'Captain' || $playerRole === 'Vice-Captain')) {
            header('Location: ' . ROOT . '/captainDashboard');
            exit();
        }

        header('Location: ' . ROOT . '/playerDashboard');
        exit();
    }

    public function index()
    {
        $this->requireLogin();

        if (empty($_SESSION['must_change_password'])) {
            $this->redirectByRole();
        }

        $data = [
            'error' => '',
            'success' => ''
        ];

        $this->view('passwordChange', $data);
    }

    public function update()
    {
        $this->requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . ROOT . '/PasswordChange');
            exit();
        }

        $newPassword = (string)($_POST['new_password'] ?? '');
        $confirmPassword = (string)($_POST['confirm_password'] ?? '');

        $data = [
            'error' => '',
            'success' => ''
        ];

        if (strlen($newPassword) < 6) {
            $data['error'] = 'Password must be at least 6 characters.';
            $this->view('passwordChange', $data);
            return;
        }

        if ($newPassword !== $confirmPassword) {
            $data['error'] = 'Passwords do not match.';
            $this->view('passwordChange', $data);
            return;
        }

        if ($newPassword === '123456') {
            $data['error'] = 'Please choose a password different from the default password.';
            $this->view('passwordChange', $data);
            return;
        }

        $userModel = $this->model('User');
        $userModel->updateByNic($_SESSION['nic'], [
            'password' => password_hash($newPassword, PASSWORD_BCRYPT)
        ]);

        $_SESSION['must_change_password'] = false;

        $this->redirectByRole();
    }
}
