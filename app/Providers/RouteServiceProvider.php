<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * @var string
     */
    public const HOME = '/';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::middleware(['api', 'force.json'])
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }

    /**
     * Configure the rate limiters for the application.
     */
    protected function configureRateLimiting(): void
    {
        // Global API rate limiter
        RateLimiter::for('api', function (Request $request) {
            $limit = config('ratelimit.api', 60);
            return Limit::perMinute($limit)->by($request->user()?->id ?: $request->ip());
        });

        // Authentication endpoint rate limiter (10/min/IP)
        RateLimiter::for('auth', function (Request $request) {
            $limit = config('ratelimit.auth', 10);
            return Limit::perMinute($limit)->by($request->ip());
        });

        // URL Creation rate limiter (60/min/User)
        RateLimiter::for('urls.create', function (Request $request) {
            $limit = config('ratelimit.urls_create', 60);
            return Limit::perMinute($limit)->by($request->user()?->id ?: $request->ip());
        });

        // Redirect rate limiter (120/min/IP)
        RateLimiter::for('redirect', function (Request $request) {
            $limit = config('ratelimit.redirect', 120);
            return Limit::perMinute($limit)->by($request->ip());
        });

        // Analytics API rate limiter (30/min/User)
        RateLimiter::for('analytics', function (Request $request) {
            $limit = config('ratelimit.analytics', 30);
            return Limit::perMinute($limit)->by($request->user()?->id ?: $request->ip());
        });
    }
}
