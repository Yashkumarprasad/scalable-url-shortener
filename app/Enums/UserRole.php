<?php

declare(strict_types=1);

namespace App\Enums;

enum UserRole: string
{
    case USER = 'USER';
    case ADMIN = 'ADMIN';

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
