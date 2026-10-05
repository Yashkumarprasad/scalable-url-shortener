<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUrlRequest;
use App\Http\Requests\UpdateUrlRequest;
use App\Http\Resources\UrlResource;
use App\Models\Url;
use App\Services\UrlService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UrlController extends Controller
{
    public function __construct(
        private readonly UrlService $urlService
    ) {
    }

    /**
     * List paginated URLs for the authenticated user.
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Url::class);

        $perPage = (int) $request->query('per_page', '15');
        $perPage = max(1, min(100, $perPage));

        $urls = $this->urlService->getUserUrls($request->user()->id, $perPage);

        return ApiResponse::success([
            'items' => UrlResource::collection($urls->items()),
            'pagination' => [
                'current_page' => $urls->currentPage(),
                'last_page' => $urls->lastPage(),
                'per_page' => $urls->perPage(),
                'total' => $urls->total(),
            ],
        ], 'URLs retrieved successfully');
    }

    /**
     * Create a new short URL.
     */
    public function store(StoreUrlRequest $request): JsonResponse
    {
        $this->authorize('create', Url::class);

        $dto = $request->toDTO($request->user()->id);
        $url = $this->urlService->createUrl($dto);

        return ApiResponse::created(
            new UrlResource($url),
            'URL created successfully'
        );
    }

    /**
     * Get specific URL details.
     */
    public function show(Url $url): JsonResponse
    {
        $this->authorize('view', $url);

        return ApiResponse::success(
            new UrlResource($url),
            'URL retrieved successfully'
        );
    }

    /**
     * Update an existing URL.
     */
    public function update(UpdateUrlRequest $request, Url $url): JsonResponse
    {
        $this->authorize('update', $url);

        $dto = $request->toDTO();
        $updated = $this->urlService->updateUrl($url, $dto);

        return ApiResponse::success(
            new UrlResource($updated),
            'URL updated successfully'
        );
    }

    /**
     * Delete a URL (soft-delete).
     */
    public function destroy(Url $url): JsonResponse
    {
        $this->authorize('delete', $url);

        $this->urlService->deleteUrl($url);

        return ApiResponse::success(null, 'URL deleted successfully');
    }
}
