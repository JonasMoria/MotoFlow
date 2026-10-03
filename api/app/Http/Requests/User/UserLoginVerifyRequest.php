<?php

namespace App\Http\Requests\User;

use App\Http\Requests\AppFormRequest;

class UserLoginVerifyRequest extends AppFormRequest {
    public function authorize(): bool {
        return true;
    }

    public function rules(): array {
        return [
            'email' => [
                'required',
                'email',
            ],
            'code' => [
                'required',
                'string',
                'size:6',
            ],
        ];
    }

    public function messages(): array {
        return [
            'email.required' => 'FORM.LOGIN.EMAIL.REQUIRED',
            'email.email' => 'FORM.LOGIN.EMAIL.INVALID',
            'code.required' => 'FORM.LOGIN.CODE.REQUIRED',
            'code.string' => 'FORM.LOGIN.CODE.INVALID',
            'code.size' => 'FORM.LOGIN.CODE.SIZE',
        ];
    }
}
