<?php

/**
 * Error that happens when user information is not valid or complete.
 */

namespace Usuarios\Application;

/**
 * Exception thrown when user data doesn't meet validation requirements
 */
class InvalidUserDataException extends \RuntimeException
{
    /**
     * Creates a new invalid user data exception with default message
     *
     * @param string $message Custom error message
     * @param int $code Error code
     * @param \Throwable|null $previous Previous exception
     */
    public function __construct(
        string $message = "Los datos del usuario no son válidos",
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
