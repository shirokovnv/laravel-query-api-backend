<?php


namespace Shirokovnv\LaravelQueryApiBackend\Exceptions;

use Exception;
use Throwable;

class UnknownQueryModeException extends Exception
{
    public const AVAILABLE_QUERY_MODES = [
        'transaction',
        'multiple'
    ];

    public function __construct(Throwable $previous = null)
    {
        $message = "Unknown query mode. Available modes: "
            . implode(",", self::AVAILABLE_QUERY_MODES);
        $code = 500;

        parent::__construct($message, $code, $previous);
    }
}
