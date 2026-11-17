<?php

namespace App\Http\Requests\Maintenance\Assignments;

use App\Enums\UserRole;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ReassignAssignmentRequest extends FormRequest
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
            'officer_id' => ['required', 'exists:maintenance_officers,id'],
            'reason' => ['required', 'string', 'max:1000'],
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
            'officer_id.required' => 'Petugas baru wajib dipilih.',
            'officer_id.exists' => 'Petugas yang dipilih tidak valid.',
            'reason.required' => 'Alasan pengalihan penugasan wajib diisi.',
            'reason.string' => 'Alasan pengalihan penugasan harus berupa teks.',
            'reason.max' => 'Alasan pengalihan penugasan maksimal 1000 karakter.',
        ];
    }
}
