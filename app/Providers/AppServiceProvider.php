<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Carbon\Carbon;

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
        // Usar Bootstrap para la paginación
        Paginator::useBootstrap();

        // Configurar la localización regional a español
        setlocale(LC_TIME, 'es_ES.UTF-8');
        
        // Configurar la zona horaria predeterminada
        date_default_timezone_set('America/Bogota');

        // Configurar Carbon en español
        Carbon::setLocale('es');
    }
}
