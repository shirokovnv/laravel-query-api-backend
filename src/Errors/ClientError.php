<?php

namespace Shirokovnv\LaravelQueryApiBackend\Errors;

use Exception;

class ClientError extends QueryError
{
    public function __construct(Exception $e)
    {
        parent::__construct($e, 'client');
    }

    public function getErrorContent(): array
    {
        return [
                'message' => $this->exception->getMessage(),
            ] + $this->getErrorTrace();
    }
}
