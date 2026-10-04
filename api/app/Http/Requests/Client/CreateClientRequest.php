<?php

namespace App\Http\Requests\Client;

use App\Http\Requests\AppFormRequest;
use App\Rules\BrazilianCellPhoneRule;

class CreateClientRequest extends AppFormRequest {
    public function authorize(): bool {
        return true;
    }

    public function rules(): array {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'phone' => [
                'required',
                'string',
                new BrazilianCellPhoneRule(),
            ],
            'email' => [
                'nullable',
                'email',
                'max:255',
            ],
            'document' => [
                'nullable',
                'string',
                'max:20',
            ],
            'address' => [
                'nullable',
                'string',
            ],
            'avatar' => [
                'nullable',
                'file',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],
        ];
    }

    public function messages(): array {
        return [
            'name.required' => 'FORM.CLIENT.NAME.REQUIRED',
            'name.string' => 'FORM.CLIENT.NAME.STRING',
            'name.max' => 'FORM.CLIENT.NAME.MAX',
            'phone.required' => 'FORM.CLIENT.PHONE.REQUIRED',
            'phone.string' => 'FORM.CLIENT.PHONE.STRING',
            'email.email' => 'FORM.CLIENT.EMAIL.INVALID',
            'email.max' => 'FORM.CLIENT.EMAIL.MAX',
            'document.string' => 'FORM.CLIENT.DOCUMENT.STRING',
            'document.max' => 'FORM.CLIENT.DOCUMENT.MAX',
            'address.string' => 'FORM.CLIENT.ADDRESS.STRING',
            'avatar.file' => 'FORM.CLIENT.AVATAR.FILE',
            'avatar.image' => 'FORM.CLIENT.AVATAR.IMAGE',
            'avatar.mimes' => 'FORM.CLIENT.AVATAR.MIMES',
            'avatar.max' => 'FORM.CLIENT.AVATAR.MAX',
        ];
    }
}
