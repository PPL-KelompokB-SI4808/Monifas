<?php

namespace App\Http\Requests\Facility\Facilities;

use Illuminate\Foundation\Http\FormRequest;

class StoreFacilityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'location_id' => ['required', 'exists:locations,id'],
            'building_id' => ['required', 'exists:buildings,id'],
            'room_id' => ['nullable', 'exists:rooms,id'],
            'facility_category_id' => ['required', 'exists:facility_categories,id'],
            'code' => ['required', 'string', 'max:40', 'unique:facilities,code'],
            'name' => ['required', 'string', 'max:150'],
            'specifications' => ['nullable', 'string'],
            'condition' => ['required', 'string'],
            'is_active' => ['boolean'],
        ];
    }
}