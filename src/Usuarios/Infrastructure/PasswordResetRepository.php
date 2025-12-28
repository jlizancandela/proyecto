<?php

/**
 * Handles temporary tokens for users who forget their passwords.
 */

namespace Usuarios\Infrastructure;

use PDO;
use PDOException;
use Usuarios\Domain\Usuario;

/**
 * Manages all database actions for password reset tokens.
 */
class PasswordResetRepository
{
    private PDO $db;

    /**
     * @param PDO $db The PDO database connection.
     */
    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * @param int $userId User ID
     * @param string $token Reset token
     * @param string $expiration Expiration date (format: Y-m-d H:i:s)
     * @return void
     * @throws PDOException
     */
    public function savePasswordResetToken(int $userId, string $token, string $expiration): void
    {
        $stmt = $this->db->prepare(
            "UPDATE usuarios
            SET reset_token = :token,
                reset_token_expiration = :expiration
            WHERE id = :userId"
        );

        $stmt->execute([
            ':token' => $token,
            ':expiration' => $expiration,
            ':userId' => $userId
        ]);
    }

    /**
     * @param string $token Reset token
     * @return Usuario|null
     * @throws PDOException
     */
    public function getUserByResetToken(string $token): ?Usuario
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM usuarios
            WHERE reset_token = :token
            AND reset_token_expiration > NOW()"
        );

        $stmt->execute([':token' => $token]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? Usuario::fromDatabase($row) : null;
    }

    /**
     * @param int $userId User ID
     * @return void
     * @throws PDOException
     */
    public function clearResetToken(int $userId): void
    {
        $stmt = $this->db->prepare(
            "UPDATE usuarios
            SET reset_token = NULL,
                reset_token_expiration = NULL
            WHERE id = :userId"
        );

        $stmt->execute([':userId' => $userId]);
    }
}
