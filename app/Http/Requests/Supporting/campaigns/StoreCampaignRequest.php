<?php

namespace App\Http\Requests\Supporting\Campaigns;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;

class StoreCampaignRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * Only admins can create campaigns.
     */
    public function authorize(): bool
    {
        return $this->user()?->hasRole(UserRole::Admin) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'building_id' => ['required', 'exists:buildings,id'],
            'facility_category_id' => ['required', 'exists:facility_categories,id'],
            'title' => ['required', 'string', 'max:200'],
            'objective' => ['required', 'string'],
            'content' => ['required', 'string'],
            'banner_path' => ['nullable', 'string', 'max:255'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'status' => ['nullable', 'string', 'in:scheduled,active,ended,cancelled'],
        ];
    }
}
