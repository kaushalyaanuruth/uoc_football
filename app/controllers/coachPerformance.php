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

        $this->view('coachPerformance', $this->buildCoachViewData([
            'performance_payload' => $payload,
        ]));
    }
}