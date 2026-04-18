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

        $data = [
            'performance' => [
                ['label' => 'Stamina', 'value' => 85, 'status' => 'Excellent', 'color' => '#a29bfe'],
                ['label' => 'Speed', 'value' => 78, 'status' => 'Good', 'color' => '#0984e3'],
                ['label' => 'Accuracy', 'value' => 92, 'status' => 'Excellent', 'color' => '#00b894'],
                ['label' => 'Overall', 'value' => 81, 'status' => 'Good', 'color' => '#e17055']
            ],
            'trends' => [
                'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                'datasets' => [
                    ['label' => 'Endurance', 'data' => [75, 78, 83, 85, 88, 90], 'color' => '#a29bfe'],
                    ['label' => 'Sprint Speed', 'data' => [70, 72, 75, 78, 82, 85], 'color' => '#00b894'],
                    ['label' => 'Agility', 'data' => [68, 70, 73, 76, 80, 83], 'color' => '#fdcb6e']
                ]
            ],
            'comparison' => [
                ['label' => 'Goals Scored', 'value' => '+25%', 'sub' => '8 vs 6', 'type' => 'up'],
                ['label' => 'Minutes Played', 'value' => '+12%', 'sub' => '450 vs 402', 'type' => 'up'],
                ['label' => 'Sprint Speed', 'value' => '+8%', 'sub' => '28.5 vs 26.4 km/h', 'type' => 'up']
            ],
            'stats' => [
                ['label' => 'Goals', 'value' => 12, 'icon' => 'G', 'color' => '#a29bfe'],
                ['label' => 'Assists', 'value' => 8, 'icon' => 'A', 'color' => '#0984e3'],
                ['label' => 'Passes', 'value' => 456, 'icon' => 'P', 'color' => '#00b894'],
                ['label' => 'Minutes', 'value' => 890, 'icon' => 'M', 'color' => '#e17055']
            ],
            'player_image' => $this->getPlayerImageBySession()
        ];

        $this->view('captain/analyze', $data);
    }
}
