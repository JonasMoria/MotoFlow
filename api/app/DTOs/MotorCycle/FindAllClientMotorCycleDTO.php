<?php

namespace App\DTOs\MotorCycle;

final readonly class FindAllClientMotorCycleDTO {
    public function __construct(
        public int $page,
        public int $perPage,
    ) {
    }

    public static function fromArray(array $data): self {
        return new self(
            page: (int) ($data['page'] ?? 1),
            perPage: (int) ($data['per_page'] ?? 15),
        );
    }
}
