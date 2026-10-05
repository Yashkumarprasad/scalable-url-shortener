<?php

declare(strict_types=1);

namespace App\Providers;

use App\Contracts\DeviceDetectorInterface;
use App\Contracts\GeoLocationServiceInterface;
use App\Contracts\ShortCodeGeneratorInterface;
use App\Contracts\UrlCacheServiceInterface;
use App\Contracts\UrlClickRepositoryInterface;
use App\Contracts\UrlRepositoryInterface;
use App\Repositories\UrlClickRepository;
use App\Repositories\UrlRepository;
use App\Services\DeviceDetectorService;
use App\Services\GeoLocationService;
use App\Services\ShortCodeGeneratorService;
use App\Services\UrlCacheService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Core contracts
        $this->app->singleton(ShortCodeGeneratorInterface::class, ShortCodeGeneratorService::class);
        $this->app->singleton(UrlCacheServiceInterface::class, UrlCacheService::class);
        $this->app->singleton(DeviceDetectorInterface::class, DeviceDetectorService::class);
        $this->app->singleton(GeoLocationServiceInterface::class, GeoLocationService::class);

        // Repositories
        $this->app->singleton(UrlRepositoryInterface::class, UrlRepository::class);
        $this->app->singleton(UrlClickRepositoryInterface::class, UrlClickRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
