<?php


namespace Shirokovnv\LaravelQueryApiBackend\Exceptions;

use Exception;
use Shirokovnv\LaravelQueryApiBackend\Support\Constants;
use Throwable;

class UnknownQueryModeException extends Exception
{
    public function __construct(Throwable $previous = null)
    {
        $message = "Unknown query mode. Available modes: "
            . implode(",", Constants::AVAILABLE_QUERY_MODES);
        $code = 500;

        parent::__construct($message, $code, $previous);
    }
}
