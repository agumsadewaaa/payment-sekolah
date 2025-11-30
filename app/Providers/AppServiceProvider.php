<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
{
    if ($this->app->runningInConsole()) {
        $this->app->register(\InfyOm\Generator\InfyOmGeneratorServiceProvider::class);
    }
}
}
