<?php

/**
 * InvalidValidation
 *
 * Custom exception class for invalid validation scenarios.
 */

namespace Shared\Domain\Exceptions;

class InvalidValidation extends \Exception
{
    /**
     * InvalidValidation constructor.
     * @param string $message The exception message.
     */
    public function __construct($message = "InvalidValidation")
    {
        parent::__construct($message);
    }
}
