<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Admin;

use App\Enums\UserStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\UrlResource;
use App\Models\Url;
use App\Models\UrlClick;
use App\Models\User;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

class AdminStatsController extends Controller
{
    /**
     * Get platform overview metrics.
     */
    public function index(): JsonResponse
    {
        $totalUsers = User::count();
        $activeUsers = User::where('status', UserStatus::ACTIVE)->count();
        $suspendedUsers = User::where('status', UserStatus::SUSPENDED)->count();

        $totalUrls = Url::count();
        $activeUrls = Url::where('is_active', true)->count();
        $expiredUrls = Url::whereNotNull('expires_at')->where('expires_at', '<', now())->count();

        $totalClicks = (int) Url::sum('click_count');
        $clicksLast24h = UrlClick::where('clicked_at', '>=', now()->subDay())->count();

        $topUrls = Url::query()->with('user')->orderByDesc('click_count')->limit(5)->get();

        return ApiResponse::success([
            'users' => [
                'total' => $totalUsers,
                'active' => $activeUsers,
                'suspended' => $suspendedUsers,
            ],
            'urls' => [
                'total' => $totalUrls,
                'active' => $activeUrls,
                'expired' => $expiredUrls,
            ],
            'clicks' => [
                'total' => $totalClicks,
                'last_24_hours' => $clicksLast24h,
            ],
            'top_performing_urls' => UrlResource::collection($topUrls),
        ], 'System statistics retrieved successfully');
    }
}
