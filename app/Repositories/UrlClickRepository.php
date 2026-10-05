<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\UrlClickRepositoryInterface;
use App\DTOs\AnalyticsFilterDTO;
use App\DTOs\RecordClickDTO;
use App\Models\UrlClick;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class UrlClickRepository implements UrlClickRepositoryInterface
{
    /**
     * Store click analytics record.
     */
    public function record(RecordClickDTO $dto): UrlClick
    {
        return UrlClick::create([
            'url_id' => $dto->urlId,
            'ip_address' => $dto->ipAddress,
            'user_agent' => $dto->userAgent,
            'referer' => $dto->referer,
            'device_type' => $dto->deviceType,
            'country_code' => $dto->countryCode,
            'country_name' => $dto->countryName,
            'city' => $dto->city,
            'clicked_at' => $dto->clickedAt,
            'created_at' => now(),
        ]);
    }

    /**
     * Apply date range filters to query.
     */
    private function applyFilter(Builder $query, AnalyticsFilterDTO $filter): Builder
    {
        if ($filter->from !== null) {
            $query->where('clicked_at', '>=', $filter->from);
        }

        if ($filter->to !== null) {
            $query->where('clicked_at', '<=', $filter->to);
        }

        return $query;
    }

    /**
     * Get aggregate click count for a URL within optional date range.
     */
    public function getTotalClicks(int $urlId, AnalyticsFilterDTO $filter): int
    {
        $query = UrlClick::query()->where('url_id', $urlId);
        return $this->applyFilter($query, $filter)->count();
    }

    /**
     * Get clicks aggregated by day.
     */
    public function getClicksByDay(int $urlId, AnalyticsFilterDTO $filter): Collection
    {
        $driver = DB::getDriverName();
        $dateExpression = $driver === 'sqlite'
            ? "strftime('%Y-%m-%d', clicked_at)"
            : "DATE(clicked_at)";

        $query = UrlClick::query()
            ->where('url_id', $urlId)
            ->selectRaw("{$dateExpression} as date, COUNT(*) as count")
            ->groupBy('date')
            ->orderBy('date', 'asc');

        return $this->applyFilter($query, $filter)->get();
    }

    /**
     * Get clicks aggregated by hour of day (0-23).
     */
    public function getClicksByHour(int $urlId, AnalyticsFilterDTO $filter): Collection
    {
        $driver = DB::getDriverName();
        $hourExpression = $driver === 'sqlite'
            ? "CAST(strftime('%H', clicked_at) AS INTEGER)"
            : "HOUR(clicked_at)";

        $query = UrlClick::query()
            ->where('url_id', $urlId)
            ->selectRaw("{$hourExpression} as hour, COUNT(*) as count")
            ->groupBy('hour')
            ->orderBy('hour', 'asc');

        return $this->applyFilter($query, $filter)->get();
    }

    /**
     * Get top referrers.
     */
    public function getTopReferrers(int $urlId, AnalyticsFilterDTO $filter, int $limit = 10): Collection
    {
        $query = UrlClick::query()
            ->where('url_id', $urlId)
            ->whereNotNull('referer')
            ->where('referer', '!=', '')
            ->select('referer', DB::raw('COUNT(*) as count'))
            ->groupBy('referer')
            ->orderByDesc('count')
            ->limit($limit);

        return $this->applyFilter($query, $filter)->get();
    }

    /**
     * Get breakdown by device type.
     */
    public function getDeviceBreakdown(int $urlId, AnalyticsFilterDTO $filter): Collection
    {
        $query = UrlClick::query()
            ->where('url_id', $urlId)
            ->select('device_type', DB::raw('COUNT(*) as count'))
            ->groupBy('device_type')
            ->orderByDesc('count');

        return $this->applyFilter($query, $filter)->get();
    }

    /**
     * Get breakdown by country.
     */
    public function getCountryBreakdown(int $urlId, AnalyticsFilterDTO $filter, int $limit = 10): Collection
    {
        $query = UrlClick::query()
            ->where('url_id', $urlId)
            ->whereNotNull('country_code')
            ->select('country_code', 'country_name', DB::raw('COUNT(*) as count'))
            ->groupBy('country_code', 'country_name')
            ->orderByDesc('count')
            ->limit($limit);

        return $this->applyFilter($query, $filter)->get();
    }
}
