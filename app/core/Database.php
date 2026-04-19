<?php

trait Database
{
    private $pdo = null;

    private function connect()
    {
        if ($this->pdo !== null) {
            return $this->pdo;
        }

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        $hosts = array_values(array_unique([DB_HOST, 'localhost', '127.0.0.1']));

        $credentials = [
            ['user' => DB_USER, 'pass' => DB_PASS],
        ];

        // Common XAMPP local default: root with empty password.
        if (strtolower((string) DB_USER) === 'root' && (string) DB_PASS !== '') {
            $credentials[] = ['user' => 'root', 'pass' => ''];
        }

        $lastError = null;

        foreach ($hosts as $host) {
            $dsn = "mysql:host=" . $host . ";dbname=" . DB_NAME . ";charset=utf8mb4";

            foreach ($credentials as $credential) {
                try {
                    $this->pdo = new PDO($dsn, $credential['user'], $credential['pass'], $options);
                    return $this->pdo;
                } catch (PDOException $e) {
                    $lastError = $e;
                }
            }
        }

        if ($lastError instanceof PDOException) {
            error_log('Database connection failed: ' . $lastError->getMessage());
        }

        return false;
    }

    public function query($query, $data = [])
    {
        $connection = $this->connect();
        if (!$connection) {
            error_log("Database connection failed in query()");
            throw new Exception("Database connection failed");
        }

        try {
            $stmt = $connection->prepare($query);
            $result = $stmt->execute($data);
            
            $rows = $stmt->fetchAll();
            return $rows ?: [];
        } catch (PDOException $e) {
            error_log("PDO Error: " . $e->getMessage());
            error_log("Query: " . $query);
            error_log("Params: " . print_r($data, true));
            throw new Exception("Database query failed: " . $e->getMessage());
        }
    }

    public function lastInsertId()
    {
        $connection = $this->connect();
        if (!$connection) {
            return false;
        }
        return $connection->lastInsertId();
    }
}