<?php

trait Database
{
    private $pdo = null;

    private function connect()
    {
        if ($this->pdo !== null) {
            return $this->pdo;
        }

        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $this->pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
            return $this->pdo;
        } catch (PDOException $e) {
            $msg = $e->getMessage(); 
            error_log($msg);
            return false;
        }
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