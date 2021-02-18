<?php

namespace Shirokovnv\LaravelQueryApiBackend\Errors;

use Exception;

/**
 * Class ServerError
 *
 * @package Shirokovnv\LaravelQueryApiBackend\Errors
 */
class ServerError extends QueryError
{
    /**
     * ServerError constructor.
     *
     * @param Exception $e
     */
    public function __construct(Exception $e)
    {
        parent::__construct($e, 'server');
    }

    /**
     * @return array
     */
    public function getErrorContent(): array
    {
        return [
                'exception' => get_class($this->exception),
                'message' => $this->exception->getMessage(),
                'code' => $this->exception->getCode()
            ] + $this->getErrorTrace();
    }
}
