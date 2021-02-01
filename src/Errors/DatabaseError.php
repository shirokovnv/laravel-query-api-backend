<?php


namespace Shirokovnv\LaravelQueryApiBackend\Errors;

use Exception;

class DatabaseError extends QueryError
{
    public function __construct(Exception $e)
    {
        parent::__construct($e, 'database');
    }

    public function getErrorContent(): array
    {
        return [
                'message' => $this->exception->getMessage(),
                'code' => $this->exception->getCode()
            ] + $this->getErrorTrace();
    }
}
