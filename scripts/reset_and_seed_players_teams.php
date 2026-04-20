<?php
require __DIR__ . '/../app/core/config.php';

function tableExists(PDO $pdo, $table)
{
    $stmt = $pdo->prepare(
        "SELECT COUNT(*) FROM information_schema.TABLES WHERE TABLE_SCHEMA = :schema AND TABLE_NAME = :table"
    );
    $stmt->execute([
        'schema' => DB_NAME,
        'table' => $table,
    ]);

    return ((int) $stmt->fetchColumn()) > 0;
}

function columnMap(PDO $pdo, $table)
{
    if (!tableExists($pdo, $table)) {
        return [];
    }

    $rows = $pdo->query("SHOW COLUMNS FROM {$table}")->fetchAll(PDO::FETCH_ASSOC);
    $map = [];
    foreach ($rows as $row) {
        $field = (string) ($row['Field'] ?? '');
        if ($field !== '') {
            $map[strtolower($field)] = $field;
        }
    }

    return $map;
}

function pickColumn(array $map, array $candidates)
{
    foreach ($candidates as $candidate) {
        $key = strtolower($candidate);
        if (isset($map[$key])) {
            return $map[$key];
        }
    }

    return null;
}

function execDelete(PDO $pdo, &$counter, $table, $where = '', array $params = [])
{
    if (!tableExists($pdo, $table)) {
        return;
    }

    $sql = "DELETE FROM {$table}";
    if ($where !== '') {
        $sql .= " WHERE {$where}";
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $counter += $stmt->rowCount();
}

try {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    $pdo->beginTransaction();

    $summary = [
        'RESET_OLD_PLAYERS' => 0,
        'RESET_OLD_TEAMS' => 0,
        'INSERTED_PLAYERS' => 0,
        'INSERTED_TEAMS' => 0,
        'INSERTED_TEST_RESULTS' => 0,
        'INSERTED_MATCH_RESULTS' => 0,
        'INSERTED_PLAYER_MATCH_STATS' => 0,
    ];

    $oldPlayerNics = [];
    if (tableExists($pdo, 'players')) {
        $rows = $pdo->query("SELECT nic FROM players")->fetchAll(PDO::FETCH_ASSOC);
        foreach ($rows as $row) {
            $nic = trim((string) ($row['nic'] ?? ''));
            if ($nic !== '') {
                $oldPlayerNics[] = $nic;
            }
        }
        $summary['RESET_OLD_PLAYERS'] = count($oldPlayerNics);
    }

    if (tableExists($pdo, 'teams')) {
        $summary['RESET_OLD_TEAMS'] = (int) $pdo->query("SELECT COUNT(*) FROM teams")->fetchColumn();
    }

    // Delete in FK-safe order.
    $tmpDeleted = 0;
    execDelete($pdo, $tmpDeleted, 'player_match_stats');
    $tmpDeleted = 0;
    execDelete($pdo, $tmpDeleted, 'match_results');
    $tmpDeleted = 0;
    execDelete($pdo, $tmpDeleted, 'test_results');

    $tmpDeleted = 0;
    execDelete($pdo, $tmpDeleted, 'captains');
    $tmpDeleted = 0;
    execDelete($pdo, $tmpDeleted, 'team_players');
    $tmpDeleted = 0;
    execDelete($pdo, $tmpDeleted, 'team_coaches');
    $tmpDeleted = 0;
    execDelete($pdo, $tmpDeleted, 'inventory_log');

    $tmpDeleted = 0;
    execDelete($pdo, $tmpDeleted, 'incomes');
    $tmpDeleted = 0;
    execDelete($pdo, $tmpDeleted, 'expenses');
    $tmpDeleted = 0;
    execDelete($pdo, $tmpDeleted, 'budgets');

    $tmpDeleted = 0;
    execDelete($pdo, $tmpDeleted, 'breakfast');
    $tmpDeleted = 0;
    execDelete($pdo, $tmpDeleted, 'lunch');
    $tmpDeleted = 0;
    execDelete($pdo, $tmpDeleted, 'dinner');
    $tmpDeleted = 0;
    execDelete($pdo, $tmpDeleted, 'meal_plans');

    $tmpDeleted = 0;
    execDelete($pdo, $tmpDeleted, 'tournaments');
    $tmpDeleted = 0;
    execDelete($pdo, $tmpDeleted, 'achievements');
    $tmpDeleted = 0;
    execDelete($pdo, $tmpDeleted, 'Achievements');

    $tmpDeleted = 0;
    execDelete($pdo, $tmpDeleted, 'players');
    $tmpDeleted = 0;
    execDelete($pdo, $tmpDeleted, 'teams');

    // Remove only users who belonged to old players.
    if (!empty($oldPlayerNics) && tableExists($pdo, 'users')) {
        $hasInventoryItems = tableExists($pdo, 'inventory_items');
        $deleteUserStmt = $pdo->prepare(
            "DELETE FROM users
             WHERE nic = :nic
               AND nic NOT IN (SELECT nic FROM admins)
               AND nic NOT IN (SELECT nic FROM coaches)"
        );

        $isUsedByInventoryStmt = null;
        if ($hasInventoryItems) {
            $isUsedByInventoryStmt = $pdo->prepare(
                "SELECT COUNT(*) FROM inventory_items WHERE updated_by = :nic"
            );
        }

        foreach ($oldPlayerNics as $nic) {
            if ($isUsedByInventoryStmt) {
                $isUsedByInventoryStmt->execute(['nic' => $nic]);
                if ((int) $isUsedByInventoryStmt->fetchColumn() > 0) {
                    continue;
                }
            }
            $deleteUserStmt->execute(['nic' => $nic]);
        }
    }

    // Insert teams.
    $presentTeamId = null;
    $pastTeamId = null;
    if (tableExists($pdo, 'teams')) {
        $teamMap = columnMap($pdo, 'teams');
        $seasonCol = pickColumn($teamMap, ['season']);
        $statusCol = pickColumn($teamMap, ['status']);

        if ($seasonCol && $statusCol) {
            $insertTeamStmt = $pdo->prepare("INSERT INTO teams ({$seasonCol}, {$statusCol}) VALUES (:season, :status)");
            $insertTeamStmt->execute([
                'season' => '2026',
                'status' => 'present',
            ]);
            $presentTeamId = (int) $pdo->lastInsertId();

            $insertTeamStmt->execute([
                'season' => '2025',
                'status' => 'past',
            ]);
            $pastTeamId = (int) $pdo->lastInsertId();
        } elseif ($seasonCol) {
            $insertTeamStmt = $pdo->prepare("INSERT INTO teams ({$seasonCol}) VALUES (:season)");
            $insertTeamStmt->execute(['season' => '2026']);
            $presentTeamId = (int) $pdo->lastInsertId();

            $insertTeamStmt->execute(['season' => '2025']);
            $pastTeamId = (int) $pdo->lastInsertId();
        } else {
            $pdo->exec("INSERT INTO teams () VALUES ()");
            $presentTeamId = (int) $pdo->lastInsertId();

            $pdo->exec("INSERT INTO teams () VALUES ()");
            $pastTeamId = (int) $pdo->lastInsertId();
        }

        $summary['INSERTED_TEAMS'] = 2;
    }

    // Realistic fictional Sri Lankan sample players.
    $samplePlayers = [
        ['nic' => '200102145678', 'first_name' => 'Nimesh', 'last_name' => 'Perera', 'email' => 'nimesh.perera@uocfootball.lk', 'phone' => '0771234567', 'position' => 'Goalkeeper', 'role' => 'Captain'],
        ['nic' => '200211236789', 'first_name' => 'Sahan', 'last_name' => 'Fernando', 'email' => 'sahan.fernando@uocfootball.lk', 'phone' => '0772345678', 'position' => 'Defender', 'role' => 'Vice-Captain'],
        ['nic' => '200323457891', 'first_name' => 'Kavindu', 'last_name' => 'Silva', 'email' => 'kavindu.silva@uocfootball.lk', 'phone' => '0773456789', 'position' => 'Defender', 'role' => 'Player'],
        ['nic' => '200434568912', 'first_name' => 'Pasindu', 'last_name' => 'Jayasinghe', 'email' => 'pasindu.jayasinghe@uocfootball.lk', 'phone' => '0774567890', 'position' => 'Defender', 'role' => 'Player'],
        ['nic' => '200545679123', 'first_name' => 'Tharindu', 'last_name' => 'Gunasekara', 'email' => 'tharindu.gunasekara@uocfootball.lk', 'phone' => '0775678901', 'position' => 'Midfielder', 'role' => 'Player'],
        ['nic' => '200656781234', 'first_name' => 'Dinuka', 'last_name' => 'Dissanayake', 'email' => 'dinuka.dissanayake@uocfootball.lk', 'phone' => '0776789012', 'position' => 'Midfielder', 'role' => 'Player'],
        ['nic' => '200767892345', 'first_name' => 'Shehan', 'last_name' => 'Karunaratne', 'email' => 'shehan.karunaratne@uocfootball.lk', 'phone' => '0777890123', 'position' => 'Midfielder', 'role' => 'Player'],
        ['nic' => '200878903456', 'first_name' => 'Ravindu', 'last_name' => 'Wijesinghe', 'email' => 'ravindu.wijesinghe@uocfootball.lk', 'phone' => '0778901234', 'position' => 'Forward', 'role' => 'Player'],
        ['nic' => '200989014567', 'first_name' => 'Isuru', 'last_name' => 'Nawarathne', 'email' => 'isuru.nawarathne@uocfootball.lk', 'phone' => '0779012345', 'position' => 'Forward', 'role' => 'Player'],
        ['nic' => '201090125678', 'first_name' => 'Madura', 'last_name' => 'Amarasinghe', 'email' => 'madura.amarasinghe@uocfootball.lk', 'phone' => '0780123456', 'position' => 'Forward', 'role' => 'Player'],
        ['nic' => '201101236789', 'first_name' => 'Gayan', 'last_name' => 'Rathnayake', 'email' => 'gayan.rathnayake@uocfootball.lk', 'phone' => '0781234567', 'position' => 'Goalkeeper', 'role' => 'Player'],
    ];

    $hashedPassword = password_hash('123456', PASSWORD_BCRYPT);

    $insertUserStmt = null;
    if (tableExists($pdo, 'users')) {
        $insertUserStmt = $pdo->prepare(
            "INSERT INTO users (nic, user_id, password, first_name, last_name, email, phone_number)
             VALUES (:nic, :user_id, :password, :first_name, :last_name, :email, :phone_number)"
        );
    }

    $insertPlayerStmt = null;
    if (tableExists($pdo, 'players')) {
        $insertPlayerStmt = $pdo->prepare(
            "INSERT INTO players (position, role, nic) VALUES (:position, :role, :nic)"
        );
    }

    $insertTeamPlayerStmt = null;
    if (tableExists($pdo, 'team_players')) {
        $insertTeamPlayerStmt = $pdo->prepare(
            "INSERT INTO team_players (team_id, player_id) VALUES (:team_id, :player_id)"
        );
    }

    $captainPlayerId = null;
    $playerRows = [];

    foreach ($samplePlayers as $index => $player) {
        if ($insertUserStmt) {
            $insertUserStmt->execute([
                'nic' => $player['nic'],
                'user_id' => $player['nic'],
                'password' => $hashedPassword,
                'first_name' => $player['first_name'],
                'last_name' => $player['last_name'],
                'email' => $player['email'],
                'phone_number' => $player['phone'],
            ]);
        }

        if ($insertPlayerStmt) {
            $insertPlayerStmt->execute([
                'position' => $player['position'],
                'role' => $player['role'],
                'nic' => $player['nic'],
            ]);

            $playerId = (int) $pdo->lastInsertId();
            $summary['INSERTED_PLAYERS']++;

            $playerRows[] = [
                'player_id' => $playerId,
                'position' => $player['position'],
                'role' => $player['role'],
                'name' => $player['first_name'] . ' ' . $player['last_name'],
            ];

            if ($player['role'] === 'Captain') {
                $captainPlayerId = $playerId;
            }

            if ($insertTeamPlayerStmt && $presentTeamId !== null) {
                $insertTeamPlayerStmt->execute([
                    'team_id' => $presentTeamId,
                    'player_id' => $playerId,
                ]);
            }
        }
    }

    if ($captainPlayerId !== null && tableExists($pdo, 'captains')) {
        $insertCaptainStmt = $pdo->prepare("INSERT INTO captains (player_id) VALUES (:player_id)");
        $insertCaptainStmt->execute(['player_id' => $captainPlayerId]);
    }

    // Seed test_results with dynamic schema mapping (2 rows per player).
    if (tableExists($pdo, 'test_results') && !empty($playerRows)) {
        $map = columnMap($pdo, 'test_results');
        $typeCol = pickColumn($map, ['test_type', 'test_name', 'type']);
        $dateCol = pickColumn($map, ['date', 'test_date']);
        $scoreCol = pickColumn($map, ['score', 'test_score', 'result', 'result_value', 'value']);
        $notesCol = pickColumn($map, ['notes', 'remarks', 'comment', 'description']);
        $playerCol = pickColumn($map, ['player_id']);
        $teamCol = pickColumn($map, ['team_id']);

        if ($typeCol && $dateCol && $playerCol) {
            $insertCols = [$typeCol, $dateCol, $playerCol];
            $valueCols = [':test_type', ':test_date', ':player_id'];

            if ($scoreCol) {
                $insertCols[] = $scoreCol;
                $valueCols[] = ':score';
            }
            if ($notesCol && strtolower($notesCol) !== strtolower((string) $scoreCol)) {
                $insertCols[] = $notesCol;
                $valueCols[] = ':notes';
            }
            if ($teamCol) {
                $insertCols[] = $teamCol;
                $valueCols[] = ':team_id';
            }

            $insertTestStmt = $pdo->prepare(
                'INSERT INTO test_results (' . implode(', ', $insertCols) . ') VALUES (' . implode(', ', $valueCols) . ')'
            );

            $testTypes = ['YoYo', 'Sprint30m', 'Bronco', '2km Run'];
            foreach ($playerRows as $idx => $row) {
                for ($round = 0; $round < 2; $round++) {
                    $scoreValue = (string) (14 + (($idx + $round) % 6)) . '.' . (string) (1 + (($idx + $round) % 8));
                    $params = [
                        'test_type' => $testTypes[($idx + $round) % count($testTypes)],
                        'test_date' => date('Y-m-d', strtotime('-' . (14 - ($round * 6)) . ' days')),
                        'player_id' => $row['player_id'],
                    ];

                    if ($scoreCol) {
                        $params['score'] = $scoreValue;
                    }
                    if ($notesCol && strtolower($notesCol) !== strtolower((string) $scoreCol)) {
                        $params['notes'] = 'Performance test for ' . $row['name'];
                    }
                    if ($teamCol) {
                        $params['team_id'] = $presentTeamId ?: 1;
                    }

                    $insertTestStmt->execute($params);
                    $summary['INSERTED_TEST_RESULTS']++;
                }
            }
        }
    }

    // Seed match_results and player_match_stats.
    $matchResultIds = [];
    if (tableExists($pdo, 'match_results') && $presentTeamId !== null) {
        $insertMatchStmt = $pdo->prepare(
            "INSERT INTO match_results (
                opponent_team, result, goals_scored, goals_conceded, shots, shots_on_target,
                possession, passes, passes_accuracy, corners, date, notes, team_id
            ) VALUES (
                :opponent_team, :result, :goals_scored, :goals_conceded, :shots, :shots_on_target,
                :possession, :passes, :passes_accuracy, :corners, :date, :notes, :team_id
            )"
        );

        $fixtures = [
            ['opponent' => 'Royal College', 'result' => 'Won',  'gf' => 2, 'ga' => 1],
            ['opponent' => 'St Joseph\'s College', 'result' => 'Draw', 'gf' => 1, 'ga' => 1],
            ['opponent' => 'Nalanda College', 'result' => 'Won', 'gf' => 3, 'ga' => 0],
            ['opponent' => 'Ananda College', 'result' => 'Lost', 'gf' => 0, 'ga' => 1],
        ];

        foreach ($fixtures as $i => $match) {
            $insertMatchStmt->execute([
                'opponent_team' => $match['opponent'],
                'result' => $match['result'],
                'goals_scored' => $match['gf'],
                'goals_conceded' => $match['ga'],
                'shots' => 10 + ($i * 2),
                'shots_on_target' => 4 + $i,
                'possession' => 48 + ($i * 3),
                'passes' => 330 + ($i * 26),
                'passes_accuracy' => 76 + ($i * 1.5),
                'corners' => 4 + $i,
                'date' => date('Y-m-d', strtotime('-' . ((4 - $i) * 7) . ' days')),
                'notes' => 'Seeded match result',
                'team_id' => $presentTeamId,
            ]);

            $matchResultIds[] = (int) $pdo->lastInsertId();
            $summary['INSERTED_MATCH_RESULTS']++;
        }
    }

    if (tableExists($pdo, 'player_match_stats') && !empty($matchResultIds) && !empty($playerRows)) {
        $insertStatStmt = $pdo->prepare(
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

        foreach ($matchResultIds as $mIndex => $matchId) {
            foreach ($playerRows as $pIndex => $row) {
                $started = $pIndex < 8;
                $sub = !$started && $pIndex < 10;
                $minutes = $started ? 90 : ($sub ? 25 : 0);
                $status = $started ? 'Started' : ($sub ? 'Substitute' : 'Unused');

                $insertStatStmt->execute([
                    'match_id' => $matchId,
                    'player_id' => $row['player_id'],
                    'position_played' => $row['position'],
                    'minutes_played' => $minutes,
                    'substitution_status' => $status,
                    'goals_scored' => ($row['position'] === 'Forward' && $started && (($mIndex + $pIndex) % 5 === 0)) ? 1 : 0,
                    'assists' => ($row['position'] === 'Midfielder' && $started && (($mIndex + $pIndex) % 4 === 0)) ? 1 : 0,
                    'shots_on_target' => ($row['position'] === 'Forward' && $started) ? (1 + (($mIndex + $pIndex) % 3)) : 0,
                    'shots_off_target' => ($row['position'] === 'Forward' && $started) ? (($mIndex + $pIndex) % 2) : 0,
                    'key_passes' => ($row['position'] === 'Midfielder') ? (1 + (($mIndex + $pIndex) % 4)) : 0,
                    'successful_dribbles' => ($row['position'] !== 'Goalkeeper' && $started) ? (($mIndex + $pIndex) % 3) : 0,
                    'completed_passes' => $minutes === 0 ? 0 : (18 + (($mIndex + $pIndex) % 26)),
                    'line_breaking_passes' => ($row['position'] === 'Midfielder') ? (($mIndex + $pIndex) % 3) : 0,
                    'tackles_won' => ($row['position'] === 'Defender') ? (1 + (($mIndex + $pIndex) % 5)) : 0,
                    'interceptions' => ($row['position'] === 'Defender') ? (($mIndex + $pIndex) % 3) : 0,
                    'defensive_duels_won' => ($row['position'] === 'Defender') ? (1 + (($mIndex + $pIndex) % 4)) : 0,
                    'aerial_duels_won' => ($row['position'] === 'Defender') ? (($mIndex + $pIndex) % 3) : 0,
                    'yellow_cards' => (($mIndex + $pIndex) % 10 === 0) ? 1 : 0,
                    'red_cards' => 0,
                    'fouls_committed' => ($minutes > 0) ? (($mIndex + $pIndex) % 2) : 0,
                    'fouls_won' => ($minutes > 0) ? (($mIndex + $pIndex + 1) % 2) : 0,
                    'notes' => 'Seeded realistic sample stat',
                ]);

                $summary['INSERTED_PLAYER_MATCH_STATS']++;
            }
        }
    }

    $pdo->commit();

    echo 'RESET_OLD_PLAYERS: ' . $summary['RESET_OLD_PLAYERS'] . PHP_EOL;
    echo 'RESET_OLD_TEAMS: ' . $summary['RESET_OLD_TEAMS'] . PHP_EOL;
    echo 'INSERTED_PLAYERS: ' . $summary['INSERTED_PLAYERS'] . PHP_EOL;
    echo 'INSERTED_TEAMS: ' . $summary['INSERTED_TEAMS'] . PHP_EOL;
    echo 'INSERTED_TEST_RESULTS: ' . $summary['INSERTED_TEST_RESULTS'] . PHP_EOL;
    echo 'INSERTED_MATCH_RESULTS: ' . $summary['INSERTED_MATCH_RESULTS'] . PHP_EOL;
    echo 'INSERTED_PLAYER_MATCH_STATS: ' . $summary['INSERTED_PLAYER_MATCH_STATS'] . PHP_EOL;
} catch (Throwable $e) {
    if (isset($pdo) && $pdo instanceof PDO && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo 'ERROR: ' . $e->getMessage() . PHP_EOL;
    exit(1);
}
