<?php

namespace App\DTOs\RepairOrder;

final readonly class CreateRepairOrderServiceDTO {
    public function __construct(
        public string $name,
        public ?string $description,
        public float $quantity,
        public float $unitValue,
    ) {
    }

    public static function fromArray(array $data): self {
        return new self(
            name: $data['name'] ?? '',
            description: $data['description'] ?? null,
            quantity: isset($data['quantity'])
                ? (float) $data['quantity']
                : 1.0,
            unitValue: (float) ($data['unit_value'] ?? 0),
        );
    }
}
