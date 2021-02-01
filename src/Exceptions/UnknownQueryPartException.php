<?php


namespace Shirokovnv\LaravelQueryApiBackend\Exceptions;

use Exception;
use Throwable;

class UnknownQueryPartException extends Exception
{
    public function __construct(string $kind_of_query_part, Throwable $previous = null)
    {
        $message = "Unknown query part $kind_of_query_part";
        $code = 500;

        parent::__construct($message, $code, $previous);
    }
}
