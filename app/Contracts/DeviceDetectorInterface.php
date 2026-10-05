<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Enums\DeviceType;

interface DeviceDetectorInterface
{
    /**
     * Detect the device category from a User-Agent string.
     *
     * @param string|null $userAgent
     * @return DeviceType
     */
    public function detect(?string $userAgent): DeviceType;
}
