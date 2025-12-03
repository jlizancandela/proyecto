<?php

namespace Shared\Infrastructure\Database;

use PDO;
use PDOException;

class Database
{
    private string $host;
    private string $dbname;
    private string $user;
    private string $password;
    private PDO|null $db = null;
    private static Database|null $instance = null;

    private function __construct()
    {
        $this->host = $_ENV["DB_HOST"] ?? "localhost";
        $this->dbname = $_ENV["DB_NAME"] ?? "sistema_reservas";
        $this->user = $_ENV["DB_USER"] ?? "root";
        $this->password = $_ENV["DB_PASSWORD"] ?? "";

        $this->connect();
    }

    public function connect(): void
    {
        try {
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];

            $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset=utf8mb4";

            $this->db = new PDO($dsn, $this->user, $this->password, $options);
        } catch (PDOException $e) {
            throw new \Exception("Connection failed: " . $e->getMessage());
        }
    }

    public function getConnection(): PDO
    {
        return $this->db;
    }

    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }
}
