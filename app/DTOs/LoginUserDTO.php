<?php

declare(strict_types=1);

namespace App\DTOs;

class LoginUserDTO
{
    public function __construct(
        public readonly string $email,
        public readonly string $password,
        public readonly ?string $deviceName = 'api-token',
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            email: strtolower(trim((string) $data['email'])),
            password: (string) $data['password'],
            deviceName: isset($data['device_name']) ? (string) $data['device_name'] : 'api-token',
        );
    }
}
