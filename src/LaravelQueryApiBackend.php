<?php

namespace Shirokovnv\LaravelQueryApiBackend;

class LaravelQueryApiBackend
{
    public static function makeQueryRunnerInstance($request, array $options = [])
    {
        $isWarningable = $options['isWarningable'] ??
            config('laravel-query-api-backend.options.isWarningable');
        $isLoggable = $options['isLoggable'] ??
            config('laravel-query-api-backend.options.isLoggable');
        $isTraceable = $options['isTraceable'] ??
            config('laravel-query-api-backend.options.isTraceable');

        return new QueryRunner($request, $isTraceable, $isLoggable, $isWarningable);
    }
}
