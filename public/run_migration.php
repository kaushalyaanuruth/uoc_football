<?php
// Database configuration
const DB_HOST = 'localhost';
const DB_USER = 'root';
const DB_PASS = '';
const DB_NAME = 'UOC_FOOTBALL';

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
    ]);
    
    // Read and execute migration
    $sql = file_get_contents(__DIR__ . '/migration_fix_test_results.sql');
    $statements = array_filter(array_map('trim', explode(';', $sql)), fn($s) => !empty($s) && !str_starts_with($s, '--'));
    
    foreach ($statements as $statement) {
        if (!empty($statement)) {
            $pdo->exec($statement . ';');
            echo "✓ Executed: " . substr($statement, 0, 50) . "...\n";
        }
    }
    
    echo "\n✓ Database migration completed successfully!\n";
} catch (PDOException $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    exit(1);
}
