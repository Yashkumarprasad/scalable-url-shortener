<?php

declare(strict_types=1);

namespace App\DTOs;

use DateTimeInterface;
use Illuminate\Support\Carbon;

class CreateUrlDTO
{
    public function __construct(
        public readonly string $originalUrl,
        public readonly ?int $userId = null,
        public readonly ?string $customAlias = null,
        public readonly ?string $title = null,
        public readonly ?DateTimeInterface $expiresAt = null,
    ) {
    }

    public static function fromArray(array $data, ?int $userId = null): self
    {
        $expiresAt = null;
        if (! empty($data['expires_at'])) {
            $expiresAt = Carbon::parse($data['expires_at']);
        }

        $customAlias = ! empty($data['custom_alias']) ? trim((string) $data['custom_alias']) : null;
        $title = ! empty($data['title']) ? trim((string) $data['title']) : null;

        return new self(
            originalUrl: trim((string) $data['original_url']),
            userId: $userId,
            customAlias: $customAlias,
            title: $title,
            expiresAt: $expiresAt,
        );
    }
}
