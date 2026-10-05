<?php

namespace App\Http\Requests\MotorCycle;

use App\Http\Requests\AppFormRequest;

class FindAllClientMotorCycleRequest extends AppFormRequest {
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
            'page.integer' => 'FORM.MOTORCYCLE.PAGE.INTEGER',
            'page.min' => 'FORM.MOTORCYCLE.PAGE.MIN',

            'per_page.integer' => 'FORM.MOTORCYCLE.PER_PAGE.INTEGER',
            'per_page.min' => 'FORM.MOTORCYCLE.PER_PAGE.MIN',
            'per_page.max' => 'FORM.MOTORCYCLE.PER_PAGE.MAX',
        ];
    }
}
