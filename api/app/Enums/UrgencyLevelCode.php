<?php

namespace App\Enums;

enum UrgencyLevelCode: int {
    case VERY_LOW = 1;
    case LOW = 2;
    case MEDIUM = 3;
    case HIGH = 4;
    case VERY_HIGH = 5;

    public function label(): string {
        return match($this) {
            self::VERY_LOW => 'Lowest',
            self::LOW => 'Low',
            self::MEDIUM => 'Medium',
            self::HIGH => 'High',
            self::VERY_HIGH => 'Highest',
        };
    }
}
