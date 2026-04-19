<?php

class CaptainAnalyze extends Controller
{
    private function normalizeImageUrl($imagePath)
    {
        if (empty($imagePath)) {
            return ROOT . '/assets/images/adminDashboard/header/avatar.jpg';
        }

        $normalized = str_replace('\\', '/', ltrim((string) $imagePath, '/'));
        return ROOT . '/' . $normalized;
    }

    private function isCaptainLikeRole($role)
    {
        $normalized = strtolower(trim((string) $role));
        $normalized = str_replace(['_', ' '], '-', $normalized);

        return in_array($normalized, ['captain', 'vice-captain', 'vicecaptain'], true);
    }

    private function ensureCaptainAccess()
    {
        if (!isset($_SESSION['user_id'], $_SESSION['nic'])) {
            header('Location: ' . ROOT . '/login');
            exit();
        }

        $userType = strtolower((string) ($_SESSION['user_type'] ?? ''));
        $playerRole = (string) ($_SESSION['player_role'] ?? '');

        $isCaptain = $userType === 'captain' || ($userType === 'player' && $this->isCaptainLikeRole($playerRole));
        if (!$isCaptain) {
            header('Location: ' . ROOT . '/login');
            exit();
        }
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
        $this->ensureCaptainAccess();

        $analyticsModel = $this->model('PerformanceAnalyticsModel');
        $context = $analyticsModel->resolvePlayerContextByNic((string) ($_SESSION['nic'] ?? ''));
        $payload = $analyticsModel->getAnalyzePayload((int) ($context['player_id'] ?? 0), (int) ($context['team_id'] ?? 0));

        $data = array_merge($payload, [
            'player_image' => $this->getPlayerImageBySession(),
        ]);

        $this->view('captain/analyze', $data);
    }
}
