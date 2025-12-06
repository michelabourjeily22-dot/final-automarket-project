<?php

class Database {
    private static $instance = null;
    private $pdo = null;

    private function __construct() {
        try {
            $host = DB_HOST;
            $dbname = DB_NAME;
            $charset = 'utf8mb4';

            $port = defined('DB_PORT') ? DB_PORT : 3306;

            if (strpos($host, ':') === false) {
                $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset={$charset}";
            } else {
                $dsn = "mysql:host={$host};dbname={$dbname};charset={$charset}";
            }

            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            $this->pdo = new PDO($dsn, DB_USER, DB_PASS, $options);

        } catch (PDOException $e) {
            error_log("Database connection failed: " . $e->getMessage());
            error_log("DSN attempted: " . (isset($dsn) ? $dsn : 'N/A'));
            error_log("Error code: " . $e->getCode());
            throw new Exception("Database connection failed: " . $e->getMessage() . " (Code: " . $e->getCode() . ")");
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->pdo;
    }

    private function __clone() {}

    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }
}