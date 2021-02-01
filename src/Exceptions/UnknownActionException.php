<?php


namespace Shirokovnv\LaravelQueryApiBackend\Exceptions;

use Exception;
use Throwable;

class UnknownActionException extends Exception
{
    public function __construct(string $action_name, Throwable $previous = null)
    {
        $message = "Unknown action $action_name";
        $code = 500;

        parent::__construct($message, $code, $previous);
    }
}
