<?php

declare(strict_types=1);

namespace App\DTOs;

use App\Enums\DeviceType;
use DateTimeInterface;
use Illuminate\Support\Carbon;

class RecordClickDTO
{
    public function __construct(
        public readonly int $urlId,
        public readonly ?string $ipAddress = null,
        public readonly ?string $userAgent = null,
        public readonly ?string $referer = null,
        public readonly DeviceType $deviceType = DeviceType::UNKNOWN,
        public readonly ?string $countryCode = null,
        public readonly ?string $countryName = null,
        public readonly ?string $city = null,
        public readonly ?DateTimeInterface $clickedAt = null,
    ) {
    }

    public static function fromRequest(int $urlId, ?string $ip, ?string $userAgent, ?string $referer, DeviceType $deviceType, ?array $geo = null): self
    {
        return new self(
            urlId: $urlId,
            ipAddress: $ip,
            userAgent: $userAgent,
            referer: $referer,
            deviceType: $deviceType,
            countryCode: $geo['country_code'] ?? null,
            countryName: $geo['country_name'] ?? null,
            city: $geo['city'] ?? null,
            clickedAt: Carbon::now(),
        );
    }
}
