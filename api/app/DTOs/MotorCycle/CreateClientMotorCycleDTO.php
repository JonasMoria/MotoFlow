<?php

namespace App\DTOs\MotorCycle;

final readonly class CreateClientMotorCycleDTO {
    public function __construct(
        public string $brand,
        public string $model,
        public string $plate,
        public ?string $renavam,
        public ?string $chassis,
        public ?int $year,
        public ?string $color,
        public ?string $engineNumber,
    ) {
    }

    public static function fromArray(array $data): self {
        return new self(
            brand: $data['brand'] ?? '',
            model: $data['model'] ?? '',
            plate: $data['plate'] ?? '',
            renavam: $data['renavam'] ?? null,
            chassis: $data['chassis'] ?? null,
            year: $data['year'] ?? null,
            color: $data['color'] ?? null,
            engineNumber: $data['engine_number'] ?? null,
        );
    }
}
