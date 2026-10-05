<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\DeviceDetectorInterface;
use App\Enums\DeviceType;

class DeviceDetectorService implements DeviceDetectorInterface
{
    /**
     * Common bot user-agent patterns.
     */
    private const BOT_PATTERNS = [
        'bot', 'crawl', 'spider', 'slurp', 'mediapartners', 'facebookexternalhit',
        'curl', 'wget', 'python', 'postman', 'insomnia', 'httpclient', 'headlesschrome'
    ];

    /**
     * Common tablet patterns.
     */
    private const TABLET_PATTERNS = [
        'ipad', 'tablet', 'kindle', 'silk', 'playbook', 'nexus 7', 'nexus 9', 'nexus 10', 'tb-x'
    ];

    /**
     * Common mobile patterns.
     */
    private const MOBILE_PATTERNS = [
        'mobile', 'iphone', 'ipod', 'opera mini', 'iemobile', 'webos', 'windows phone'
    ];

    /**
     * Detect device type from User-Agent string.
     */
    public function detect(?string $userAgent): DeviceType
    {
        if (empty($userAgent)) {
            return DeviceType::UNKNOWN;
        }

        $ua = strtolower($userAgent);

        // 1. Check for bots / scrapers
        foreach (self::BOT_PATTERNS as $bot) {
            if (str_contains($ua, $bot)) {
                return DeviceType::BOT;
            }
        }

        // 2. Check for explicit tablet keywords or models
        foreach (self::TABLET_PATTERNS as $tablet) {
            if (str_contains($ua, $tablet)) {
                return DeviceType::TABLET;
            }
        }

        // 3. Android devices without "mobile" in user agent are tablets
        if (str_contains($ua, 'android') && ! str_contains($ua, 'mobile')) {
            return DeviceType::TABLET;
        }

        // 4. Check for mobile keywords
        foreach (self::MOBILE_PATTERNS as $mobile) {
            if (str_contains($ua, $mobile)) {
                return DeviceType::MOBILE;
            }
        }

        // 5. Default to desktop if standard browser engine detected
        if (str_contains($ua, 'mozilla') || str_contains($ua, 'gecko') || str_contains($ua, 'webkit') || str_contains($ua, 'windows') || str_contains($ua, 'macintosh')) {
            return DeviceType::DESKTOP;
        }

        return DeviceType::UNKNOWN;
    }
}
