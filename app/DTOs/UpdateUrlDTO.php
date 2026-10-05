<?php

declare(strict_types=1);

namespace App\DTOs;

use DateTimeInterface;
use Illuminate\Support\Carbon;

class UpdateUrlDTO
{
    public function __construct(
        public readonly ?string $originalUrl = null,
        public readonly ?string $title = null,
        public readonly ?string $customAlias = null,
        public readonly ?bool $isActive = null,
        public readonly ?DateTimeInterface $expiresAt = null,
        public readonly bool $clearExpiresAt = false,
    ) {
    }

    public static function fromArray(array $data): self
    {
        $clearExpiresAt = array_key_exists('expires_at', $data) && $data['expires_at'] === null;
        $expiresAt = (! empty($data['expires_at'])) ? Carbon::parse($data['expires_at']) : null;

        return new self(
            originalUrl: isset($data['original_url']) ? trim((string) $data['original_url']) : null,
            title: array_key_exists('title', $data) ? ($data['title'] !== null ? trim((string) $data['title']) : null) : null,
            customAlias: array_key_exists('custom_alias', $data) ? ($data['custom_alias'] !== null ? trim((string) $data['custom_alias']) : null) : null,
            isActive: isset($data['is_active']) ? (bool) $data['is_active'] : null,
            expiresAt: $expiresAt,
            clearExpiresAt: $clearExpiresAt,
        );
    }
}
