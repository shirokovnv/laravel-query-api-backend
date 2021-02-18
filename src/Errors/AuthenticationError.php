<?php

namespace Shirokovnv\LaravelQueryApiBackend\Errors;

use Exception;

/**
 * Class AuthenticationError
 *
 * @package Shirokovnv\LaravelQueryApiBackend\Errors
 */
class AuthenticationError extends QueryError
{
    /**
     * AuthenticationError constructor.
     *
     * @param Exception $e
     */
    public function __construct(Exception $e)
    {
        parent::__construct($e, 'authentication');
    }

    /**
     * @return string[]
     */
    public function getErrorContent(): array
    {
        return [
                'message' => 'Unauthenticated.'
            ] + $this->getErrorTrace();
    }
}
