<?php

namespace App\Http\Requests\Reporting\Reports;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;

class VerifyReportRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * Hanya Supervisor atau Admin yang dapat memverifikasi/menolak laporan.
     */
    public function authorize(): bool
    {
        $user = $this->user();

        return $user !== null
            && ($user->hasRole(UserRole::Supervisor) || $user->hasRole(UserRole::Admin));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'action' => ['required', 'string', 'in:verify,reject'],
            'rejection_reason' => ['required_if:action,reject', 'nullable', 'string'],
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
            'action.required' => 'Aksi verifikasi wajib dipilih.',
            'action.in' => 'Aksi verifikasi harus berupa "verify" (setujui) atau "reject" (tolak).',
            'rejection_reason.required_if' => 'Alasan penolakan wajib diisi jika laporan ditolak.',
        ];
    }

    /**
     * Determine if the action is to verify (approve) the report.
     */
    public function isVerify(): bool
    {
        return $this->input('action') === 'verify';
    }

    /**
     * Determine if the action is to reject the report.
     */
    public function isReject(): bool
    {
        return $this->input('action') === 'reject';
    }

    /**
     * Get the rejection reason (only meaningful when action = reject).
     */
    public function rejectionReason(): ?string
    {
        $reason = $this->input('rejection_reason');

        return is_string($reason) && trim($reason) !== '' ? trim($reason) : null;
    }
}
