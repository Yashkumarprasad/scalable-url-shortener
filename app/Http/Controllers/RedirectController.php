<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\AnalyticsService;
use App\Services\UrlService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectController extends Controller
{
    public function __construct(
        private readonly UrlService $urlService,
        private readonly AnalyticsService $analyticsService
    ) {
    }

    /**
     * Handle short URL redirection with Redis caching and asynchronous click tracking.
     */
    public function redirect(Request $request, string $shortCode): Response
    {
        // 1. Resolve destination URL (Redis Cache -> Fallback DB -> Cache Set)
        $destination = $this->urlService->resolveDestination($shortCode);

        // 2. Dispatch non-blocking analytics event to Redis background queue
        $this->analyticsService->dispatchClick($destination['id'], $request);

        // 3. Perform HTTP 302 redirect with proper caching headers
        return redirect()->away($destination['original_url'], Response::HTTP_FOUND, [
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
            'X-Robots-Tag' => 'noindex, nofollow',
            'X-Redirect-Cache' => $destination['is_cached'] ? 'HIT' : 'MISS',
        ]);
    }
}
