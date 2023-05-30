<?php

namespace YourVendorName\LaravelTracker;

use Illuminate\Support\ServiceProvider;

class TrackerServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Publish the migration file
        $this->publishes([
            __DIR__ . '/../database/migrations/' => database_path('migrations')
        ], 'migrations');

        // Register the middleware
        app('router')->aliasMiddleware('tracker', TrackerMiddleware::class);
    }

    public function register()
    {
        // Register the package           $this->app->bind('laravel-tracker', function () {
            return new Tracker;
        });
    }
}
