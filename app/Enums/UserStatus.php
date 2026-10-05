<?php

declare(strict_types=1);

namespace App\Enums;

enum UserStatus: string
{
    case ACTIVE = 'ACTIVE';
    case INACTIVE = 'INACTIVE';
    case SUSPENDED = 'SUSPENDED';

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
