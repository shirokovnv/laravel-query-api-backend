<?php

namespace Shirokovnv\LaravelQueryApiBackend\Errors;

use Exception;

/**
 * Class DatabaseError
 *
 * @package Shirokovnv\LaravelQueryApiBackend\Errors
 */
class DatabaseError extends QueryError
{
    /**
     * DatabaseError constructor.
     *
     * @param Exception $e
     */
    public function __construct(Exception $e)
    {
        parent::__construct($e, 'database');
    }

    /**
     * @return array
     */
    public function getErrorContent(): array
    {
        return [
                'message' => $this->exception->getMessage(),
                'code' => $this->exception->getCode()
            ] + $this->getErrorTrace();
    }
}
