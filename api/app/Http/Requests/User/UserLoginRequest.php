<?php

namespace App\Http\Requests\User;

use App\Http\Requests\AppFormRequest;

class UserLoginRequest extends AppFormRequest {
    public function authorize(): bool {
        return true;
    }

    public function rules(): array {
        return [
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    public function messages(): array {
        return [
            'email.required' => 'FORM.LOGIN.EMAIL.REQUIRED',
            'email.email' => 'FORM.LOGIN.EMAIL.INVALID',
            'password.required' => 'FORM.LOGIN.PASSWORD.REQUIRED',
            'password.string' => 'FORM.LOGIN.PASSWORD.INVALID',
        ];
    }
}
