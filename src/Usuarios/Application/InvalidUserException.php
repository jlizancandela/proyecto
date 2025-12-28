<?php

namespace Usuarios\Application;

/**
 * Exception thrown when a user is not found or doesn't exist in the system.
 */
class InvalidUserException extends \RuntimeException
{
    /**
     * @param string $message
     */
    public function __construct(string $message = "Usuario no encontrado")
    {
        parent::__construct($message);
    }
}
