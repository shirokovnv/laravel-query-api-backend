<?php

namespace Shirokovnv\LaravelQueryApiBackend\Errors;

use Exception;

/**
 * Class ClientError.
 */
class ClientError extends QueryError
{
    /**
     * ClientError constructor.
     *
     * @param Exception $e
     */
    public function __construct(Exception $e)
    {
        parent::__construct($e, 'client');
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
