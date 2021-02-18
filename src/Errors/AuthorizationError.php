<?php

namespace Shirokovnv\LaravelQueryApiBackend\Errors;

use Exception;

/**
 * Class AuthorizationError.
 */
class AuthorizationError extends QueryError
{
    /**
     * AuthorizationError constructor.
     *
     * @param Exception $e
     */
    public function __construct(Exception $e)
    {
        parent::__construct($e, 'authorization');
    }

    /**
     * @return array
     */
    public function getErrorContent(): array
    {
        return [
            'message' => $this->exception->getMessage(),
        ] + $this->getErrorTrace();
    }
}
