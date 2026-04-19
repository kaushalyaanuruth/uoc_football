<?php

require_once __DIR__ . '/CoachBaseController.php';

class coachPerformance extends CoachBaseController {

    public function index() {
        $this->ensureCoachAccess();

        $analyticsModel = $this->model('PerformanceAnalyticsModel');
        $teamId = $analyticsModel->resolveCoachTeamIdByNic((string) ($_SESSION['nic'] ?? ''));

        $selectedMatchId = (int) ($_GET['match_id'] ?? 0);
        $selectedPlayerId = (int) ($_GET['player_id'] ?? 0);

        $payload = $analyticsModel->getCoachPerformancePayload($teamId, $selectedMatchId, $selectedPlayerId);
        $coachNote = $analyticsModel->getCoachPerformanceNote((string) ($_SESSION['nic'] ?? ''), $teamId);

        $this->view('coachPerformance', $this->buildCoachViewData([
            'performance_payload' => $payload,
            'coach_note' => $coachNote,
        ]));
    }

    public function saveNotes()
    {
        $this->ensureCoachAccess();

        if (strtoupper($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            http_response_code(405);
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit();
        }

        $payload = json_decode(file_get_contents('php://input'), true);
        $noteText = trim((string) ($payload['note'] ?? ''));

        $analyticsModel = $this->model('PerformanceAnalyticsModel');
        $teamId = $analyticsModel->resolveCoachTeamIdByNic((string) ($_SESSION['nic'] ?? ''));
        $saved = $analyticsModel->saveCoachPerformanceNote((string) ($_SESSION['nic'] ?? ''), $teamId, $noteText);

        header('Content-Type: application/json');
        if (!$saved) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Failed to save notes']);
            exit();
        }

        echo json_encode(['success' => true, 'message' => 'Notes saved successfully']);
        exit();
    }

    public function playerMatchStat()
    {
        $this->ensureCoachAccess();

        $playerId = (int) ($_GET['player_id'] ?? 0);
        $matchId = (int) ($_GET['match_id'] ?? 0);

        if ($playerId <= 0) {
            http_response_code(400);
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid player id']);
            exit();
        }

        $analyticsModel = $this->model('PerformanceAnalyticsModel');
        $teamId = $analyticsModel->resolveCoachTeamIdByNic((string) ($_SESSION['nic'] ?? ''));
        $row = $analyticsModel->getCoachPlayerMatchStatCard($teamId, $playerId, $matchId);

        if (!$row) {
            http_response_code(404);
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'No match stats found for this player']);
            exit();
        }

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'stat' => [
                'player_id' => (int) ($row->player_id ?? 0),
                'player_name' => (string) ($row->player_name ?? 'Player'),
                'player_image' => (string) ($row->player_image ?? ''),
                'match_id' => (int) ($row->match_id ?? 0),
                'opponent_team' => (string) ($row->opponent_team ?? 'Opponent'),
                'match_date' => (string) ($row->match_date ?? ''),
                'match_result' => (string) ($row->match_result ?? 'N/A'),
                'minutes_played' => (int) ($row->minutes_played ?? 0),
                'goals_scored' => (int) ($row->goals_scored ?? 0),
                'assists' => (int) ($row->assists ?? 0),
                'completed_passes' => (int) ($row->completed_passes ?? 0),
                'shots_on_target' => (int) ($row->shots_on_target ?? 0),
                'shots_off_target' => (int) ($row->shots_off_target ?? 0),
                'tackles_won' => (int) ($row->tackles_won ?? 0),
                'interceptions' => (int) ($row->interceptions ?? 0),
                'fouls_committed' => (int) ($row->fouls_committed ?? 0),
                'notes' => (string) ($row->notes ?? ''),
            ],
        ]);
        exit();
    }
}