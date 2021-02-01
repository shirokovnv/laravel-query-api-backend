<?php


namespace Shirokovnv\LaravelQueryApiBackend\Errors;

use Exception;

class AuthorizationError extends QueryError
{
    public function __construct(Exception $e)
    {
        parent::__construct($e, 'authorization');
    }

    public function getErrorContent(): array
    {
        return [
                'message' => $this->exception->getMessage()
            ] + $this->getErrorTrace();
    }
}
