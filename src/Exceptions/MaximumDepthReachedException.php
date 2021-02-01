<?php


namespace Shirokovnv\LaravelQueryApiBackend\Exceptions;

use Exception;
use Throwable;

class MaximumDepthReachedException extends Exception
{
    public function __construct(Throwable $previous = null)
    {
        $message = "Maximum nested level reached.";
        $code = 500;

        parent::__construct($message, $code, $previous);
    }
}
