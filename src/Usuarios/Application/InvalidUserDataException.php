<?php

namespace Usuarios\Application;

use RuntimeException;

/**
 * Exception thrown when user data doesn't meet validation requirements
 */
class InvalidUserDataException extends RuntimeException
{
    /**
     * @param string $message
     */
    public function __construct(string $message = "Los datos del usuario no son válidos")
    {
        parent::__construct($message);
    }
}
