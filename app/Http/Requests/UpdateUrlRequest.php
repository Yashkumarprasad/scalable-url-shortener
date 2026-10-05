<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\DTOs\UpdateUrlDTO;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUrlRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $urlId = $this->route('url') ? (is_object($this->route('url')) ? $this->route('url')->id : $this->route('url')) : null;

        return [
            'original_url' => [
                'sometimes',
                'required',
                'string',
                'url',
                'max:2048',
                'regex:/^https?:\/\//i',
            ],
            'title' => ['sometimes', 'nullable', 'string', 'max:255'],
            'custom_alias' => [
                'sometimes',
                'nullable',
                'string',
                'min:3',
                'max:64',
                'regex:/^[a-zA-Z0-9_-]+$/',
                Rule::unique('urls', 'custom_alias')->ignore($urlId),
                Rule::unique('urls', 'short_code')->ignore($urlId),
            ],
            'is_active' => ['sometimes', 'boolean'],
            'expires_at' => ['sometimes', 'nullable', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'original_url.regex' => 'The destination URL must use an http:// or https:// scheme.',
            'custom_alias.regex' => 'The custom alias may only contain letters, numbers, hyphens, and underscores.',
        ];
    }

    public function toDTO(): UpdateUrlDTO
    {
        return UpdateUrlDTO::fromArray($this->validated());
    }
}
