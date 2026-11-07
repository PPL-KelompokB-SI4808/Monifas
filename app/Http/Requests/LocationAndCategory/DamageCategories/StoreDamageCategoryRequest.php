<?php

namespace App\Http\Requests\LocationAndCategory\DamageCategories;

use App\DTOs\LocationAndCategory\DamageCategoryData;
use Illuminate\Foundation\Http\FormRequest;

class StoreDamageCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
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
            'code' => ['required', 'string', 'max:30', 'unique:damage_categories,code'],
            'name' => ['required', 'string', 'max:150'],
            'severity_hint' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ];
    }

    /**
     * Convert validated request to DTO.
     */
    public function toDto(): DamageCategoryData
    {
        return DamageCategoryData::fromRequest($this);
    }
}
