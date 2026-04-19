<?php
require __DIR__ . '/../app/core/config.php';

try {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS match_results (
            result_id INT(32) PRIMARY KEY AUTO_INCREMENT,
            opponent_team VARCHAR(100) NOT NULL,
            result ENUM('Won', 'Draw', 'Lost') NOT NULL,
            goals_scored INT(32) DEFAULT 0,
            goals_conceded INT(32) DEFAULT 0,
            shots INT(32) DEFAULT 0,
            shots_on_target INT(32) DEFAULT 0,
            possession DECIMAL(5,2) DEFAULT 0,
            passes INT(32) DEFAULT 0,
            passes_accuracy DECIMAL(5,2) DEFAULT 0,
            corners INT(32) DEFAULT 0,
            date DATE NOT NULL,
            notes VARCHAR(255),
            team_id INT(32) NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
    );

    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS player_match_stats (
            stat_id INT AUTO_INCREMENT PRIMARY KEY,
            match_id INT NOT NULL,
            player_id INT NOT NULL,
            position_played VARCHAR(100),
            minutes_played INT DEFAULT 0,
            substitution_status ENUM('Started', 'Substitute', 'Unused') DEFAULT 'Started',
            goals_scored INT DEFAULT 0,
            assists INT DEFAULT 0,
            shots_on_target INT DEFAULT 0,
            shots_off_target INT DEFAULT 0,
            key_passes INT DEFAULT 0,
            successful_dribbles INT DEFAULT 0,
            completed_passes INT DEFAULT 0,
            line_breaking_passes INT DEFAULT 0,
            tackles_won INT DEFAULT 0,
            interceptions INT DEFAULT 0,
            defensive_duels_won INT DEFAULT 0,
            aerial_duels_won INT DEFAULT 0,
            yellow_cards INT DEFAULT 0,
            red_cards INT DEFAULT 0,
            fouls_committed INT DEFAULT 0,
            fouls_won INT DEFAULT 0,
            notes TEXT,
            UNIQUE KEY uniq_match_player (match_id, player_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
    );

    $teams = $pdo->query("SELECT DISTINCT team_id FROM team_players ORDER BY team_id ASC")->fetchAll(PDO::FETCH_ASSOC);
    if (empty($teams)) {
        echo "NO_TEAMS_WITH_PLAYERS\n";
        exit(0);
    }

    $opponents = ['Arsenal FC', 'Chelsea FC', 'Liverpool FC', 'Manchester City', 'Tottenham FC', 'Leeds United'];
    $results = ['Won', 'Draw', 'Lost'];

    $insertMatch = $pdo->prepare(
        "INSERT INTO match_results (
            opponent_team, result, goals_scored, goals_conceded, shots, shots_on_target,
            possession, passes, passes_accuracy, corners, date, notes, team_id
        ) VALUES (
            :opponent_team, :result, :goals_scored, :goals_conceded, :shots, :shots_on_target,
            :possession, :passes, :passes_accuracy, :corners, :date, :notes, :team_id
        )"
    );

    $insertStat = $pdo->prepare(
        "INSERT INTO player_match_stats (
            match_id, player_id, position_played, minutes_played, substitution_status,
            goals_scored, assists, shots_on_target, shots_off_target, key_passes, successful_dribbles,
            completed_passes, line_breaking_passes,
            tackles_won, interceptions, defensive_duels_won, aerial_duels_won,
            yellow_cards, red_cards, fouls_committed, fouls_won, notes
        ) VALUES (
            :match_id, :player_id, :position_played, :minutes_played, :substitution_status,
            :goals_scored, :assists, :shots_on_target, :shots_off_target, :key_passes, :successful_dribbles,
            :completed_passes, :line_breaking_passes,
            :tackles_won, :interceptions, :defensive_duels_won, :aerial_duels_won,
            :yellow_cards, :red_cards, :fouls_committed, :fouls_won, :notes
        )"
    );

    $seededMatches = 0;
    $seededStats = 0;
    $today = new DateTimeImmutable('today');

    foreach ($teams as $teamIndex => $t) {
        $teamId = (int) ($t['team_id'] ?? 0);
        if ($teamId <= 0) {
            continue;
        }

        $players = $pdo->prepare(
            "SELECT p.player_id, p.position
             FROM team_players tp
             JOIN players p ON p.player_id = tp.player_id
             WHERE tp.team_id = :team_id
             ORDER BY p.player_id ASC"
        );
        $players->execute(['team_id' => $teamId]);
        $teamPlayers = $players->fetchAll(PDO::FETCH_ASSOC);

        if (empty($teamPlayers)) {
            continue;
        }

        $matchCountForTeam = 3;
        for ($m = 0; $m < $matchCountForTeam; $m++) {
            $offsetDays = ($teamIndex * 10) + ($m * 7);
            $matchDate = $today->sub(new DateInterval('P' . $offsetDays . 'D'))->format('Y-m-d');
            $res = $results[($m + $teamIndex) % count($results)];

            $goalsScored = 1 + (($teamIndex + $m) % 4);
            $goalsConceded = ($res === 'Won') ? max(0, $goalsScored - 1) : (($res === 'Draw') ? $goalsScored : $goalsScored + 1);
            $shots = 8 + (($teamIndex + $m) % 9);
            $shotsOnTarget = max(2, min($shots, (int) floor($shots * 0.45)));
            $possession = 45 + (($teamIndex + $m) % 18);
            $passes = 280 + (($teamIndex + $m) * 22);
            $passAcc = 68 + (($teamIndex + $m) % 22);
            $corners = 2 + (($teamIndex + $m) % 6);

            $insertMatch->execute([
                'opponent_team' => $opponents[($teamIndex + $m) % count($opponents)],
                'result' => $res,
                'goals_scored' => $goalsScored,
                'goals_conceded' => $goalsConceded,
                'shots' => $shots,
                'shots_on_target' => $shotsOnTarget,
                'possession' => $possession,
                'passes' => $passes,
                'passes_accuracy' => $passAcc,
                'corners' => $corners,
                'date' => $matchDate,
                'notes' => 'Dummy seeded match data for integration testing',
                'team_id' => $teamId,
            ]);

            $matchId = (int) $pdo->lastInsertId();
            $seededMatches++;

            foreach ($teamPlayers as $pIndex => $p) {
                $playerId = (int) ($p['player_id'] ?? 0);
                if ($playerId <= 0) {
                    continue;
                }

                $minutes = ($pIndex % 5 === 4) ? 30 : 90;
                $subStatus = ($minutes === 90) ? 'Started' : 'Substitute';
                $goals = (($pIndex + $m) % 9 === 0) ? 1 : 0;
                $assists = (($pIndex + $m) % 7 === 0) ? 1 : 0;
                $onTarget = max(0, min(3, ($pIndex + $m) % 4));
                $offTarget = max(0, min(3, ($pIndex + $m + 1) % 4));
                $keyPasses = ($pIndex + $m) % 5;
                $dribbles = ($pIndex + $m + 2) % 6;
                $completedPasses = 18 + (($pIndex * 3 + $m) % 35);
                $lineBreakingPasses = ($pIndex + $m) % 4;
                $tackles = ($pIndex + $m + 1) % 5;
                $interceptions = ($pIndex + $m + 2) % 4;
                $defDuels = ($pIndex + $m + 3) % 6;
                $aerial = ($pIndex + $m) % 4;
                $yellow = (($pIndex + $m) % 13 === 0) ? 1 : 0;
                $red = 0;
                $foulsCommitted = ($pIndex + $m) % 3;
                $foulsWon = ($pIndex + $m + 1) % 3;

                $insertStat->execute([
                    'match_id' => $matchId,
                    'player_id' => $playerId,
                    'position_played' => (string) ($p['position'] ?? 'Midfielder'),
                    'minutes_played' => $minutes,
                    'substitution_status' => $subStatus,
                    'goals_scored' => $goals,
                    'assists' => $assists,
                    'shots_on_target' => $onTarget,
                    'shots_off_target' => $offTarget,
                    'key_passes' => $keyPasses,
                    'successful_dribbles' => $dribbles,
                    'completed_passes' => $completedPasses,
                    'line_breaking_passes' => $lineBreakingPasses,
                    'tackles_won' => $tackles,
                    'interceptions' => $interceptions,
                    'defensive_duels_won' => $defDuels,
                    'aerial_duels_won' => $aerial,
                    'yellow_cards' => $yellow,
                    'red_cards' => $red,
                    'fouls_committed' => $foulsCommitted,
                    'fouls_won' => $foulsWon,
                    'notes' => 'Dummy player stat row',
                ]);

                $seededStats++;
            }
        }
    }

    $matchCount = (int) $pdo->query("SELECT COUNT(*) FROM match_results")->fetchColumn();
    $statCount = (int) $pdo->query("SELECT COUNT(*) FROM player_match_stats")->fetchColumn();

    echo 'SEEDED_MATCHES:' . $seededMatches . PHP_EOL;
    echo 'SEEDED_PLAYER_STATS:' . $seededStats . PHP_EOL;
    echo 'TOTAL_MATCHES:' . $matchCount . PHP_EOL;
    echo 'TOTAL_PLAYER_STATS:' . $statCount . PHP_EOL;
} catch (Throwable $e) {
    echo 'ERROR:' . $e->getMessage() . PHP_EOL;
    exit(1);
}
