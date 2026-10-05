<?php

declare(strict_types=1);

namespace App\Enums;

enum DeviceType: string
{
    case DESKTOP = 'DESKTOP';
    case MOBILE = 'MOBILE';
    case TABLET = 'TABLET';
    case BOT = 'BOT';
    case UNKNOWN = 'UNKNOWN';

    /**
     * Get all values as an array.
     *
     * @return array<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
