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
        // Forzar HTTPS si la petición es segura, si está configurado en .env o en producción
        if (request()->isSecure() || env('FORCE_HTTPS', false) || app()->environment('production')) {
            URL::forceScheme('https');
        }
    }
}
