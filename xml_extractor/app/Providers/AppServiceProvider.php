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
    public function boot(): void
    {
        // Force timezone for runtime
        config(['app.timezone' => 'America/Cancun']);
        date_default_timezone_set('America/Cancun');
        \Carbon\Carbon::setLocale('es');
    }
}
