<?php

namespace App\Http\Requests\LocationAndCategory\FacilityCategories;

use App\DTOs\LocationAndCategory\FacilityCategoryData;
use App\Models\FacilityCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateFacilityCategoryRequest extends FormRequest
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
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $category = $this->route('facility_category') ?? $this->route('category');
        $categoryId = $category instanceof FacilityCategory ? $category->id : $category;

        return [
            'code' => [
                'required',
                'string',
                'max:30',
                Rule::unique('facility_categories', 'code')->ignore($categoryId),
            ],
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ];
    }

    /**
     * Convert validated request to DTO.
     */
    public function toDto(): FacilityCategoryData
    {
        return FacilityCategoryData::fromRequest($this);
    }
}
