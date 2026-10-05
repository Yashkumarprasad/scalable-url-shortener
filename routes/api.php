<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\Admin\AdminStatsController;
use App\Http\Controllers\Api\V1\Admin\AdminUrlController;
use App\Http\Controllers\Api\V1\Admin\AdminUserController;
use App\Http\Controllers\Api\V1\AnalyticsController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\UrlController;
use App\Http\Controllers\HealthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes (v1)
|--------------------------------------------------------------------------
*/

// Health Check
Route::get('/health', [HealthController::class, 'check'])->name('api.health');
Route::get('/v1/health', [HealthController::class, 'check'])->name('api.v1.health');

Route::prefix('v1')->name('api.v1.')->group(function () {

    // Authentication Routes
    Route::prefix('auth')->name('auth.')->group(function () {
        Route::post('/register', [AuthController::class, 'register'])
            ->middleware('throttle:auth')
            ->name('register');

        Route::post('/login', [AuthController::class, 'login'])
            ->middleware('throttle:auth')
            ->name('login');

        Route::middleware(['auth:sanctum', 'active'])->group(function () {
            Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
            Route::get('/me', [AuthController::class, 'me'])->name('me');
        });
    });

    // Authenticated User Module
    Route::middleware(['auth:sanctum', 'active'])->group(function () {

        // URL Management
        Route::get('/urls', [UrlController::class, 'index'])->name('urls.index');
        Route::post('/urls', [UrlController::class, 'store'])
            ->middleware('throttle:urls.create')
            ->name('urls.store');
        Route::get('/urls/{url}', [UrlController::class, 'show'])->name('urls.show');
        Route::put('/urls/{url}', [UrlController::class, 'update'])->name('urls.update');
        Route::delete('/urls/{url}', [UrlController::class, 'destroy'])->name('urls.destroy');

        // Analytics
        Route::get('/urls/{url}/analytics', [AnalyticsController::class, 'show'])
            ->middleware('throttle:analytics')
            ->name('urls.analytics');
    });

    // Admin Module
    Route::prefix('admin')
        ->name('admin.')
        ->middleware(['auth:sanctum', 'active', 'admin'])
        ->group(function () {

            // System Statistics
            Route::get('/stats', [AdminStatsController::class, 'index'])->name('stats');

            // User Moderation
            Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
            Route::get('/users/{user}', [AdminUserController::class, 'show'])->name('users.show');
            Route::patch('/users/{user}/status', [AdminUserController::class, 'updateStatus'])->name('users.status');

            // URL Platform Moderation
            Route::get('/urls', [AdminUrlController::class, 'index'])->name('urls.index');
            Route::patch('/urls/{url}/status', [AdminUrlController::class, 'toggleStatus'])->name('urls.status');
            Route::delete('/urls/{url}', [AdminUrlController::class, 'destroy'])->name('urls.destroy');
        });
});
