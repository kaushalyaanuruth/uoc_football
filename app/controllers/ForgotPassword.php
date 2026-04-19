<?php

class ForgotPassword extends Controller
{
    private function genericRequestMessage()
    {
        return 'If an account with that email exists, a password reset link has been sent.';
    }

    private function sendResetEmail($toEmail, $resetLink)
    {
        $subject = 'UOC Football Password Reset';
        $body = "Hello,\n\n" .
            "We received a request to reset your password.\n" .
            "Use this link to set a new password:\n\n" .
            $resetLink . "\n\n" .
            "This link expires in 45 minutes. If you did not request this, you can ignore this message.\n";

        $headers = [
            'MIME-Version: 1.0',
            'Content-Type: text/plain; charset=UTF-8',
            'From: no-reply@uocfootball.local'
        ];

        $sent = @mail($toEmail, $subject, $body, implode("\r\n", $headers));
        if (!$sent) {
            error_log('Password reset email failed to send to: ' . $toEmail);
            error_log('Reset link fallback: ' . $resetLink);
        }

        return $sent;
    }

    public function index()
    {
        $this->view('forgotPassword', [
            'error' => '',
            'success' => ''
        ]);
    }

    public function request()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . ROOT . '/ForgotPassword');
            exit();
        }

        $email = trim((string)($_POST['email'] ?? ''));
        $successMessage = $this->genericRequestMessage();

        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->view('forgotPassword', [
                'error' => 'Please enter a valid email address.',
                'success' => ''
            ]);
            return;
        }

        try {
            $userModel = $this->model('User');
            $rows = $userModel->getUserByEmail($email);

            if (!empty($rows) && isset($rows[0]->nic)) {
                $rawToken = bin2hex(random_bytes(32));
                $tokenHash = hash('sha256', $rawToken);
                $expiresAt = date('Y-m-d H:i:s', time() + (45 * 60));

                $userModel->createPasswordResetToken((string) $rows[0]->nic, $tokenHash, $expiresAt);

                $resetLink = ROOT . '/ForgotPassword/reset/' . $rawToken;
                $this->sendResetEmail($email, $resetLink);
            }

            $this->view('forgotPassword', [
                'error' => '',
                'success' => $successMessage
            ]);
        } catch (Exception $e) {
            error_log('Forgot password request failed: ' . $e->getMessage());
            $this->view('forgotPassword', [
                'error' => 'Unable to process request right now. Please try again.',
                'success' => ''
            ]);
        }
    }

    public function reset($token = '')
    {
        $token = trim((string) $token);
        if ($token === '') {
            $token = trim((string) ($_GET['token'] ?? ''));
        }

        if ($token === '') {
            $this->view('resetPassword', [
                'error' => 'Invalid or missing reset token.',
                'success' => '',
                'token' => ''
            ]);
            return;
        }

        $tokenHash = hash('sha256', $token);

        try {
            $userModel = $this->model('User');
            $tokenRow = $userModel->findValidPasswordResetToken($tokenHash);
            if ($tokenRow === null) {
                $this->view('resetPassword', [
                    'error' => 'This reset link is invalid or has expired.',
                    'success' => '',
                    'token' => ''
                ]);
                return;
            }

            $this->view('resetPassword', [
                'error' => '',
                'success' => '',
                'token' => $token
            ]);
        } catch (Exception $e) {
            error_log('Forgot password reset page load failed: ' . $e->getMessage());
            $this->view('resetPassword', [
                'error' => 'Unable to validate reset link right now.',
                'success' => '',
                'token' => ''
            ]);
        }
    }

    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . ROOT . '/ForgotPassword');
            exit();
        }

        $token = trim((string)($_POST['token'] ?? ''));
        $newPassword = (string)($_POST['new_password'] ?? '');
        $confirmPassword = (string)($_POST['confirm_password'] ?? '');

        if ($token === '') {
            $this->view('resetPassword', [
                'error' => 'Invalid or missing reset token.',
                'success' => '',
                'token' => ''
            ]);
            return;
        }

        if (strlen($newPassword) < 6) {
            $this->view('resetPassword', [
                'error' => 'Password must be at least 6 characters.',
                'success' => '',
                'token' => $token
            ]);
            return;
        }

        if ($newPassword !== $confirmPassword) {
            $this->view('resetPassword', [
                'error' => 'Passwords do not match.',
                'success' => '',
                'token' => $token
            ]);
            return;
        }

        if ($newPassword === '123456') {
            $this->view('resetPassword', [
                'error' => 'Please choose a password different from the default password.',
                'success' => '',
                'token' => $token
            ]);
            return;
        }

        try {
            $tokenHash = hash('sha256', $token);
            $userModel = $this->model('User');
            $tokenRow = $userModel->findValidPasswordResetToken($tokenHash);

            if ($tokenRow === null || empty($tokenRow->nic)) {
                $this->view('resetPassword', [
                    'error' => 'This reset link is invalid or has expired.',
                    'success' => '',
                    'token' => ''
                ]);
                return;
            }

            $userModel->updatePasswordByNic((string) $tokenRow->nic, $newPassword);
            $userModel->markPasswordResetTokenUsed((int) $tokenRow->token_id);
            $userModel->clearPasswordResetTokensByNic((string) $tokenRow->nic);

            header('Location: ' . ROOT . '/login?reset=success');
            exit();
        } catch (Exception $e) {
            error_log('Forgot password update failed: ' . $e->getMessage());
            $this->view('resetPassword', [
                'error' => 'Unable to reset password right now. Please try again.',
                'success' => '',
                'token' => $token
            ]);
        }
    }
}
