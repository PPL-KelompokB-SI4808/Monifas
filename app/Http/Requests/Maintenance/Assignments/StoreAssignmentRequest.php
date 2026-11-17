<?php

namespace App\Http\Requests\Maintenance\Assignments;

use App\Enums\UserRole;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreAssignmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();

        return $user !== null
            && ($user->hasRole(UserRole::Admin) || $user->hasRole(UserRole::Supervisor));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'report_id' => ['required', 'exists:reports,id'],
            'officer_id' => ['required', 'exists:maintenance_officers,id'],
            'instruction_notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Custom validation error messages in Indonesian.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'report_id.required' => 'Laporan wajib dipilih.',
            'report_id.exists' => 'Laporan yang dipilih tidak valid.',
            'officer_id.required' => 'Petugas wajib dipilih.',
            'officer_id.exists' => 'Petugas yang dipilih tidak valid.',
            'instruction_notes.string' => 'Instruksi penugasan harus berupa teks.',
            'instruction_notes.max' => 'Instruksi penugasan maksimal 1000 karakter.',
        ];
    }
}
