<?php

/**
 * Error thrown when an email address is already being used.
 */

namespace Usuarios\Application;

/**
 * Exception thrown when an email is already registered
 */
class InvalidEmailException extends \RuntimeException
{
    /**
     * Creates a new invalid email exception with default message
     *
     * @param string $message Custom error message
     * @param int $code Error code
     * @param \Throwable|null $previous Previous exception
     */
    public function __construct(
        string $message = "El email ya está registrado",
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
