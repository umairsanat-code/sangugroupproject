<?php

namespace Sanat\ScrapeGuard;

use Illuminate\Support\ServiceProvider;
use Sanat\ScrapeGuard\Console\StatusCommand;
use Sanat\ScrapeGuard\Services\ScrapeGuardManager;

class ScrapeGuardServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/scrapeguard.php', 'scrapeguard');
        $this->app->singleton(ScrapeGuardManager::class, fn ($app) => new ScrapeGuardManager($app));
        $this->app->alias(ScrapeGuardManager::class, 'scrapeguard');
    }

    public function boot(): void
    {
        $this->publishes([__DIR__.'/../config/scrapeguard.php' => config_path('scrapeguard.php')], 'scrapeguard-config');
        $this->publishes(array_combine(
            glob(__DIR__.'/../database/migrations/*.php'),
            array_map(fn ($f) => database_path('migrations/'.basename($f)), glob(__DIR__.'/../database/migrations/*.php'))
        ), 'scrapeguard-migrations');
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'scrapeguard');
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        if ($this->app->runningInConsole()) $this->commands([StatusCommand::class]);
    }
}
