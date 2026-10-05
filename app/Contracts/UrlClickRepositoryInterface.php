<?php

declare(strict_types=1);

namespace App\Contracts;

use App\DTOs\AnalyticsFilterDTO;
use App\DTOs\RecordClickDTO;
use App\Models\UrlClick;
use Illuminate\Support\Collection;

interface UrlClickRepositoryInterface
{
    /**
     * Store click analytics record.
     *
     * @param RecordClickDTO $dto
     * @return UrlClick
     */
    public function record(RecordClickDTO $dto): UrlClick;

    /**
     * Get aggregate click count for a URL within optional date range.
     *
     * @param int $urlId
     * @param AnalyticsFilterDTO $filter
     * @return int
     */
    public function getTotalClicks(int $urlId, AnalyticsFilterDTO $filter): int;

    /**
     * Get clicks aggregated by day.
     *
     * @param int $urlId
     * @param AnalyticsFilterDTO $filter
     * @return Collection
     */
    public function getClicksByDay(int $urlId, AnalyticsFilterDTO $filter): Collection;

    /**
     * Get clicks aggregated by hour of day (0-23).
     *
     * @param int $urlId
     * @param AnalyticsFilterDTO $filter
     * @return Collection
     */
    public function getClicksByHour(int $urlId, AnalyticsFilterDTO $filter): Collection;

    /**
     * Get top referrers.
     *
     * @param int $urlId
     * @param AnalyticsFilterDTO $filter
     * @param int $limit
     * @return Collection
     */
    public function getTopReferrers(int $urlId, AnalyticsFilterDTO $filter, int $limit = 10): Collection;

    /**
     * Get breakdown by device type.
     *
     * @param int $urlId
     * @param AnalyticsFilterDTO $filter
     * @return Collection
     */
    public function getDeviceBreakdown(int $urlId, AnalyticsFilterDTO $filter): Collection;

    /**
     * Get breakdown by country.
     *
     * @param int $urlId
     * @param AnalyticsFilterDTO $filter
     * @param int $limit
     * @return Collection
     */
    public function getCountryBreakdown(int $urlId, AnalyticsFilterDTO $filter, int $limit = 10): Collection;
}
