<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\DeviceDetectorInterface;
use App\Contracts\GeoLocationServiceInterface;
use App\Contracts\UrlClickRepositoryInterface;
use App\DTOs\AnalyticsFilterDTO;
use App\DTOs\RecordClickDTO;
use App\Jobs\RecordUrlClickJob;
use App\Models\Url;
use Illuminate\Http\Request;

class AnalyticsService
{
    public function __construct(
        private readonly UrlClickRepositoryInterface $clickRepository,
        private readonly DeviceDetectorInterface $deviceDetector,
        private readonly GeoLocationServiceInterface $geoService
    ) {
    }

    /**
     * Dispatch an asynchronous click tracking job.
     * Keeps the redirect request path fast and unblocked.
     */
    public function dispatchClick(int $urlId, Request $request): void
    {
        $userAgent = $request->userAgent();
        $ip = $request->ip();
        $referer = $request->header('referer');

        $deviceType = $this->deviceDetector->detect($userAgent);
        $geo = $this->geoService->resolve($ip);

        $dto = RecordClickDTO::fromRequest(
            urlId: $urlId,
            ip: $ip,
            userAgent: $userAgent,
            referer: $referer,
            deviceType: $deviceType,
            geo: $geo
        );

        RecordUrlClickJob::dispatch($dto);
    }

    /**
     * Retrieve aggregated analytics report for a given URL.
     */
    public function getAnalytics(Url $url, AnalyticsFilterDTO $filter): array
    {
        return [
            'url_id' => $url->id,
            'short_code' => $url->short_code,
            'custom_alias' => $url->custom_alias,
            'original_url' => $url->original_url,
            'total_clicks' => $this->clickRepository->getTotalClicks($url->id, $filter),
            'lifetime_clicks' => (int) $url->click_count,
            'filter' => [
                'from' => $filter->from?->toIso8601String(),
                'to' => $filter->to?->toIso8601String(),
            ],
            'clicks_by_day' => $this->clickRepository->getClicksByDay($url->id, $filter),
            'clicks_by_hour' => $this->clickRepository->getClicksByHour($url->id, $filter),
            'top_referrers' => $this->clickRepository->getTopReferrers($url->id, $filter, 10),
            'device_breakdown' => $this->clickRepository->getDeviceBreakdown($url->id, $filter),
            'country_breakdown' => $this->clickRepository->getCountryBreakdown($url->id, $filter, 10),
        ];
    }
}
