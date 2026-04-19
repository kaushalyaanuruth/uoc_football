<?php
require __DIR__ . '/../app/core/config.php';

try {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    $columns = $pdo->query("SHOW COLUMNS FROM test_results")->fetchAll(PDO::FETCH_ASSOC);
    $fieldMap = [];
    foreach ($columns as $col) {
        $name = (string) ($col['Field'] ?? '');
        if ($name !== '') {
            $fieldMap[strtolower($name)] = $name;
        }
    }

    $pick = function (array $candidates) use ($fieldMap) {
        foreach ($candidates as $candidate) {
            $key = strtolower($candidate);
            if (isset($fieldMap[$key])) {
                return $fieldMap[$key];
            }
        }
        return null;
    };

    $typeCol = $pick(['test_type', 'test_name', 'type']);
    $dateCol = $pick(['date', 'test_date']);
    $scoreCol = $pick(['score', 'test_score', 'result', 'result_value', 'value']);
    $notesCol = $pick(['notes', 'remarks', 'comment', 'description']);
    $playerCol = $pick(['player_id']);
    $teamCol = $pick(['team_id']);

    if (!$typeCol || !$dateCol || !$playerCol) {
        echo "ERROR:Required columns missing in test_results\n";
        echo "COLUMNS:" . implode(',', array_values($fieldMap)) . "\n";
        exit(1);
    }

    $players = $pdo->query(
        "SELECT p.player_id,
                COALESCE(tp.team_id, 1) AS team_id,
                TRIM(CONCAT(COALESCE(u.first_name, ''), ' ', COALESCE(u.last_name, ''))) AS player_name
         FROM players p
         LEFT JOIN users u ON u.nic = p.nic
         LEFT JOIN team_players tp ON tp.player_id = p.player_id
         GROUP BY p.player_id, team_id, player_name
         ORDER BY p.player_id ASC"
    )->fetchAll(PDO::FETCH_ASSOC);

    if (empty($players)) {
        echo "NO_PLAYERS\n";
        exit(0);
    }

    $insertCols = [$typeCol, $dateCol, $playerCol];
    $placeholders = [':type', ':date', ':player_id'];

    if ($scoreCol) {
        $insertCols[] = $scoreCol;
        $placeholders[] = ':score';
    }

    if ($notesCol && strtolower($notesCol) !== strtolower((string) $scoreCol)) {
        $insertCols[] = $notesCol;
        $placeholders[] = ':notes';
    }

    if ($teamCol) {
        $insertCols[] = $teamCol;
        $placeholders[] = ':team_id';
    }

    $sql = "INSERT INTO test_results (" . implode(', ', $insertCols) . ") VALUES (" . implode(', ', $placeholders) . ")";
    $stmt = $pdo->prepare($sql);

    $testTypes = ['bronko', 'yoyo', '2km', '5km'];
    $inserted = 0;

    foreach ($players as $index => $player) {
        $pid = (int) $player['player_id'];
        $teamId = (int) $player['team_id'];
        $name = trim((string) ($player['player_name'] ?? 'Player ' . $pid));

        $type = $testTypes[$index % count($testTypes)];
        $scoreValue = (string) (14 + ($index % 7)) . '.' . (string) (($index % 5) + 1);
        $noteText = 'Dummy seed test for ' . $name;

        $params = [
            'type' => $type,
            'date' => date('Y-m-d'),
            'player_id' => $pid,
        ];

        if ($scoreCol) {
            $params['score'] = $scoreValue;
        }

        if ($notesCol && strtolower($notesCol) !== strtolower((string) $scoreCol)) {
            $params['notes'] = $noteText;
        } elseif ($notesCol && strtolower($notesCol) === strtolower((string) $scoreCol)) {
            $params['score'] = $noteText;
        }

        if ($teamCol) {
            $params['team_id'] = $teamId;
        }

        $stmt->execute($params);
        $inserted++;
    }

    $coverage = $pdo->query(
        "SELECT COUNT(DISTINCT player_id) AS covered_players FROM test_results"
    )->fetch(PDO::FETCH_ASSOC);

    echo 'INSERTED_ROWS:' . $inserted . "\n";
    echo 'TOTAL_PLAYERS:' . count($players) . "\n";
    echo 'COVERED_PLAYERS:' . (int) ($coverage['covered_players'] ?? 0) . "\n";
} catch (Throwable $e) {
    echo 'ERROR:' . $e->getMessage() . "\n";
    exit(1);
}
