<?php

declare(strict_types=1);

namespace App\Contracts;

interface GeoLocationServiceInterface
{
    /**
     * Resolve IP address to approximate location (country code, country name, city).
     *
     * @param string|null $ipAddress
     * @return array{country_code: string|null, country_name: string|null, city: string|null}
     */
    public function resolve(?string $ipAddress): array;
}
