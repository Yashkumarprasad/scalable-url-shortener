<?php

declare(strict_types=1);

namespace App\DTOs;

class RegisterUserDTO
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly string $password,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            name: (string) $data['name'],
            email: strtolower(trim((string) $data['email'])),
            password: (string) $data['password'],
        );
    }
}
