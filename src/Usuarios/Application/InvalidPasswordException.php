<?php

namespace Usuarios\Application;

/**
 * Exception thrown when a password doesn't meet security requirements
 */
class InvalidPasswordException extends \RuntimeException
{
    /**
     * Creates a new invalid password exception with default message
     *
     * @param string $message Custom error message
     * @param int $code Error code
     * @param \Throwable|null $previous Previous exception
     */
    public function __construct(
        string $message = "La contraseña debe tener al menos 8 caracteres, una letra mayúscula, una letra minúscula, un número y un carácter especial (@$!%*?&#.,;:-_+)",
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
