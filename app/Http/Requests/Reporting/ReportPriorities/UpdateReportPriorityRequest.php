<?php

namespace App\Http\Requests\Reporting\ReportPriorities;

use App\DTOs\Reporting\ReportPriorityData;
use App\Enums\PriorityLevel;
use App\Enums\UserRole;
use App\Models\ReportPriority;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateReportPriorityRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();

        return $user !== null && ($user->hasRole(UserRole::Admin) || $user->is_active);
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
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $priority = $this->route('report_priority') ?? $this->route('priority');
        $priorityId = $priority instanceof ReportPriority ? $priority->id : $priority;

        return [
            'name' => ['required', 'string', 'max:100'],
            'level' => [
                'required',
                'string',
                Rule::enum(PriorityLevel::class),
                Rule::unique('report_priorities', 'level')->ignore($priorityId),
            ],
            'sla_response_hours' => ['required', 'integer', 'min:1'],
            'sla_resolution_hours' => [
                'required',
                'integer',
                'min:1',
                'gte:sla_response_hours',
            ],
            'badge_variant' => ['required', 'string', 'max:30'],
            'sort_order' => ['nullable', 'integer', 'min:1', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ];
    }

    /**
     * Custom validation error messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'sla_response_hours.min' => 'Target jam respon awal (SLA Response) minimal 1 jam.',
            'sla_resolution_hours.min' => 'Target jam penyelesaian (SLA Resolution) minimal 1 jam.',
            'sla_resolution_hours.gte' => 'Target jam penyelesaian (SLA Resolution) harus lebih besar atau sama dengan target jam respon (SLA Response).',
            'level.unique' => 'Tingkat prioritas ini sudah terdaftar.',
        ];
    }

    /**
     * Convert validated request to DTO.
     */
    public function toDto(): ReportPriorityData
    {
        return ReportPriorityData::fromRequest($this);
    }
}
