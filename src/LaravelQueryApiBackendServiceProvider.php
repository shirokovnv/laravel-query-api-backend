<?php

namespace Shirokovnv\LaravelQueryApiBackend;

use Illuminate\Support\ServiceProvider;

/**
 * Class LaravelQueryApiBackendServiceProvider
 *
 * @package Shirokovnv\LaravelQueryApiBackend
 */
class LaravelQueryApiBackendServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        // $this->loadRoutesFrom(__DIR__.'/routes.php');

        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/laravel-query-api-backend.php',
            'laravel-query-api-backend'
        );

        // Register the service the package provides.
        $this->app->singleton('laravel-query-api-backend', function ($app) {
            return new LaravelQueryApiBackend();
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['laravel-query-api-backend'];
    }

    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole(): void
    {
        // Publishing the configuration file.
        $this->publishes(
            [
                __DIR__ . '/../config/laravel-query-api-backend.php'
                    => config_path('laravel-query-api-backend.php'),
            ],
            'laravel-query-api-backend.config'
        );

        // Registering package commands.
        // $this->commands([]);
    }
}
