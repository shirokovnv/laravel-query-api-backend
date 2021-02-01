<?php


namespace Shirokovnv\LaravelQueryApiBackend\Errors;

use Exception;

class AuthenticationError extends QueryError
{
    public function __construct(Exception $e)
    {
        parent::__construct($e, 'authentication');
    }

    public function getErrorContent(): array
    {
        return [
                'message' => 'Unauthenticated.'
            ] + $this->getErrorTrace();
    }
}
