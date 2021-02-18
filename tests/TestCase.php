<?php

namespace Shirokovnv\LaravelQueryApiBackend\Tests;

use Shirokovnv\LaravelQueryApiBackend\LaravelQueryApiBackendServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        // additional setup
    }

    protected function getPackageProviders($app)
    {
        return [
            LaravelQueryApiBackendServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        // perform environment setup
    }
}
