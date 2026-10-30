<?php

namespace App\Http\Requests\Maintenance\Officers;

use App\Enums\OfficerAvailability;
use App\Models\MaintenanceOfficer;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateOfficerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        /** @var MaintenanceOfficer|int|string|null $officer */
        $officer = $this->route('officer');

        return [
            'user_id' => [
                'required',
                Rule::exists('users', 'id'),
                Rule::unique(MaintenanceOfficer::class, 'user_id')->ignore($officer instanceof MaintenanceOfficer ? $officer->id : $officer),
            ],
            'employee_code' => [
                'required',
                'string',
                'max:50',
                Rule::unique(MaintenanceOfficer::class, 'employee_code')->ignore($officer instanceof MaintenanceOfficer ? $officer->id : $officer),
            ],
            'specialization_notes' => ['nullable', 'string'],
            'availability_status' => [Rule::enum(OfficerAvailability::class)],
            'max_active_tasks' => ['required', 'integer', 'min:1'],
            'is_active' => ['boolean'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'user_id' => 'pengguna',
            'employee_code' => 'kode pegawai',
            'specialization_notes' => 'catatan spesialisasi',
            'availability_status' => 'status ketersediaan',
            'max_active_tasks' => 'batas tugas aktif',
            'is_active' => 'status aktif',
        ];
    }
}
