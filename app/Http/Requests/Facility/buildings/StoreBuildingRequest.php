<?php

namespace App\Http\Requests\Facility\Buildings;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;

class StoreBuildingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->hasRole(UserRole::Admin) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'location_id' => ['required', 'exists:locations,id'],
            'area_zone_id' => ['nullable', 'exists:area_zones,id'],
            'code' => ['required', 'string', 'max:30', 'unique:buildings,code'],
            'name' => ['required', 'string', 'max:150'],
            'total_floors' => ['required', 'integer', 'min:1'],
            'description' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ];
    }
}
