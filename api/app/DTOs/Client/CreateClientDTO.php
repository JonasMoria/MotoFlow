<?php

namespace App\DTOs\Client;

use Illuminate\Http\UploadedFile;

final readonly class CreateClientDTO {
    public function __construct(
        public string $name,
        public string $phone,
        public ?string $email,
        public ?string $document,
        public ?string $address,
        public ?UploadedFile $avatar,
    ) {
    }

    public static function fromArray(array $data): self {
        return new self(
            name: $data['name'] ?? '',
            phone: $data['phone'] ?? '',
            email: $data['email'] ?? null,
            document: $data['document'] ?? null,
            address: $data['address'] ?? null,
            avatar: $data['avatar'] ?? null,
        );
    }
}
