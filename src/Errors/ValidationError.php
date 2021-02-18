<?php

namespace Shirokovnv\LaravelQueryApiBackend\Errors;

use Exception;

/**
 * Class ValidationError.
 */
class ValidationError extends QueryError
{
    /**
     * ValidationError constructor.
     *
     * @param Exception $e
     */
    public function __construct(Exception $e)
    {
        parent::__construct($e, 'validation');
    }

    /**
     * @return array
     */
    public function getErrorContent(): array
    {
        return [
            'message' => $this->exception->getMessage(),
            'errors' => $this->exception->errors(),
        ] + $this->getErrorTrace();
    }
}
