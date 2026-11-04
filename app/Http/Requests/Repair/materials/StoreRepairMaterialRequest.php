<?php

namespace App\Http\Requests\Repair\materials;

use Illuminate\Foundation\Http\FormRequest;

class StoreRepairMaterialRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'repair_id' => ['required', 'exists:repairs,id'],

            'materials' => ['required', 'array', 'min:1'],

            'materials.*.material_name' => [
                'required',
                'string',
                'max:150',
            ],

            'materials.*.material_type' => [
                'required',
                'string',
                'max:100',
            ],

            'materials.*.unit' => [
                'required',
                'string',
                'max:30',
            ],

            'materials.*.quantity' => [
                'required',
                'numeric',
                'gt:0',
            ],

            'materials.*.unit_price' => [
                'nullable',
                'numeric',
                'gte:0',
            ],

            'materials.*.notes' => [
                'nullable',
                'string',
            ],
        ];
    }
}
