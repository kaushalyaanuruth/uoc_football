<?php

require_once __DIR__ . '/CoachBaseController.php';

class coachAttendance extends CoachBaseController {

    private function resolveCoachTeamContext()
    {
        $nic = (string) ($_SESSION['nic'] ?? '');
        $coachModel = $this->model('CoachModel');

        // Prefer a coach-linked team that actually has players.
        $rows = $coachModel->query(
            "SELECT t.team_id, t.season
             FROM coaches c
             JOIN team_coaches tc ON tc.coach_id = c.coach_id
             JOIN teams t ON t.team_id = tc.team_id
             LEFT JOIN team_players tp ON tp.team_id = t.team_id
             WHERE c.nic = :nic
             GROUP BY t.team_id, t.season
             ORDER BY COUNT(tp.player_id) DESC, t.team_id DESC
             LIMIT 1",
            ['nic' => $nic]
        );

        // Fallback: if coach is not linked yet, still pick a team that has players.
        if (empty($rows)) {
            $rows = $coachModel->query(
                "SELECT t.team_id, t.season
                 FROM teams t
                 JOIN team_players tp ON tp.team_id = t.team_id
                 GROUP BY t.team_id, t.season
                 ORDER BY COUNT(tp.player_id) DESC, t.team_id DESC
                 LIMIT 1"
            );
        }

        if (empty($rows)) {
            return [
                'team_id' => null,
                'season' => 'N/A',
            ];
        }

        return [
            'team_id' => (int) ($rows[0]->team_id ?? 0) ?: null,
            'season' => (string) ($rows[0]->season ?? 'N/A'),
        ];
    }

    private function normalizeDate($date)
    {
        $value = (string) $date;
        $today = date('Y-m-d');
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $value) || strtotime($value) === false) {
            return $today;
        }

        if ($value > $today) {
            return $today;
        }

        return $value;
    }

    private function normalizeType($type)
    {
        $allowed = ['Practice', 'Match', 'Training', 'Fitness'];
        $normalized = ucfirst(strtolower(trim((string) $type)));
        return in_array($normalized, $allowed, true) ? $normalized : 'Practice';
    }

    private function responseJson($payload, $status = 200)
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($payload);
        exit();
    }

    private function buildPlayerOptions($players)
    {
        $options = [];
        foreach ($players as $player) {
            $options[] = [
                'id' => (int) ($player->player_id ?? 0),
                'name' => (string) ($player->name ?? 'Player'),
            ];
        }

        return $options;
    }

    public function index() {
        $this->ensureCoachAccess();

        $attendanceModel = new AttendanceModel();
        $teamContext = $this->resolveCoachTeamContext();

        $date = $this->normalizeDate($_GET['date'] ?? date('Y-m-d'));
        $type = $this->normalizeType($_GET['type'] ?? 'Practice');
        $event = $attendanceModel->getOrCreateEvent($date, $type);
        $eventId = (int) ($event->event_id ?? 0);

        $teamId = $teamContext['team_id'];

        if ($teamId) {
            $players = $attendanceModel->getPlayersWithAttendanceByTeam($eventId, $teamId);
            $stats = $attendanceModel->getStatsByTeam($eventId, $teamId);
            $distribution = $attendanceModel->getDistributionByTeam($eventId, $teamId);
            $totalSessions = $attendanceModel->countSessionsByTeam($teamId);
            $trend = $attendanceModel->getAttendanceTrendByTeam($teamId, $date, $type, 8);
        } else {
            $players = [];
            $stats = (object) ['total' => 0, 'present' => 0, 'absent' => 0, 'late' => 0];
            $distribution = ['Present' => 0, 'Absent' => 0, 'Late' => 0, 'Excused' => 0];
            $totalSessions = 0;
            $trend = ['labels' => ['No Data'], 'values' => [0]];
        }

        $total = (int) ($stats->total ?? 0);
        $present = (int) ($stats->present ?? 0);
        $late = (int) ($stats->late ?? 0);
        $overall = $total > 0 ? round((($present + $late) / $total) * 100, 1) : 0;

        $this->view('coachAttendance', $this->buildCoachViewData([
            'event_id' => $eventId,
            'selected_date' => $date,
            'selected_type' => $type,
            'season' => $teamContext['season'],
            'team_id' => $teamId,
            'players' => $players,
            'player_options' => $this->buildPlayerOptions($players),
            'totalPlayers' => $total,
            'present' => $present,
            'absent' => (int) ($stats->absent ?? 0),
            'late' => $late,
            'overall_attendance' => $overall,
            'total_sessions' => $totalSessions,
            'trend_labels' => $trend['labels'],
            'trend_values' => $trend['values'],
            'distribution_labels' => array_keys($distribution),
            'distribution_values' => array_values($distribution),
        ]));
    }

    public function update()
    {
        $this->ensureCoachAccess();

        try {
            if (strtoupper($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
                $this->responseJson(['success' => false, 'message' => 'Method not allowed'], 405);
            }

            $payload = json_decode(file_get_contents('php://input'), true);
            if (empty($payload)) {
                $this->responseJson(['success' => false, 'message' => 'No attendance data received'], 400);
            }

            $rows = is_array($payload) && isset($payload[0]) ? $payload : ($payload['rows'] ?? []);
            if (empty($rows)) {
                $this->responseJson(['success' => false, 'message' => 'No changed rows to save'], 400);
            }

            $date = $this->normalizeDate($payload['date'] ?? date('Y-m-d'));
            $type = $this->normalizeType($payload['type'] ?? 'Practice');

            $attendanceModel = new AttendanceModel();
            $teamContext = $this->resolveCoachTeamContext();
            $teamId = $teamContext['team_id'];
            if (!$teamId) {
                $this->responseJson(['success' => false, 'message' => 'Coach team not found'], 400);
            }

            $event = $attendanceModel->getOrCreateEvent($date, $type);
            $targetEventId = (int) ($event->event_id ?? 0);
            if ($targetEventId <= 0) {
                $this->responseJson(['success' => false, 'message' => 'Unable to resolve attendance event'], 500);
            }

            $allowedStatuses = ['Present', 'Absent', 'Late'];
            $teamPlayerIds = array_flip($attendanceModel->getTeamPlayerIds($teamId));

            foreach ($rows as $row) {
                $playerId = (int) ($row['player_id'] ?? 0);
                $status = (string) ($row['status'] ?? '');

                if ($playerId <= 0 || !isset($teamPlayerIds[$playerId])) {
                    continue;
                }

                if (!in_array($status, $allowedStatuses, true)) {
                    continue;
                }

                $attendanceModel->query(
                    "INSERT INTO attendance (player_id, event_id, status)
                     VALUES (:player_id, :event_id, :status)
                     ON DUPLICATE KEY UPDATE status = :status2",
                    [
                        'player_id' => $playerId,
                        'event_id' => $targetEventId,
                        'status' => $status,
                        'status2' => $status,
                    ]
                );
            }

            $this->responseJson(['success' => true, 'event_id' => $targetEventId]);
        } catch (Throwable $e) {
            $this->responseJson([
                'success' => false,
                'message' => 'Failed to save attendance: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function export()
    {
        $this->ensureCoachAccess();

        $date = $this->normalizeDate($_GET['date'] ?? date('Y-m-d'));
        $type = $this->normalizeType($_GET['type'] ?? 'Practice');

        $attendanceModel = new AttendanceModel();
        $teamContext = $this->resolveCoachTeamContext();
        $teamId = $teamContext['team_id'];

        $event = $attendanceModel->getOrCreateEvent($date, $type);
        $eventId = (int) ($event->event_id ?? 0);

        $players = $teamId
            ? $attendanceModel->getPlayersWithAttendanceByTeam($eventId, $teamId)
            : [];

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="coach-attendance-' . $date . '.csv"');

        $output = fopen('php://output', 'w');
        fputcsv($output, ['Player', 'Position', 'Status']);

        foreach ($players as $player) {
            fputcsv($output, [
                $player->name ?? 'Player',
                $player->position ?? '-',
                $player->status ?? 'Absent',
            ]);
        }

        fclose($output);
        exit();
    }

    public function playerHistory()
    {
        $this->ensureCoachAccess();

        try {
            $playerId = (int) ($_GET['player_id'] ?? 0);
            if ($playerId <= 0) {
                $this->responseJson(['success' => false, 'message' => 'Invalid player id'], 400);
            }

            $attendanceModel = new AttendanceModel();
            $teamContext = $this->resolveCoachTeamContext();
            $teamId = $teamContext['team_id'];
            if (!$teamId) {
                $this->responseJson(['success' => false, 'message' => 'Coach team not found'], 400);
            }

            $teamPlayerIds = array_flip($attendanceModel->getTeamPlayerIds($teamId));
            if (!isset($teamPlayerIds[$playerId])) {
                $this->responseJson(['success' => false, 'message' => 'Player does not belong to coach team'], 403);
            }

            $historyRows = $attendanceModel->getPlayerAttendanceHistoryByTeam($teamId, $playerId, 250);
            $history = [];
            foreach ($historyRows as $row) {
                $history[] = [
                    'date' => (string) ($row->date ?? ''),
                    'session_type' => (string) ($row->session_type ?? 'Practice'),
                    'status' => (string) ($row->status ?? 'Absent'),
                    'location' => (string) ($row->location ?? 'Ground'),
                ];
            }

            $this->responseJson([
                'success' => true,
                'player_id' => $playerId,
                'history' => $history,
            ]);
        } catch (Throwable $e) {
            $this->responseJson([
                'success' => false,
                'message' => 'Failed to load player attendance history: ' . $e->getMessage(),
            ], 500);
        }
    }
}