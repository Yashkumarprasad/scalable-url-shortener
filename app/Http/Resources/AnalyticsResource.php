<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AnalyticsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'url_id' => $this->resource['url_id'],
            'short_code' => $this->resource['short_code'],
            'custom_alias' => $this->resource['custom_alias'],
            'original_url' => $this->resource['original_url'],
            'total_clicks' => $this->resource['total_clicks'],
            'lifetime_clicks' => $this->resource['lifetime_clicks'],
            'filter' => $this->resource['filter'],
            'clicks_by_day' => $this->resource['clicks_by_day'],
            'clicks_by_hour' => $this->resource['clicks_by_hour'],
            'top_referrers' => $this->resource['top_referrers'],
            'device_breakdown' => $this->resource['device_breakdown'],
            'country_breakdown' => $this->resource['country_breakdown'],
        ];
    }
}
