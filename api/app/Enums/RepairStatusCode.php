<?php

namespace App\Enums;

enum RepairStatusCode: int {
    case CREATED = 1;
    case WAITING = 2;
    case IN_MAINTENANCE = 3;
    case WAITING_PICKUP = 4;
    case DONE = 5;
    case CANCELED = 6;

    public function label(): string {
        return match ($this) {
            self::CREATED => 'Created',
            self::WAITING => 'Waiting',
            self::IN_MAINTENANCE => 'In Maintenance',
            self::WAITING_PICKUP => 'Waiting Pickup',
            self::DONE => 'Done',
            self::CANCELED => 'Canceled',
        };
    }

    public static function getNotActiveStatusValues(): array {
        return [
            self::DONE->value,
            self::CANCELED->value,
        ];
    }
}
