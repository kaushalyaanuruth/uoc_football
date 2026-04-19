<?php
require __DIR__ . '/../app/core/config.php';

try {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    $matches = $pdo->query(
        "SELECT result_id, opponent_team, result, goals_scored, goals_conceded, date, team_id
         FROM match_results
         ORDER BY result_id DESC
         LIMIT 5"
    )->fetchAll(PDO::FETCH_ASSOC);

    $coverage = $pdo->query(
        "SELECT pms.match_id, COUNT(*) AS player_rows
         FROM player_match_stats pms
         GROUP BY pms.match_id
         ORDER BY pms.match_id DESC
         LIMIT 5"
    )->fetchAll(PDO::FETCH_ASSOC);

    echo 'LATEST_MATCHES=' . json_encode($matches) . PHP_EOL;
    echo 'PLAYER_ROWS_PER_MATCH=' . json_encode($coverage) . PHP_EOL;
} catch (Throwable $e) {
    echo 'ERROR:' . $e->getMessage() . PHP_EOL;
    exit(1);
}
