<?php

namespace App\Http\Requests\Supporting\Feedback;

use App\Enums\UserRole;
use App\Models\Feedback;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class RespondFeedbackRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * Khusus Supervisor atau Admin.
     */
    public function authorize(): bool
    {
        $user = $this->user();

        if ($user === null) {
            return false;
        }

        // Cek otorisasi: Khusus Supervisor atau Admin
        return $user->hasRole(UserRole::Admin) || $user->hasRole(UserRole::Supervisor);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'admin_response' => [
                'required',
                'string',
                'max:2000',
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'admin_response.required' => 'Tanggapan admin wajib diisi.',
            'admin_response.max' => 'Tanggapan admin maksimal 2000 karakter.',
        ];
    }
}
