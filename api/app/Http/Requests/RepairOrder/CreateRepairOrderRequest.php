<?php

namespace App\Http\Requests\RepairOrder;

use App\Enums\RepairStatusCode;
use App\Enums\UrgencyLevelCode;
use App\Http\Requests\AppFormRequest;
use Illuminate\Validation\Rule;

class CreateRepairOrderRequest extends AppFormRequest {
    public function authorize(): bool {
        return true;
    }

    public function rules(): array {
        return [
            'title' => [
                'required',
                'string',
                'max:255',
            ],
            'description' => [
                'required',
                'string',
            ],
            'status' => [
                'sometimes',
                'integer',
                Rule::enum(RepairStatusCode::class),
            ],
            'urgency_level' => [
                'sometimes',
                'integer',
                Rule::enum(UrgencyLevelCode::class),
            ],
            'diagnosis' => [
                'nullable',
                'string',
            ],
            'observations' => [
                'nullable',
                'string',
            ],

            // Peças da ordem de serviço
            'parts' => [
                'sometimes',
                'array',
            ],
            'parts.*' => [
                'required',
                'array',
            ],
            'parts.*.name' => [
                'required',
                'string',
                'max:255',
            ],
            'parts.*.description' => [
                'nullable',
                'string',
            ],
            'parts.*.quantity' => [
                'sometimes',
                'numeric',
                'gt:0',
                'decimal:0,2',
            ],
            'parts.*.unit_value' => [
                'required',
                'numeric',
                'min:0',
                'decimal:0,2',
            ],

            // Serviços da ordem de serviço
            'services' => [
                'sometimes',
                'array',
            ],
            'services.*' => [
                'required',
                'array',
            ],
            'services.*.name' => [
                'required',
                'string',
                'max:255',
            ],
            'services.*.description' => [
                'nullable',
                'string',
            ],
            'services.*.quantity' => [
                'sometimes',
                'numeric',
                'gt:0',
                'decimal:0,2',
            ],
            'services.*.unit_value' => [
                'required',
                'numeric',
                'min:0',
                'decimal:0,2',
            ],
        ];
    }

    public function messages(): array {
        return [
            'title.required' => 'FORM.REPAIR_ORDER.TITLE.REQUIRED',
            'title.string' => 'FORM.REPAIR_ORDER.TITLE.STRING',
            'title.max' => 'FORM.REPAIR_ORDER.TITLE.MAX',

            'description.required' => 'FORM.REPAIR_ORDER.DESCRIPTION.REQUIRED',
            'description.string' => 'FORM.REPAIR_ORDER.DESCRIPTION.STRING',

            'status.integer' => 'FORM.REPAIR_ORDER.STATUS.INTEGER',
            'status.enum' => 'FORM.REPAIR_ORDER.STATUS.INVALID',

            'urgency_level.integer' => 'FORM.REPAIR_ORDER.URGENCY_LEVEL.INTEGER',
            'urgency_level.enum' => 'FORM.REPAIR_ORDER.URGENCY_LEVEL.INVALID',

            'diagnosis.string' => 'FORM.REPAIR_ORDER.DIAGNOSIS.STRING',

            'observations.string' => 'FORM.REPAIR_ORDER.OBSERVATIONS.STRING',

            // Peças
            'parts.array' => 'FORM.REPAIR_ORDER.PARTS.ARRAY',
            'parts.*.required' => 'FORM.REPAIR_ORDER.PARTS.ITEM.REQUIRED',
            'parts.*.array' => 'FORM.REPAIR_ORDER.PARTS.ITEM.ARRAY',

            'parts.*.name.required' => 'FORM.REPAIR_ORDER.PARTS.NAME.REQUIRED',
            'parts.*.name.string' => 'FORM.REPAIR_ORDER.PARTS.NAME.STRING',
            'parts.*.name.max' => 'FORM.REPAIR_ORDER.PARTS.NAME.MAX',

            'parts.*.description.string' => 'FORM.REPAIR_ORDER.PARTS.DESCRIPTION.STRING',

            'parts.*.quantity.numeric' => 'FORM.REPAIR_ORDER.PARTS.QUANTITY.NUMERIC',
            'parts.*.quantity.gt' => 'FORM.REPAIR_ORDER.PARTS.QUANTITY.INVALID',
            'parts.*.quantity.decimal' => 'FORM.REPAIR_ORDER.PARTS.QUANTITY.DECIMAL',

            'parts.*.unit_value.required' => 'FORM.REPAIR_ORDER.PARTS.UNIT_VALUE.REQUIRED',
            'parts.*.unit_value.numeric' => 'FORM.REPAIR_ORDER.PARTS.UNIT_VALUE.NUMERIC',
            'parts.*.unit_value.min' => 'FORM.REPAIR_ORDER.PARTS.UNIT_VALUE.MIN',
            'parts.*.unit_value.decimal' => 'FORM.REPAIR_ORDER.PARTS.UNIT_VALUE.DECIMAL',

            // Serviços
            'services.array' => 'FORM.REPAIR_ORDER.SERVICES.ARRAY',
            'services.*.required' => 'FORM.REPAIR_ORDER.SERVICES.ITEM.REQUIRED',
            'services.*.array' => 'FORM.REPAIR_ORDER.SERVICES.ITEM.ARRAY',

            'services.*.name.required' => 'FORM.REPAIR_ORDER.SERVICES.NAME.REQUIRED',
            'services.*.name.string' => 'FORM.REPAIR_ORDER.SERVICES.NAME.STRING',
            'services.*.name.max' => 'FORM.REPAIR_ORDER.SERVICES.NAME.MAX',

            'services.*.description.string' => 'FORM.REPAIR_ORDER.SERVICES.DESCRIPTION.STRING',

            'services.*.quantity.numeric' => 'FORM.REPAIR_ORDER.SERVICES.QUANTITY.NUMERIC',
            'services.*.quantity.gt' => 'FORM.REPAIR_ORDER.SERVICES.QUANTITY.INVALID',
            'services.*.quantity.decimal' => 'FORM.REPAIR_ORDER.SERVICES.QUANTITY.DECIMAL',

            'services.*.unit_value.required' => 'FORM.REPAIR_ORDER.SERVICES.UNIT_VALUE.REQUIRED',
            'services.*.unit_value.numeric' => 'FORM.REPAIR_ORDER.SERVICES.UNIT_VALUE.NUMERIC',
            'services.*.unit_value.min' => 'FORM.REPAIR_ORDER.SERVICES.UNIT_VALUE.MIN',
            'services.*.unit_value.decimal' => 'FORM.REPAIR_ORDER.SERVICES.UNIT_VALUE.DECIMAL',
        ];
    }
}
