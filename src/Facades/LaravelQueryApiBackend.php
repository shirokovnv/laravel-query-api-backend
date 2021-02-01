<?php

namespace Shirokovnv\LaravelQueryApiBackend\Facades;

use Illuminate\Support\Facades\Facade;

class LaravelQueryApiBackend extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'laravel-query-api-backend';
    }
}
