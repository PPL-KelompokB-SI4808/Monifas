<?php

namespace App\Http\Requests\Repair\materials;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRepairMaterialRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'material_name' => ['required', 'string', 'max:150'],
            'material_type' => ['required', 'string', 'max:100'],
            'unit' => ['required', 'string', 'max:30'],
            'quantity' => ['required', 'numeric', 'gt:0'],
            'unit_price' => ['nullable', 'numeric', 'gte:0'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
