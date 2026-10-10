<?php

namespace App\Enums;

enum PaperFormatCode: int {
    case A4 = 1;

    public function label(): string {
        return match ($this) {
            self::A4 => 'a4',
        };
    }
}
