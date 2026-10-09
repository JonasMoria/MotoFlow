<?php

namespace App\DTOs\RepairOrder;

final readonly class CreateRepairOrderDTO {
    public function __construct(
        public string $title,
        public string $description,
        public ?int $status,
        public ?int $urgencyLevel,
        public ?string $diagnosis,
        public ?string $observations,
    ) {
    }

    public static function fromArray(array $data): self {
        return new self(
            title: $data['title'] ?? '',
            description: $data['description'] ?? '',
            status: isset($data['status'])
                ? (int) $data['status']
                : null,
            urgencyLevel: isset($data['urgency_level'])
                ? (int) $data['urgency_level']
                : null,
            diagnosis: $data['diagnosis'] ?? null,
            observations: $data['observations'] ?? null,
        );
    }
}
