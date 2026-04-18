<?php
class team extends Controller {

    private function buildInitialsAvatarUrl($displayName)
    {
        $name = trim((string)$displayName);
        if ($name === '') {
            $name = 'Player';
        }

        $parts = preg_split('/\s+/', $name);
        $initials = '';
        foreach ($parts as $part) {
            if ($part === '') {
                continue;
            }
            $initials .= strtoupper(substr($part, 0, 1));
            if (strlen($initials) >= 2) {
                break;
            }
        }

        if ($initials === '') {
            $initials = 'P';
        }

        $palette = ['#4f46e5', '#0ea5e9', '#059669', '#d97706', '#dc2626', '#7c3aed'];
        $color = $palette[abs(crc32($name)) % count($palette)];

        $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="480" height="640" viewBox="0 0 480 640">'
            . '<rect width="480" height="640" fill="' . $color . '"/>'
            . '<text x="50%" y="52%" dominant-baseline="middle" text-anchor="middle" '
            . 'font-family="Arial, Helvetica, sans-serif" font-size="170" font-weight="700" fill="#ffffff">'
            . htmlspecialchars($initials, ENT_QUOTES, 'UTF-8')
            . '</text>'
            . '</svg>';

        return 'data:image/svg+xml;utf8,' . rawurlencode($svg);
    }

    private function normalizeImageUrl($imagePath, $displayName = '')
    {
        if (empty($imagePath)) {
            return $this->buildInitialsAvatarUrl($displayName);
        }

        $normalized = str_replace('\\', '/', ltrim((string)$imagePath, '/'));
        return ROOT . '/' . $normalized;
    }

    private function categorizePosition($position)
    {
        $position = strtolower(trim((string)$position));

        if ($position === '') {
            return 'midfielders';
        }

        if (strpos($position, 'keeper') !== false || strpos($position, 'goal') !== false) {
            return 'goalkeepers';
        }

        if (
            strpos($position, 'defender') !== false ||
            strpos($position, 'back') !== false ||
            strpos($position, 'sweeper') !== false ||
            strpos($position, 'center back') !== false
        ) {
            return 'defenders';
        }

        if (
            strpos($position, 'striker') !== false ||
            strpos($position, 'forward') !== false ||
            strpos($position, 'wing') !== false ||
            strpos($position, 'attacker') !== false
        ) {
            return 'strikers';
        }

        return 'midfielders';
    }

    public function index() {
        $teamModel = $this->model('TeamModel');
        $playerModel = $this->model('PlayerModel');
        $coachModel = $this->model('CoachModel');

        $presentTeams = [];
        try {
            $presentTeams = $teamModel->getByStatus('present');
        } catch (Exception $e) {
            error_log('Failed to load present teams: ' . $e->getMessage());
            $presentTeams = $teamModel->getAll();
        }

        $groupedPlayers = [
            'goalkeepers' => [],
            'defenders' => [],
            'midfielders' => [],
            'strikers' => []
        ];

        $seenPlayers = [];

        foreach ($presentTeams as $teamRow) {
            try {
                $players = $playerModel->getByTeamId($teamRow->team_id);
            } catch (Exception $e) {
                error_log('Failed to load players for team ' . $teamRow->team_id . ': ' . $e->getMessage());
                $players = [];
            }

            foreach ($players as $player) {
                if (isset($seenPlayers[$player->player_id])) {
                    continue;
                }

                $seenPlayers[$player->player_id] = true;
                $group = $this->categorizePosition($player->position ?? '');

                $fullName = trim(($player->first_name ?? '') . ' ' . ($player->last_name ?? ''));
                if ($fullName === '') {
                    $fullName = $player->nic ?? 'Player';
                }

                $image = $this->normalizeImageUrl($player->image ?? '', $fullName);

                $groupedPlayers[$group][] = [
                    'id' => $player->player_id,
                    'name' => $fullName,
                    'position' => $player->position ?: 'Player',
                    'role' => $player->role ?: 'Player',
                    'image' => $image
                ];
            }
        }

        $coaches = [];
        $seenCoaches = [];
        foreach ($presentTeams as $teamRow) {
            try {
                $teamCoaches = $coachModel->getByTeamId($teamRow->team_id);
            } catch (Exception $e) {
                $teamCoaches = [];
            }

            foreach ($teamCoaches as $coach) {
                if (isset($seenCoaches[$coach->coach_id])) {
                    continue;
                }

                $seenCoaches[$coach->coach_id] = true;
                $name = trim(($coach->first_name ?? '') . ' ' . ($coach->last_name ?? ''));
                if ($name === '') {
                    $name = $coach->nic ?? 'Coach';
                }

                $coaches[] = [
                    'name' => $name,
                    'license' => $coach->license ?? 'Coach',
                    'image' => $this->normalizeImageUrl($coach->image ?? '', $name)
                ];
            }
        }

        $this->view('team', [
            'playersByPosition' => $groupedPlayers,
            'coaches' => $coaches
        ]);
    }
}
