<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Admin;

use App\DTOs\UpdateUrlDTO;
use App\Http\Controllers\Controller;
use App\Http\Resources\UrlResource;
use App\Models\Url;
use App\Services\UrlService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AdminUrlController extends Controller
{
    public function __construct(
        private readonly UrlService $urlService
    ) {
    }

    /**
     * List all URLs across the entire platform.
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = max(1, min(100, (int) $request->query('per_page', '15')));
        $filters = [
            'search' => $request->query('search'),
            'is_active' => $request->has('is_active') ? $request->boolean('is_active') : null,
            'user_id' => $request->query('user_id'),
        ];

        $urls = $this->urlService->getAllUrls($perPage, array_filter($filters, fn ($v) => $v !== null));

        return ApiResponse::success([
            'items' => UrlResource::collection($urls->items()),
            'pagination' => [
                'current_page' => $urls->currentPage(),
                'last_page' => $urls->lastPage(),
                'per_page' => $urls->perPage(),
                'total' => $urls->total(),
            ],
        ], 'All URLs retrieved successfully');
    }

    /**
     * Activate or deactivate a URL by Admin.
     */
    public function toggleStatus(Request $request, Url $url): JsonResponse
    {
        $request->validate([
            'is_active' => ['required', 'boolean'],
        ]);

        $isActive = $request->boolean('is_active');
        $updated = $this->urlService->updateUrl($url, new UpdateUrlDTO(isActive: $isActive));

        Log::info('Admin toggled URL status', [
            'admin_id' => $request->user()->id,
            'url_id' => $url->id,
            'is_active' => $isActive,
        ]);

        return ApiResponse::success(
            new UrlResource($updated),
            $isActive ? 'URL activated successfully' : 'URL deactivated successfully'
        );
    }

    /**
     * Delete any URL by Admin.
     */
    public function destroy(Request $request, Url $url): JsonResponse
    {
        $this->urlService->deleteUrl($url);

        Log::info('Admin deleted URL', [
            'admin_id' => $request->user()->id,
            'url_id' => $url->id,
        ]);

        return ApiResponse::success(null, 'URL deleted successfully by admin');
    }
}
