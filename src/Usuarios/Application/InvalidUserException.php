<?php

/**
 * Custom error for when a user cannot be found in the system.
 */

namespace Usuarios\Application;

/**
 * Exception thrown when a user is not found or doesn't exist in the system.
 */
class InvalidUserException extends \RuntimeException
{
    /**
     * Creates a new invalid user exception with default message
     *
     * @param string $message Custom error message
     * @param int $code Error code
     * @param \Throwable|null $previous Previous exception
     */
    public function __construct(
        string $message = "Usuario no encontrado",
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
