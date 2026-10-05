<?php

declare(strict_types=1);

namespace App\DTOs;

use DateTimeInterface;
use Illuminate\Support\Carbon;

class AnalyticsFilterDTO
{
    public function __construct(
        public readonly ?DateTimeInterface $from = null,
        public readonly ?DateTimeInterface $to = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        $from = ! empty($data['from']) ? Carbon::parse($data['from'])->startOfDay() : null;
        $to = ! empty($data['to']) ? Carbon::parse($data['to'])->endOfDay() : null;

        return new self(
            from: $from,
            to: $to,
        );
    }
}
