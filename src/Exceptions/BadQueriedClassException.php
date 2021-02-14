<?php


namespace Shirokovnv\LaravelQueryApiBackend\Exceptions;

use Exception;
use Shirokovnv\LaravelQueryApiBackend\Support\Constants;
use Throwable;

class BadQueriedClassException extends Exception
{
    public function __construct($message = "", $code = 422, Throwable $previous = null)
    {
        $prepared_message = $this->prepareMessage() . $message;
        parent::__construct($prepared_message, $code, $previous);
    }

    private function prepareMessage() {
        return "Model class should have one of these ancestors: "
            . implode(",", Constants::AVAILABLE_MODEL_PARENT_CLASSES)
            . ". Custom query class should implement one of these interfaces: "
            . implode(",", Constants::AVAILABLE_CUSTOM_QUERY_INTERFACES);
    }
}
