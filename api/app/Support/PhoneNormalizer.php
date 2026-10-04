<?php

namespace App\Support;

class PhoneNormalizer {
    public static function brazilian(string $phone): string {
        $phone = StringNormalizer::numbersOnly($phone);

        if (str_starts_with($phone, '55')) {
            $phone = substr($phone, 2);
        }

        return '55' . $phone;
    }
}
