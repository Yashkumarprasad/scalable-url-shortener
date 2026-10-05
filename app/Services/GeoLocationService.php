<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\GeoLocationServiceInterface;

class GeoLocationService implements GeoLocationServiceInterface
{
    /**
     * Resolve IP address to location metadata.
     * Checks optional reverse proxy headers (e.g., Cloudflare CF-IPCountry) or fallback.
     */
    public function resolve(?string $ipAddress): array
    {
        if (empty($ipAddress) || $this->isPrivateIp($ipAddress)) {
            return [
                'country_code' => 'LOC',
                'country_name' => 'Local / Private Network',
                'city' => 'Localhost',
            ];
        }

        // Check if behind Cloudflare or similar reverse proxy
        $cfCountry = request()->header('CF-IPCountry');
        if (! empty($cfCountry) && strlen($cfCountry) === 2) {
            return [
                'country_code' => strtoupper($cfCountry),
                'country_name' => strtoupper($cfCountry),
                'city' => request()->header('CF-IPCity') ?: null,
            ];
        }

        return [
            'country_code' => 'UNK',
            'country_name' => 'Unknown Location',
            'city' => null,
        ];
    }

    /**
     * Check if IP address is a private / loopback address.
     */
    private function isPrivateIp(string $ip): bool
    {
        return ! filter_var(
            $ip,
            FILTER_VALIDATE_IP,
            FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
        );
    }
}
