<?php
require __DIR__ . '/../app/core/config.php';

try {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    $rows = $pdo->query(
        "SELECT p.player_id,
                TRIM(CONCAT(COALESCE(u.first_name, ''), ' ', COALESCE(u.last_name, ''))) AS player_name,
                COUNT(tr.player_id) AS result_count
         FROM players p
         LEFT JOIN users u ON u.nic = p.nic
         LEFT JOIN test_results tr ON tr.player_id = p.player_id
         GROUP BY p.player_id, player_name
         ORDER BY p.player_id ASC"
    )->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($rows, JSON_PRETTY_PRINT) . PHP_EOL;
} catch (Throwable $e) {
    echo 'ERROR:' . $e->getMessage() . PHP_EOL;
    exit(1);
}
