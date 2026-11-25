<?php

namespace App\Http\Requests\MonitoringAndAnalytic\Dashboard;

use App\Enums\ReportStatus;
use App\Enums\UserRole;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DashboardFilterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();

        return $user !== null && (
            $user->hasRole(UserRole::Admin)
            || $user->hasRole(UserRole::Management)
            || $user->hasRole(UserRole::Supervisor)
        );
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'start_date' => ['nullable', 'date'],
            'end_date' => [
                'nullable',
                'date',
                Rule::when($this->filled('start_date'), ['after_or_equal:start_date']),
            ],
            'location_id' => ['nullable', 'integer', 'exists:locations,id'],
            'building_id' => ['nullable', 'integer', 'exists:buildings,id'],
            'facility_id' => ['nullable', 'integer', 'exists:facilities,id'],
            'status' => ['nullable', 'string', Rule::enum(ReportStatus::class)],
            'officer_id' => ['nullable', 'integer', 'exists:maintenance_officers,id'],
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
            'start_date.date' => 'Tanggal mulai tidak valid.',
            'end_date.date' => 'Tanggal akhir tidak valid.',
            'end_date.after_or_equal' => 'Tanggal akhir harus sama dengan atau setelah tanggal mulai.',
            'location_id.integer' => 'Lokasi harus berupa ID yang valid.',
            'location_id.exists' => 'Lokasi yang dipilih tidak valid.',
            'building_id.integer' => 'Gedung harus berupa ID yang valid.',
            'building_id.exists' => 'Gedung yang dipilih tidak valid.',
            'facility_id.integer' => 'Fasilitas harus berupa ID yang valid.',
            'facility_id.exists' => 'Fasilitas yang dipilih tidak valid.',
            'status.string' => 'Status laporan harus berupa teks.',
            'status.Illuminate\Validation\Rules\Enum' => 'Status laporan yang dipilih tidak valid.',
            'officer_id.integer' => 'Petugas harus berupa ID yang valid.',
            'officer_id.exists' => 'Petugas yang dipilih tidak valid.',
        ];
    }
}
