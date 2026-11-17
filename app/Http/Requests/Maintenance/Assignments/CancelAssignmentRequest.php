<?php

namespace App\Http\Requests\Maintenance\Assignments;

use App\Enums\UserRole;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CancelAssignmentRequest extends FormRequest
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
            'reason.required' => 'Alasan pembatalan penugasan wajib diisi.',
            'reason.string' => 'Alasan pembatalan penugasan harus berupa teks.',
            'reason.max' => 'Alasan pembatalan penugasan maksimal 1000 karakter.',
        ];
    }
}
