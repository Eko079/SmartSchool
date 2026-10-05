<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
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
        // Paksa skema https di production agar url() tidak hasilkan http (anti mixed-content).
        if (config('app.env') === 'production' || ($this->app->request && $this->app->request->header('X-Forwarded-Proto') === 'https')) {
            URL::forceScheme('https');
        }
    }
}
