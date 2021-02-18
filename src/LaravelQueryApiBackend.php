<?php

namespace Shirokovnv\LaravelQueryApiBackend;

/**
 * Class LaravelQueryApiBackend.
 */
class LaravelQueryApiBackend
{
    /**
     * Makes instance for query runner.
     * For available options see config file.
     *
     * @param $request
     * @param array $options
     *
     * @return QueryRunner
     */
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
