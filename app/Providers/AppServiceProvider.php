<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

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
        // Force HTTPS only in production (e.g. behind a reverse proxy/load balancer
        // that terminates SSL). Forcing it locally breaks links/redirects when the
        // local dev server only serves plain HTTP.
        if (app()->environment('production')) {
            URL::forceScheme('https');
        }
    }
}
