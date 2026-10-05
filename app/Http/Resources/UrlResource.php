<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Url
 */
class UrlResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'original_url' => $this->original_url,
            'short_code' => $this->short_code,
            'custom_alias' => $this->custom_alias,
            'short_url' => $this->short_url,
            'title' => $this->title,
            'is_active' => (bool) $this->is_active,
            'is_expired' => $this->isExpired(),
            'expires_at' => $this->expires_at?->toIso8601String(),
            'click_count' => (int) $this->click_count,
            'user' => $this->whenLoaded('user', fn () => new UserResource($this->user)),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
