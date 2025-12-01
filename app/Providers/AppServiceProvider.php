<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\Tagihan;
use App\Models\KasSekolah;
use App\Observers\ActivityObserver;

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

        // register model observers to capture admin activity
        Siswa::observe(ActivityObserver::class);
        Kelas::observe(ActivityObserver::class);
        Tagihan::observe(ActivityObserver::class);
        KasSekolah::observe(ActivityObserver::class);
}
}
