<?php

namespace Alyakin\Reporting;

use Illuminate\Support\ServiceProvider;

class ReportingServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any package services.
     */
    public function boot(): void
    {
        // Публикация конфигурации
        $this->publishes([
            __DIR__ . '/../config/reporting.php' => config_path('reporting.php'),
        ], 'config');

        // Публикация миграций
        $this->publishes([
            __DIR__ . '/../database/migrations/' => database_path('migrations'),
        ], 'migrations');


        // Загрузка миграций
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Слияние конфигураций пакета с конфигом приложения
        $this->mergeConfigFrom(
            __DIR__ . '/../config/reporting.php',
            'reporting'
        );
    }
}
