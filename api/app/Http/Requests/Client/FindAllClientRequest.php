<?php

namespace App\Http\Requests\Client;

use App\Http\Requests\AppFormRequest;

class FindAllClientRequest extends AppFormRequest {
    public function authorize(): bool {
        return true;
    }

    public function rules(): array {
        return [
            'page' => [
                'nullable',
                'integer',
                'min:1',
            ],
            'per_page' => [
                'nullable',
                'integer',
                'min:1',
                'max:100',
            ],
        ];
    }

    public function messages(): array {
        return [
            'page.integer' => 'FORM.CLIENT.PAGE.INTEGER',
            'page.min' => 'FORM.CLIENT.PAGE.MIN',
            'per_page.integer' => 'FORM.CLIENT.PER_PAGE.INTEGER',
            'per_page.min' => 'FORM.CLIENT.PER_PAGE.MIN',
            'per_page.max' => 'FORM.CLIENT.PER_PAGE.MAX',
        ];
    }
}
