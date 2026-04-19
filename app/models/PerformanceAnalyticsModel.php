<?php

class PerformanceAnalyticsModel
{
    use Model;

    protected $table = 'match_results';
    private $tableExistsCache = [];

    private function safeQuery($sql, $params = [])
    {
        try {
            return $this->query($sql, $params);
        } catch (Throwable $e) {
            return [];
        }
    }

    private function tableExists($table)
    {
        $table = trim((string) $table);
        if ($table === '') {
            return false;
        }

        if (array_key_exists($table, $this->tableExistsCache)) {
            return $this->tableExistsCache[$table];
        }

        $rows = $this->safeQuery(
            "SELECT 1
             FROM information_schema.tables
             WHERE table_schema = DATABASE() AND table_name = :table_name
             LIMIT 1",
            ['table_name' => $table]
        );

        $exists = !empty($rows);
        $this->tableExistsCache[$table] = $exists;

        return $exists;
    }

    public function resolveCoachTeamIdByNic($nic)
    {
        $rows = $this->safeQuery(
            "SELECT tc.team_id
             FROM coaches c
             JOIN team_coaches tc ON tc.coach_id = c.coach_id
             WHERE c.nic = :nic
             ORDER BY tc.team_id ASC
             LIMIT 1",
            ['nic' => $nic]
        );

        if (!empty($rows)) {
            return (int) $rows[0]->team_id;
        }

        return $this->resolveFallbackTeamId();
    }

    public function resolvePlayerContextByNic($nic)
    {
        $rows = $this->safeQuery(
            "SELECT p.player_id, tp.team_id
             FROM players p
             LEFT JOIN team_players tp ON tp.player_id = p.player_id
             WHERE p.nic = :nic
             ORDER BY tp.team_id ASC
             LIMIT 1",
            ['nic' => $nic]
        );

        if (!empty($rows)) {
            return [
                'player_id' => (int) ($rows[0]->player_id ?? 0),
                'team_id' => (int) ($rows[0]->team_id ?? 0),
            ];
        }

        return [
            'player_id' => 0,
            'team_id' => $this->resolveFallbackTeamId(),
        ];
    }

    public function getCoachPerformancePayload($teamId, $matchId = 0, $playerId = 0)
    {
        $hasMatchResults = $this->tableExists('match_results');
        $hasPlayerStats = $this->tableExists('player_match_stats');

        $matchStats = $hasMatchResults ? $this->safeQuery(
            "SELECT
                COUNT(*) AS total_matches,
                SUM(CASE WHEN result = 'Won' THEN 1 ELSE 0 END) AS wins,
                SUM(CASE WHEN result = 'Lost' THEN 1 ELSE 0 END) AS losses,
                SUM(CASE WHEN result = 'Draw' THEN 1 ELSE 0 END) AS draws,
                SUM(COALESCE(goals_scored, 0)) AS goals_scored,
                AVG(COALESCE(possession, 0)) AS avg_possession,
                AVG(COALESCE(passes_accuracy, 0)) AS avg_pass_accuracy
             FROM match_results
             WHERE team_id = :team_id",
            ['team_id' => $teamId]
        ) : [];

        $filterMatchClause = '';
        $filterMatchParams = ['team_id' => $teamId];
        if ($matchId > 0) {
            $filterMatchClause = ' AND pms.match_id = :match_id';
            $filterMatchParams['match_id'] = $matchId;
        }

        $filterPlayerClause = '';
        if ($playerId > 0) {
            $filterPlayerClause = ' AND pms.player_id = :player_id';
            $filterMatchParams['player_id'] = $playerId;
        }

        $playerTotals = ($hasMatchResults && $hasPlayerStats) ? $this->safeQuery(
            "SELECT
                SUM(COALESCE(pms.goals_scored, 0)) AS goals,
                SUM(COALESCE(pms.fouls_committed, 0)) AS fouls,
                AVG(COALESCE(pms.completed_passes, 0)) AS passing_target,
                SUM(COALESCE(pms.tackles_won, 0)) AS tackles
             FROM player_match_stats pms
             JOIN match_results mr ON mr.result_id = pms.match_id
             WHERE mr.team_id = :team_id" . $filterMatchClause . $filterPlayerClause,
            $filterMatchParams
        ) : [];

        $rows = ($hasMatchResults && $hasPlayerStats) ? $this->safeQuery(
            "SELECT
                p.player_id,
                CONCAT(COALESCE(u.first_name, 'Player'), ' ', COALESCE(u.last_name, '')) AS player_name,
                u.image AS player_image,
                SUM(COALESCE(pms.minutes_played, 0)) AS minutes_played,
                SUM(COALESCE(pms.goals_scored, 0)) AS goals,
                SUM(COALESCE(pms.assists, 0)) AS assists,
                SUM(COALESCE(pms.completed_passes, 0)) AS passes,
                SUM(COALESCE(pms.shots_on_target, 0) + COALESCE(pms.shots_off_target, 0)) AS shots,
                SUM(COALESCE(pms.tackles_won, 0) + COALESCE(pms.interceptions, 0)) AS defense
             FROM player_match_stats pms
             JOIN match_results mr ON mr.result_id = pms.match_id
             JOIN players p ON p.player_id = pms.player_id
             LEFT JOIN users u ON u.nic = p.nic
             WHERE mr.team_id = :team_id" . $filterMatchClause . $filterPlayerClause . "
             GROUP BY p.player_id, u.first_name, u.last_name, u.image
             ORDER BY goals DESC, assists DESC, minutes_played DESC
             LIMIT 25",
            $filterMatchParams
        ) : [];

        $comparisonRows = ($hasMatchResults && $hasPlayerStats) ? $this->safeQuery(
            "SELECT
                p.player_id,
                CONCAT(COALESCE(u.first_name, 'Player'), ' ', COALESCE(u.last_name, '')) AS player_name,
                SUM(COALESCE(pms.goals_scored, 0)) AS goals,
                SUM(COALESCE(pms.assists, 0)) AS assists,
                AVG(COALESCE(mr.passes_accuracy, 0)) AS pass_accuracy,
                AVG((COALESCE(pms.minutes_played, 0) / 90) * 100) AS stamina,
                AVG(
                    LEAST(100,
                        (COALESCE(pms.goals_scored, 0) * 20) +
                        (COALESCE(pms.assists, 0) * 15) +
                        (COALESCE(pms.completed_passes, 0) * 0.25) +
                        (COALESCE(pms.tackles_won, 0) * 4)
                    )
                ) AS overall_rating
             FROM player_match_stats pms
             JOIN match_results mr ON mr.result_id = pms.match_id
             JOIN players p ON p.player_id = pms.player_id
             LEFT JOIN users u ON u.nic = p.nic
             WHERE mr.team_id = :team_id
             GROUP BY p.player_id, u.first_name, u.last_name
             ORDER BY overall_rating DESC, goals DESC, assists DESC
             LIMIT 40",
            ['team_id' => $teamId]
        ) : [];

        $matchOptions = $hasMatchResults ? $this->safeQuery(
            "SELECT result_id, opponent_team, date
             FROM match_results
             WHERE team_id = :team_id
             ORDER BY date DESC
             LIMIT 30",
            ['team_id' => $teamId]
        ) : [];

        $playerOptions = $this->safeQuery(
            "SELECT p.player_id, CONCAT(COALESCE(u.first_name, 'Player'), ' ', COALESCE(u.last_name, '')) AS player_name
             FROM team_players tp
             JOIN players p ON p.player_id = tp.player_id
             LEFT JOIN users u ON u.nic = p.nic
             WHERE tp.team_id = :team_id
             ORDER BY u.first_name ASC, u.last_name ASC",
            ['team_id' => $teamId]
        );

        $trendRows = ($hasMatchResults && $hasPlayerStats) ? $this->safeQuery(
            "SELECT
                DATE_FORMAT(mr.date, '%b %d') AS match_label,
                pms.player_id,
                CONCAT(COALESCE(u.first_name, 'Player'), ' ', COALESCE(u.last_name, '')) AS player_name,
                (
                    (COALESCE(pms.goals_scored, 0) * 20) +
                    (COALESCE(pms.assists, 0) * 15) +
                    (COALESCE(pms.completed_passes, 0) * 0.2) +
                    (COALESCE(pms.tackles_won, 0) * 4) +
                    (COALESCE(pms.interceptions, 0) * 3)
                ) AS performance_score,
                mr.date
             FROM player_match_stats pms
             JOIN match_results mr ON mr.result_id = pms.match_id
             JOIN players p ON p.player_id = pms.player_id
             LEFT JOIN users u ON u.nic = p.nic
             WHERE mr.team_id = :team_id" . $filterMatchClause . $filterPlayerClause . "
             ORDER BY mr.date DESC
             LIMIT 80",
            $filterMatchParams
        ) : [];

        $trend = $this->buildCoachTrend($trendRows);

        $summary = !empty($matchStats) ? $matchStats[0] : null;
        $totals = !empty($playerTotals) ? $playerTotals[0] : null;

        return [
            'summary' => [
                'wins' => (int) ($summary->wins ?? 0),
                'losses' => (int) ($summary->losses ?? 0),
                'draws' => (int) ($summary->draws ?? 0),
                'total_matches' => (int) ($summary->total_matches ?? 0),
            ],
            'stats' => [
                'goals_scored' => (int) ($totals->goals ?? 0),
                'fouls' => (int) ($totals->fouls ?? 0),
                'passing_target' => (int) round((float) ($totals->passing_target ?? 0)),
                'possession' => (int) round((float) ($summary->avg_possession ?? 0)),
                'pass_accuracy' => (int) round((float) ($summary->avg_pass_accuracy ?? 0)),
                'tackles_fouls' => ((int) ($totals->tackles ?? 0)) . '/' . ((int) ($totals->fouls ?? 0)),
            ],
            'table_rows' => $rows ?: [],
            'comparison_rows' => $comparisonRows ?: [],
            'match_options' => $matchOptions ?: [],
            'player_options' => $playerOptions ?: [],
            'trend' => $trend,
            'selected_match_id' => $matchId,
            'selected_player_id' => $playerId,
        ];
    }

    public function getAnalyzePayload($playerId, $teamId)
    {
        $hasMatchResults = $this->tableExists('match_results');
        $hasPlayerStats = $this->tableExists('player_match_stats');
        $hasTestResults = $this->tableExists('test_results');
        $testCols = $this->getTestResultColumns();

        $monthlyRows = ($hasMatchResults && $hasPlayerStats) ? $this->safeQuery(
            "SELECT
                DATE_FORMAT(mr.date, '%b') AS month_label,
                AVG((COALESCE(pms.minutes_played, 0) / 90) * 100) AS endurance,
                AVG(LEAST(100, COALESCE(pms.successful_dribbles, 0) * 10)) AS sprint_speed,
                AVG(LEAST(100, COALESCE(pms.key_passes, 0) * 12.5)) AS agility
             FROM player_match_stats pms
             JOIN match_results mr ON mr.result_id = pms.match_id
             WHERE pms.player_id = :player_id
             GROUP BY DATE_FORMAT(mr.date, '%Y-%m'), DATE_FORMAT(mr.date, '%b'), YEAR(mr.date), MONTH(mr.date)
             ORDER BY YEAR(mr.date) DESC, MONTH(mr.date) DESC
             LIMIT 6",
            ['player_id' => $playerId]
        ) : [];

        $monthlyRows = array_reverse($monthlyRows ?: []);

        $seasonTotals = ($hasMatchResults && $hasPlayerStats) ? $this->safeQuery(
            "SELECT
                SUM(COALESCE(pms.goals_scored, 0)) AS goals,
                SUM(COALESCE(pms.assists, 0)) AS assists,
                SUM(COALESCE(pms.completed_passes, 0)) AS passes,
                SUM(COALESCE(pms.minutes_played, 0)) AS minutes,
                AVG((COALESCE(pms.minutes_played, 0) / 90) * 100) AS stamina,
                AVG(LEAST(100, COALESCE(pms.successful_dribbles, 0) * 10)) AS speed,
                AVG(COALESCE(mr.passes_accuracy, 0)) AS accuracy
             FROM player_match_stats pms
             LEFT JOIN match_results mr ON mr.result_id = pms.match_id
             WHERE pms.player_id = :player_id",
            ['player_id' => $playerId]
        ) : [];

        $testRows = [];
        if ($hasTestResults && !empty($testCols['type']) && !empty($testCols['date']) && !empty($testCols['player'])) {
            $scoreExpr = $this->buildTestScoreExpr($testCols);
            $testRows = $this->safeQuery(
                "SELECT
                    {$testCols['type']} AS test_type,
                    {$testCols['date']} AS date,
                    {$scoreExpr} AS score
                 FROM test_results
                 WHERE {$testCols['player']} = :player_id
                 ORDER BY {$testCols['date']} DESC
                 LIMIT 8",
                ['player_id' => $playerId]
            );
        }

        $matchHistory = ($hasMatchResults && $hasPlayerStats) ? $this->safeQuery(
            "SELECT
                mr.date,
                mr.opponent_team,
                mr.result,
                COALESCE(pms.minutes_played, 0) AS minutes_played,
                COALESCE(pms.goals_scored, 0) AS goals_scored,
                COALESCE(pms.assists, 0) AS assists,
                COALESCE(pms.completed_passes, 0) AS completed_passes,
                (COALESCE(pms.tackles_won, 0) + COALESCE(pms.interceptions, 0)) AS defensive_actions,
                COALESCE(pms.notes, '') AS notes
             FROM player_match_stats pms
             JOIN match_results mr ON mr.result_id = pms.match_id
             WHERE pms.player_id = :player_id
             ORDER BY mr.date DESC
             LIMIT 8",
            ['player_id' => $playerId]
        ) : [];

        $lastMonth = ($hasMatchResults && $hasPlayerStats) ? $this->safeQuery(
            "SELECT
                SUM(COALESCE(goals_scored, 0)) AS goals,
                SUM(COALESCE(minutes_played, 0)) AS minutes,
                AVG(LEAST(100, COALESCE(successful_dribbles, 0) * 10)) AS speed
                         FROM player_match_stats pms
                         JOIN match_results mr ON mr.result_id = pms.match_id
                         WHERE pms.player_id = :player_id
                             AND mr.date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)",
            ['player_id' => $playerId]
                    ) : [];

                    $prevMonth = ($hasMatchResults && $hasPlayerStats) ? $this->safeQuery(
            "SELECT
                SUM(COALESCE(goals_scored, 0)) AS goals,
                SUM(COALESCE(minutes_played, 0)) AS minutes,
                AVG(LEAST(100, COALESCE(successful_dribbles, 0) * 10)) AS speed
                         FROM player_match_stats pms
                         JOIN match_results mr ON mr.result_id = pms.match_id
                         WHERE pms.player_id = :player_id
                             AND mr.date >= DATE_SUB(CURDATE(), INTERVAL 60 DAY)
                             AND mr.date < DATE_SUB(CURDATE(), INTERVAL 30 DAY)",
            ['player_id' => $playerId]
                        ) : [];

        $totals = !empty($seasonTotals) ? $seasonTotals[0] : null;
        $current = !empty($lastMonth) ? $lastMonth[0] : null;
        $previous = !empty($prevMonth) ? $prevMonth[0] : null;

        $latestByType = [];
        foreach ($testRows as $row) {
            $type = strtolower(trim((string) ($row->test_type ?? '')));
            if ($type === '') {
                continue;
            }
            if (!isset($latestByType[$type])) {
                $latestByType[$type] = $row;
            }
        }

        $buildTestMetric = function ($label, $key) use ($latestByType) {
            $row = $latestByType[$key] ?? null;
            $rawScore = trim((string) ($row->score ?? ''));
            preg_match('/-?\d+(?:\.\d+)?/', $rawScore, $m);
            $num = isset($m[0]) ? (float) $m[0] : 0.0;
            $percent = (int) max(0, min(100, round($num * 4)));

            return [
                'label' => $label,
                'value' => $percent,
                'status' => $rawScore !== '' ? ('Score: ' . $rawScore) : 'No result yet',
            ];
        };

        $testMetrics = [
            $buildTestMetric('2km', '2km'),
            $buildTestMetric('5km', '5km'),
            $buildTestMetric('Bronko', 'bronko'),
            $buildTestMetric('YoYo', 'yoyo'),
        ];

        $trendLabels = [];
        $trendScoreData = [];
        $trendSource = array_reverse(array_slice($testRows ?: [], 0, 8));
        foreach ($trendSource as $row) {
            $label = !empty($row->date)
                ? (date('M d', strtotime((string) $row->date)) . ' - ' . (string) ($row->test_type ?? 'Test'))
                : (string) ($row->test_type ?? 'Test');
            $rawScore = trim((string) ($row->score ?? ''));
            preg_match('/-?\d+(?:\.\d+)?/', $rawScore, $m);
            $num = isset($m[0]) ? (float) $m[0] : 0.0;

            $trendLabels[] = $label;
            $trendScoreData[] = (int) max(0, min(100, round($num * 4)));
        }

        if (empty($trendLabels)) {
            foreach ($monthlyRows as $row) {
                $trendLabels[] = $row->month_label ?? '';
                $trendScoreData[] = (int) round((float) ($row->endurance ?? 0));
            }
        }

        if (empty($trendLabels)) {
            $trendLabels = ['No Data'];
            $trendScoreData = [0];
        }

        $goalsNow = (float) ($current->goals ?? 0);
        $goalsPrev = (float) ($previous->goals ?? 0);
        $minutesNow = (float) ($current->minutes ?? 0);
        $minutesPrev = (float) ($previous->minutes ?? 0);
        $speedNow = (float) ($current->speed ?? 0);
        $speedPrev = (float) ($previous->speed ?? 0);

        return [
            'performance' => [
                ['label' => $testMetrics[0]['label'], 'value' => $testMetrics[0]['value'], 'status' => $testMetrics[0]['status'], 'color' => '#a29bfe'],
                ['label' => $testMetrics[1]['label'], 'value' => $testMetrics[1]['value'], 'status' => $testMetrics[1]['status'], 'color' => '#0984e3'],
                ['label' => $testMetrics[2]['label'], 'value' => $testMetrics[2]['value'], 'status' => $testMetrics[2]['status'], 'color' => '#00b894'],
                ['label' => $testMetrics[3]['label'], 'value' => $testMetrics[3]['value'], 'status' => $testMetrics[3]['status'], 'color' => '#e17055'],
            ],
            'trends' => [
                'labels' => $trendLabels,
                'datasets' => [
                    ['label' => 'Fitness Test Score', 'data' => $trendScoreData, 'color' => '#a29bfe'],
                ],
            ],
            'comparison' => [
                [
                    'label' => 'Goals Scored',
                    'value' => $this->formatDelta($goalsNow, $goalsPrev),
                    'sub' => ((int) $goalsNow) . ' vs ' . ((int) $goalsPrev),
                    'type' => $goalsNow >= $goalsPrev ? 'up' : 'down',
                ],
                [
                    'label' => 'Minutes Played',
                    'value' => $this->formatDelta($minutesNow, $minutesPrev),
                    'sub' => ((int) $minutesNow) . ' vs ' . ((int) $minutesPrev),
                    'type' => $minutesNow >= $minutesPrev ? 'up' : 'down',
                ],
                [
                    'label' => 'Sprint Speed',
                    'value' => $this->formatDelta($speedNow, $speedPrev),
                    'sub' => ((int) round($speedNow)) . ' vs ' . ((int) round($speedPrev)),
                    'type' => $speedNow >= $speedPrev ? 'up' : 'down',
                ],
            ],
            'stats' => [
                ['label' => 'Goals', 'value' => (int) ($totals->goals ?? 0), 'icon' => 'G', 'color' => '#a29bfe'],
                ['label' => 'Assists', 'value' => (int) ($totals->assists ?? 0), 'icon' => 'A', 'color' => '#0984e3'],
                ['label' => 'Passes', 'value' => (int) ($totals->passes ?? 0), 'icon' => 'P', 'color' => '#00b894'],
                ['label' => 'Minutes', 'value' => (int) ($totals->minutes ?? 0), 'icon' => 'M', 'color' => '#e17055'],
            ],
            'test_results' => $testRows ?: [],
            'match_history' => $matchHistory ?: [],
            'team_id' => $teamId,
        ];
    }

    private function getTestResultColumns()
    {
        if (!$this->tableExists('test_results')) {
            return [
                'type' => null,
                'date' => null,
                'score' => null,
                'notes' => null,
                'player' => null,
            ];
        }

        $rows = $this->safeQuery("SHOW COLUMNS FROM test_results");
        $map = [];
        foreach ($rows as $row) {
            $field = strtolower((string) ($row->Field ?? ''));
            if ($field !== '') {
                $map[$field] = (string) $row->Field;
            }
        }

        $pick = function (array $candidates) use ($map) {
            foreach ($candidates as $candidate) {
                $key = strtolower($candidate);
                if (isset($map[$key])) {
                    return $map[$key];
                }
            }
            return null;
        };

        return [
            'type' => $pick(['test_type', 'test_name', 'type']),
            'date' => $pick(['date', 'test_date']),
            'score' => $pick(['score', 'test_score', 'result', 'result_value', 'value']),
            'notes' => $pick(['notes', 'remarks', 'comment', 'description']),
            'player' => $pick(['player_id']),
        ];
    }

    private function buildTestScoreExpr(array $cols)
    {
        $scoreCol = $cols['score'] ?? null;
        $notesCol = $cols['notes'] ?? null;

        if (!empty($scoreCol) && !empty($notesCol) && strtolower((string) $scoreCol) !== strtolower((string) $notesCol)) {
            return "COALESCE({$scoreCol}, {$notesCol}, '')";
        }
        if (!empty($scoreCol)) {
            return "COALESCE({$scoreCol}, '')";
        }
        if (!empty($notesCol)) {
            return "COALESCE({$notesCol}, '')";
        }

        return "''";
    }

    private function buildCoachTrend(array $trendRows)
    {
        $playerBuckets = [];
        foreach ($trendRows as $row) {
            $playerId = (int) ($row->player_id ?? 0);
            if ($playerId <= 0) {
                continue;
            }

            if (!isset($playerBuckets[$playerId])) {
                $playerBuckets[$playerId] = [
                    'name' => trim((string) ($row->player_name ?? 'Player')),
                    'points' => [],
                ];
            }

            $playerBuckets[$playerId]['points'][] = [
                'label' => (string) ($row->match_label ?? ''),
                'value' => (int) round((float) ($row->performance_score ?? 0)),
            ];
        }

        uasort($playerBuckets, function ($a, $b) {
            return count($b['points']) <=> count($a['points']);
        });

        $selected = array_slice($playerBuckets, 0, 2, true);
        $labels = [];
        $datasets = [];
        $palette = ['#7c3aed', '#3b82f6'];
        $colorIndex = 0;

        foreach ($selected as $bucket) {
            $points = array_reverse(array_slice($bucket['points'], 0, 5));
            $series = [];
            $localLabels = [];
            foreach ($points as $point) {
                $localLabels[] = $point['label'];
                $series[] = $point['value'];
            }
            if (count($localLabels) > count($labels)) {
                $labels = $localLabels;
            }

            $datasets[] = [
                'label' => $bucket['name'],
                'data' => $series,
                'borderColor' => $palette[$colorIndex % count($palette)],
            ];
            $colorIndex++;
        }

        if (empty($datasets)) {
            $labels = ['Match 1', 'Match 2', 'Match 3', 'Match 4', 'Match 5'];
            $datasets[] = [
                'label' => 'Team Average',
                'data' => [0, 0, 0, 0, 0],
                'borderColor' => '#7c3aed',
            ];
        }

        return [
            'labels' => $labels,
            'datasets' => $datasets,
        ];
    }

    private function resolveFallbackTeamId()
    {
        $rows = $this->safeQuery(
            "SELECT DISTINCT team_id
             FROM team_players
             ORDER BY team_id ASC
             LIMIT 1"
        );

        if (!empty($rows)) {
            return (int) $rows[0]->team_id;
        }

        $teams = $this->safeQuery("SELECT team_id FROM teams ORDER BY team_id ASC LIMIT 1");
        if (!empty($teams)) {
            return (int) $teams[0]->team_id;
        }

        return 1;
    }

    private function statusByValue($value)
    {
        $value = (int) round((float) $value);
        if ($value >= 80) {
            return 'Excellent';
        }
        if ($value >= 60) {
            return 'Good';
        }
        if ($value >= 40) {
            return 'Average';
        }
        return 'Needs Work';
    }

    private function formatDelta($current, $previous)
    {
        $current = (float) $current;
        $previous = (float) $previous;

        if ($previous <= 0) {
            return $current > 0 ? '+100%' : '0%';
        }

        $delta = (($current - $previous) / $previous) * 100;
        $sign = $delta >= 0 ? '+' : '';

        return $sign . (string) round($delta) . '%';
    }
}
