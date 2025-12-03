<?php

namespace Shared\Domain\Exceptions;

class InvalidValidation extends \Exception
{
    public function __construct($message = "InvalidValidation")
    {
        parent::__construct($message);
    }
}
