<?php

namespace App\Http\Requests;

use App\Exceptions\FormRequestException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

abstract class AppFormRequest extends FormRequest {
    protected $stopOnFirstFailure = true;

    protected function failedValidation(Validator $validator): void {
        throw new FormRequestException(
            $validator->errors()->first(),
        );
    }
}
