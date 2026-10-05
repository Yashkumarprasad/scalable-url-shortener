<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\AnalyticsQueryRequest;
use App\Http\Resources\AnalyticsResource;
use App\Models\Url;
use App\Services\AnalyticsService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

class AnalyticsController extends Controller
{
    public function __construct(
        private readonly AnalyticsService $analyticsService
    ) {
    }

    /**
     * Retrieve aggregated click analytics for a specific URL.
     */
    public function show(AnalyticsQueryRequest $request, Url $url): JsonResponse
    {
        $this->authorize('viewAnalytics', $url);

        $filter = $request->toDTO();
        $analytics = $this->analyticsService->getAnalytics($url, $filter);

        return ApiResponse::success(
            new AnalyticsResource($analytics),
            'URL analytics retrieved successfully'
        );
    }
}
