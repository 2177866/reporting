<?php

namespace Alyakin\Reporting\Tests;

use Alyakin\Reporting\ReportingServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    protected function getPackageProviders($app)
    {
        return [ReportingServiceProvider::class];
    }

    protected function defineEnvironment($app)
    {
        $app['config']->set('database.default', 'testing');

        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }

    protected function defineDatabaseMigrations(): void
    {
        $this->afterApplicationCreated(function () {
            $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        });
    }

    protected function defineConfig($app)
    {
        $app['config']->set('reporting', require __DIR__.'/../config/reporting.php');
    }
}
