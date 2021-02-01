<?php


namespace Shirokovnv\LaravelQueryApiBackend\Errors;

use Exception;

class ValidationError extends QueryError
{
    public function __construct(Exception $e)
    {
        parent::__construct($e, 'validation');
    }

    public function getErrorContent(): array
    {
        return [
                'message' => $this->exception->getMessage(),
                'errors' => $this->exception->errors()
            ] + $this->getErrorTrace();
    }
}
