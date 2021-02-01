<?php


namespace Shirokovnv\LaravelQueryApiBackend\Exceptions;

use Exception;
use Throwable;

class BadArgumentException extends Exception
{
    public function __construct(string $message, $code = 500, Throwable $previous = null)
    {
        parent::__construct("Bad argument: " . $message, $code, $previous);
    }
}
