<?php

namespace Shirokovnv\LaravelQueryApiBackend\Exceptions;

use Exception;
use Throwable;

class AccessDeniedException extends Exception
{
    public function __construct(string $what_denied_message, Throwable $previous = null)
    {
        $message = "You don't have access " . $what_denied_message;
        $code = 403;
        parent::__construct($message, $code, $previous);
    }
}
