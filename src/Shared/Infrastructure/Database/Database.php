<?php

/**
 * Database
 *
 * Handles the database connection for the application.
 * Provides a singleton-like access to the PDO instance.
 */

namespace Shared\Infrastructure\Database;

use PDO;
use PDOException;

class Database
{
    private PDO $db;

    /**
     * Database constructor.
     * Initializes the PDO database connection using environment variables.
     * @throws \Exception If the database connection fails.
     */
    public function __construct()
    {
        $host = $_ENV["DB_HOST"] ?? "localhost";
        $dbname = $_ENV["DB_NAME"] ?? "sistema_reservas";
        $user = $_ENV["DB_USER"] ?? "root";
        $password = $_ENV["DB_PASSWORD"] ?? "";

        try {
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];

            $dsn = "mysql:host={$host};dbname={$dbname};charset=utf8mb4";
            $this->db = new PDO($dsn, $user, $password, $options);
        } catch (PDOException $e) {
            throw new \Exception("Connection failed: " . $e->getMessage());
        }
    }

    /**
     * Retrieves the PDO database connection instance.
     * @return PDO The PDO database connection.
     */
    public function getConnection(): PDO
    {
        return $this->db;
    }
}
