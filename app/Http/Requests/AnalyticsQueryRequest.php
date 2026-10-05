<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\DTOs\AnalyticsFilterDTO;
use Illuminate\Foundation\Http\FormRequest;

class AnalyticsQueryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ];
    }

    public function toDTO(): AnalyticsFilterDTO
    {
        return AnalyticsFilterDTO::fromArray($this->validated());
    }
}
