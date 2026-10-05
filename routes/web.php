<?php

declare(strict_types=1);

use App\Http\Controllers\HealthController;
use App\Http\Controllers\RedirectController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Root landing page / Web Dashboard
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Standalone Health Check
Route::get('/health', [HealthController::class, 'check'])->name('health');

// Swagger / OpenAPI documentation UI
Route::get('/docs', function () {
    return view('docs');
})->name('docs');

Route::get('/docs/openapi.yaml', function () {
    return response()->file(base_path('docs/openapi.yaml'), [
        'Content-Type' => 'text/yaml',
    ]);
});

// Public High-Performance URL Redirection Endpoint
Route::get('/{shortCode}', [RedirectController::class, 'redirect'])
    ->where('shortCode', '^(?!api|admin|health|docs|telescope|horizon)[a-zA-Z0-9_-]+$')
    ->middleware('throttle:redirect')
    ->name('url.redirect');
