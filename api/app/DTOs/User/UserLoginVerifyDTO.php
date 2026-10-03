<?php

namespace App\DTOs\User;

final readonly class UserLoginVerifyDTO {
    public function __construct(
        public string $email,
        public string $code,
    ) {
    }

    public static function fromArray(array $data): self {
        return new self(
            email: $data['email'] ?? '',
            code: $data['code'] ?? '',
        );
    }
}
