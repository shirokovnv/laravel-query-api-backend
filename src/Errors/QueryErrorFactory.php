<?php

namespace Shirokovnv\LaravelQueryApiBackend\Errors;

use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;

class QueryErrorFactory
{
    public static function createFor(Exception $e)
    {

        if ($e instanceof ValidationException) {
            return new ValidationError($e);
        }

        if ($e instanceof AuthenticationException) {
            return new AuthenticationError($e);
        }

        if ($e instanceof ModelNotFoundException) {
            return new ClientError($e);
        }

        if ($e instanceof AuthorizationException) {
            return new AuthorizationError($e);
        }

        if ($e instanceof QueryException) {
            return new DatabaseError($e);
        }

        if ($e->getCode() === 401) {
            return new AuthenticationError($e);
        }

        if ($e->getCode() === 404) {
            return new ClientError($e);
        }

        if ($e->getCode() === 403) {
            return new AuthorizationError($e);
        }

        if ($e->getCode() === 422) {
            return new ValidationError($e);
        }

        if ($e->getCode() === 500) {
            return new ServerError($e);
        }

        return new ServerError($e);
    }
}
