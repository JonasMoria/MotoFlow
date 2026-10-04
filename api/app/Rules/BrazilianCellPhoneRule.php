<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class BrazilianCellPhoneRule implements ValidationRule {
    public function validate(
        string $attribute,
        mixed $value,
        Closure $fail,
    ): void {
        $phone = preg_replace('/\D/', '', (string) $value);

        if (str_starts_with($phone, '55')) {
            $phone = substr($phone, 2);
        }

        if (
            strlen($phone) !== 11 ||
            !preg_match('/^[1-9]{2}9[0-9]{8}$/', $phone)
        ) {
            $fail('FORM.PHONE.INVALID');
        }
    }
}
