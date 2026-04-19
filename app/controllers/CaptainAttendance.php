<?php

class CaptainAttendance extends Controller
{
    private function resolveCaptainTeamId($nic)
    {
        $playerModel = $this->model('PlayerModel');

        $rows = $playerModel->query(
            "SELECT tp.team_id
             FROM players p
             JOIN team_players tp ON tp.player_id = p.player_id
             WHERE p.nic = :nic
             ORDER BY tp.team_id DESC
             LIMIT 1",
            ['nic' => $nic]
        );

        if (empty($rows)) {
            return null;
        }

        return (int) ($rows[0]->team_id ?? 0) ?: null;
    }

    private function buildInitialsAvatarUrl($displayName)
    {
        $name = trim((string) $displayName);
        if ($name === '') {
            $name = 'Captain';
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
            $initials = 'C';
        }

        $palette = ['#4f46e5', '#0ea5e9', '#059669', '#d97706', '#dc2626', '#7c3aed'];
        $color = $palette[abs(crc32($name)) % count($palette)];

        $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="320" height="320" viewBox="0 0 320 320">'
            . '<rect width="320" height="320" fill="' . $color . '"/>'
            . '<text x="50%" y="52%" dominant-baseline="middle" text-anchor="middle" '
            . 'font-family="Arial, Helvetica, sans-serif" font-size="120" font-weight="700" fill="#ffffff">'
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

    private function buildCaptainUiData()
    {
        $nic = $_SESSION['nic'] ?? '';
        $playerModel = $this->model('PlayerModel');

        $rows = $playerModel->query(
            "SELECT u.first_name, u.last_name, u.image
             FROM players p
             JOIN users u ON u.nic = p.nic
             WHERE p.nic = :nic
             LIMIT 1",
            ['nic' => $nic]
        );

        $captain = !empty($rows) ? $rows[0] : null;
        $fullName = trim((($captain->first_name ?? '') . ' ' . ($captain->last_name ?? '')));
        if ($fullName === '') {
            $fullName = 'Captain';
        }

        return [
            'captain_name' => $fullName,
            'captain_image' => $this->normalizeImageUrl($captain->image ?? '', $fullName)
        ];
    }

    private function getCaptainNotices($limit = 6)
    {
        $noticeModel = $this->model('NoticeModel');
        $noticeRows = [];

        try {
            $noticeRows = $noticeModel->getRecent($limit, 'present_team');
        } catch (Exception $e) {
            $noticeRows = [];
        }

        $notices = [];
        foreach ($noticeRows as $row) {
            $notices[] = [
                'title' => $row->title ?? 'Notice',
                'content' => $row->content ?? '',
                'author' => $row->created_by ?? 'Admin',
                'date' => !empty($row->created_at) ? date('M d, Y h:i A', strtotime($row->created_at)) : ''
            ];
        }

        if (empty($notices)) {
            $notices[] = [
                'title' => 'No notices yet',
                'content' => 'Admin notices for present team members will appear here.',
                'author' => 'System',
                'date' => ''
            ];
        }

        return $notices;
    }

   
    public function index()
    {
        $this->ensureCaptainAccess();

        $attendanceModel = new AttendanceModel();
        $teamId = $this->resolveCaptainTeamId((string) ($_SESSION['nic'] ?? ''));

        // ✅ Get selected date (from URL)
        $date = $_GET['date'] ?? date('Y-m-d');
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $date) || strtotime($date) === false) {
            $date = date('Y-m-d');
        }
        $type = $_GET['type'] ?? 'Practice';

        // ✅ Get or create event
        $event = $attendanceModel->getOrCreateEvent($date, $type);

        $event_id = $event->event_id;
        if ($teamId) {
            $players = $attendanceModel->getPlayersWithAttendanceByTeam($event_id, $teamId);
            $stats = $attendanceModel->getStatsByTeam($event_id, $teamId);
        } else {
            $players = $attendanceModel->getPlayersWithAttendance($event_id);
            $stats = $attendanceModel->getStats($event_id);
        }

        $uiData = $this->buildCaptainUiData();
        $notices = $this->getCaptainNotices();

        $data = [
            'event_id' => $event_id,
            'selected_date' => $date,
            'totalPlayers' => $stats->total ?? 0,
            'present' => $stats->present ?? 0,
            'absent' => $stats->absent ?? 0,
            'players' => $players,
            'selected_type' => $type,
            'captain_name' => $uiData['captain_name'],
            'captain_image' => $uiData['captain_image'],
            'notices' => $notices,
        ];

        $this->view('captain/attendance', $data);
    }
    public function update()
    {
        $this->ensureCaptainAccess();

        $payload = json_decode(file_get_contents("php://input"), true);
        if (empty($payload)) {
            echo json_encode(["success" => false, "message" => "No attendance data received"]);
            exit;
        }

        $attendanceModel = new AttendanceModel();
        $teamId = $this->resolveCaptainTeamId((string) ($_SESSION['nic'] ?? ''));

        // Backward compatibility: old format is a direct rows array.
        $rows = is_array($payload) && isset($payload[0]) ? $payload : ($payload['rows'] ?? []);

        // Preferred format: selected date/type is sent with rows so marking always targets selected session.
        $selectedDate = $payload['date'] ?? ($_GET['date'] ?? date('Y-m-d'));
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $selectedDate) || strtotime($selectedDate) === false) {
            $selectedDate = date('Y-m-d');
        }

        $selectedType = $payload['type'] ?? ($_GET['type'] ?? 'Practice');
        $event = $attendanceModel->getOrCreateEvent($selectedDate, $selectedType);
        $targetEventId = (int) ($event->event_id ?? 0);

        if ($targetEventId <= 0 || empty($rows)) {
            echo json_encode(["success" => false, "message" => "Invalid attendance payload"]);
            exit;
        }

        $teamPlayerIds = $teamId ? array_flip($attendanceModel->getTeamPlayerIds($teamId)) : [];

        foreach ($rows as $row) {
            if (!in_array($row['status'], ['Present', 'Absent', 'Late']))
                continue;

            $playerId = (int) ($row['player_id'] ?? 0);
            if ($playerId <= 0) {
                continue;
            }

            if ($teamId && !isset($teamPlayerIds[$playerId])) {
                continue;
            }

            $attendanceModel->query("
            INSERT INTO attendance (player_id, event_id, status)
            VALUES (:player_id, :event_id, :status)
            ON DUPLICATE KEY UPDATE status = :status2
        ", [
                'player_id' => $playerId,
                'event_id' => $targetEventId,
                'status' => $row['status'],
                'status2' => $row['status']
            ]);
        }

        echo json_encode(["success" => true, "event_id" => $targetEventId]);
        exit;
    }
    public function export()
    {
        $this->ensureCaptainAccess();

        $attendanceModel = new AttendanceModel();
        $teamId = $this->resolveCaptainTeamId((string) ($_SESSION['nic'] ?? ''));

        $date = $_GET['date'] ?? date('Y-m-d');
        $type = $_GET['type'] ?? 'Practice';
        $event = $attendanceModel->getOrCreateEvent($date, $type);

        if ($teamId) {
            $players = $attendanceModel->getPlayersWithAttendanceByTeam($event->event_id, $teamId);
        } else {
            $players = $attendanceModel->getPlayersWithAttendance($event->event_id);
        }

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="attendance-' . $date . '.csv"');

        $output = fopen("php://output", "w");

        fputcsv($output, ['Player', 'Position', 'Status']);

        foreach ($players as $p) {
            fputcsv($output, [
                $p->name,
                $p->position,
                $p->status
            ]);
        }

        fclose($output);
        exit;
    }
}

