<?php

namespace App\Http\Requests\Facility\Buildings;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;

class UpdateBuildingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->hasRole(UserRole::Admin) ?? false;
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
