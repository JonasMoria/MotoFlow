<?php

namespace App\Support;

class PlateNormalizer {
    public static function brazilian(string $plate): string {
        return strtoupper(
            preg_replace('/[^A-Za-z0-9]/', '', $plate),
        );
    }
}
