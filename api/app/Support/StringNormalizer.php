<?php

namespace App\Support;

class StringNormalizer {
    public static function numbersOnly(string $value): string {
        return preg_replace('/\D/', '', $value);
    }
}
