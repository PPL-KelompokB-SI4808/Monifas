<?php

namespace App\Http\Requests\LocationAndCategory\Locations;

use App\DTOs\LocationAndCategory\LocationData;
use Illuminate\Foundation\Http\FormRequest;

class StoreLocationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('is_active')) {
            $this->merge([
                'is_active' => $this->boolean('is_active'),
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'parent_id' => ['nullable', 'integer', 'exists:locations,id'],
            'code' => ['required', 'string', 'max:30', 'unique:locations,code'],
            'name' => ['required', 'string', 'max:150'],
            'region' => ['required', 'string', 'max:100'],
            'address' => ['required', 'string'],
            'description' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ];
    }

    /**
     * Convert validated request to DTO.
     */
    public function toDto(): LocationData
    {
        return LocationData::fromRequest($this);
    }
}
