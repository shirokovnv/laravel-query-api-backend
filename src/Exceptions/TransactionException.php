<?php


namespace Shirokovnv\LaravelQueryApiBackend\Exceptions;

use Exception;
use Throwable;

class TransactionException extends Exception
{
    public function __construct(string $transaction_id, Throwable $previous = null)
    {
        $message = "Cannot execute transaction with id $transaction_id. Transaction rolled back.";
        $code = 500;

        parent::__construct($message, $code, $previous);
    }
}
