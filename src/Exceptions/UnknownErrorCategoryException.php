<?php


namespace Shirokovnv\LaravelQueryApiBackend\Exceptions;

use Exception;
use Throwable;

class UnknownErrorCategoryException extends Exception
{
    public function __construct(array $available_categories, Throwable $previous = null)
    {
        $message = "Error must be presented by one of the following categories: " .
            implode(",", $available_categories);
        $code = 500;

        parent::__construct($message, $code, $previous);
    }
}
