<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\DTOs\RegisterUserDTO;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email:rfc', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', Password::min(8)->letters()->numbers()],
        ];
    }

    public function toDTO(): RegisterUserDTO
    {
        return RegisterUserDTO::fromArray($this->validated());
    }
}
