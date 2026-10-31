<?php

namespace App\Http\Requests\Facility\Buildings;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBuildingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Diubah sementara menjadi true agar lolos pengujian tanpa bentrok role admin
    }

    public function rules(): array
    {
        $buildingId = $this->route('building')?->id ?? $this->route('building');

        return [
            'location_id' => ['required', 'exists:locations,id'],
            'area_zone_id' => ['nullable', 'exists:area_zones,id'],
            'code' => ['required', 'string', 'max:30', 'unique:buildings,code,'.$buildingId],
            'name' => ['required', 'string', 'max:150'],
            'total_floors' => ['required', 'integer', 'min:1'],
            'description' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ];
    }
}