<?php

namespace App\Http\Requests\Supporting\Campaigns;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCampaignRequest extends FormRequest
{
    /**
     * Only admins can update campaigns.
     */
    public function authorize(): bool
    {
        return $this->user()?->hasRole(UserRole::Admin) ?? false;
    }

    /**
     * Validation rules for updating a campaign.
     * All fields are optional but validated if present.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'building_id' => ['sometimes', 'required', 'exists:buildings,id'],
            'facility_category_id' => ['sometimes', 'required', 'exists:facility_categories,id'],
            'title' => ['sometimes', 'required', 'string', 'max:200'],
            'objective' => ['sometimes', 'required', 'string'],
            'content' => ['sometimes', 'required', 'string'],
            'banner_path' => ['sometimes', 'nullable', 'string', 'max:255'],
            'start_date' => ['sometimes', 'required', 'date'],
            'end_date' => ['sometimes', 'required', 'date', 'after_or_equal:start_date'],
            'status' => ['sometimes', 'nullable', 'string', 'in:scheduled,active,ended,cancelled'],
        ];
    }
}
