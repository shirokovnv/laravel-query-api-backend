<?php


namespace Shirokovnv\LaravelQueryApiBackend\Errors;

use Exception;

class ServerError extends QueryError
{
    public function __construct(Exception $e)
    {
        parent::__construct($e, 'server');
    }

    public function getErrorContent(): array
    {
        return [
                'exception' => get_class($this->exception),
                'message' => $this->exception->getMessage(),
                'code' => $this->exception->getCode()
            ] + $this->getErrorTrace();
    }
}
