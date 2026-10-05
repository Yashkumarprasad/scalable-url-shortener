<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\DTOs\CreateUrlDTO;
use Illuminate\Foundation\Http\FormRequest;

class StoreUrlRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'original_url' => [
                'required',
                'string',
                'url',
                'max:2048',
                'regex:/^https?:\/\//i', // Strictly enforce http or https schemes only
            ],
            'custom_alias' => [
                'nullable',
                'string',
                'min:3',
                'max:64',
                'regex:/^[a-zA-Z0-9_-]+$/',
                'unique:urls,custom_alias',
                'unique:urls,short_code',
            ],
            'title' => ['nullable', 'string', 'max:255'],
            'expires_at' => ['nullable', 'date', 'after:now'],
        ];
    }

    public function messages(): array
    {
        return [
            'original_url.regex' => 'The destination URL must use an http:// or https:// scheme.',
            'custom_alias.regex' => 'The custom alias may only contain letters, numbers, hyphens, and underscores.',
            'custom_alias.unique' => 'The specified custom alias is already in use.',
        ];
    }

    public function toDTO(?int $userId = null): CreateUrlDTO
    {
        return CreateUrlDTO::fromArray($this->validated(), $userId);
    }
}
