<?php

namespace App\Http\Requests\MotorCycle;

use App\Http\Requests\AppFormRequest;

class CreateClientMotorCycleRequest extends AppFormRequest {
    public function authorize(): bool {
        return true;
    }

    public function rules(): array {
        return [
            'brand' => [
                'required',
                'string',
                'max:100',
            ],
            'model' => [
                'required',
                'string',
                'max:100',
            ],
            'plate' => [
                'required',
                'string',
                'max:10',
            ],
            'renavam' => [
                'nullable',
                'string',
                'max:11',
            ],
            'chassis' => [
                'nullable',
                'string',
                'max:17',
            ],
            'year' => [
                'nullable',
                'integer',
                'digits:4',
                'min:1900',
            ],
            'color' => [
                'nullable',
                'string',
                'max:50',
            ],
            'engine_number' => [
                'nullable',
                'string',
                'max:50',
            ],
        ];
    }

    public function messages(): array {
        return [
            'brand.required' => 'FORM.MOTORCYCLE.BRAND.REQUIRED',
            'brand.string' => 'FORM.MOTORCYCLE.BRAND.STRING',
            'brand.max' => 'FORM.MOTORCYCLE.BRAND.MAX',
            'model.required' => 'FORM.MOTORCYCLE.MODEL.REQUIRED',
            'model.string' => 'FORM.MOTORCYCLE.MODEL.STRING',
            'model.max' => 'FORM.MOTORCYCLE.MODEL.MAX',
            'plate.required' => 'FORM.MOTORCYCLE.PLATE.REQUIRED',
            'plate.string' => 'FORM.MOTORCYCLE.PLATE.STRING',
            'plate.max' => 'FORM.MOTORCYCLE.PLATE.MAX',
            'renavam.string' => 'FORM.MOTORCYCLE.RENAVAM.STRING',
            'renavam.max' => 'FORM.MOTORCYCLE.RENAVAM.MAX',
            'chassis.string' => 'FORM.MOTORCYCLE.CHASSIS.STRING',
            'chassis.max' => 'FORM.MOTORCYCLE.CHASSIS.MAX',
            'year.integer' => 'FORM.MOTORCYCLE.YEAR.INTEGER',
            'year.digits' => 'FORM.MOTORCYCLE.YEAR.DIGITS',
            'year.min' => 'FORM.MOTORCYCLE.YEAR.MIN',
            'color.string' => 'FORM.MOTORCYCLE.COLOR.STRING',
            'color.max' => 'FORM.MOTORCYCLE.COLOR.MAX',
            'engine_number.string' => 'FORM.MOTORCYCLE.ENGINE_NUMBER.STRING',
            'engine_number.max' => 'FORM.MOTORCYCLE.ENGINE_NUMBER.MAX',
        ];
    }

}
