<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    public const HOME = '/';

    public function boot()
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            // API Routes - SEULEMENT 'api' middleware, PAS 'auth:sanctum'
            Route::middleware(['api']) // ← ENLEVEZ 'auth:sanctum' ici
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            // Web Routes
            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }

    protected function configureRateLimiting()
    {
        // SIMPLIFIEZ - enlevez la partie $request->user()
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(1000)->by($request->ip());
        });
    }
}