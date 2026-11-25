<?php

namespace App\Http\Requests\MonitoringAndAnalytic\MaintenanceReport;

use App\Enums\RepairExecutionStatus;
use App\Enums\UserRole;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MaintenanceReportFilterRequest extends FormRequest
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
            'period' => ['nullable', 'string', 'in:month,quarter,year,custom'],
            'start_date' => ['nullable', 'date'],
            'end_date' => [
                'nullable',
                'date',
                Rule::when($this->filled('start_date'), ['after_or_equal:start_date']),
            ],
            'location_id' => ['nullable', 'integer', 'exists:locations,id'],
            'building_id' => ['nullable', 'integer', 'exists:buildings,id'],
            'facility_id' => ['nullable', 'integer', 'exists:facilities,id'],
            'facility_category_id' => ['nullable', 'integer', 'exists:facility_categories,id'],
            'damage_category_id' => ['nullable', 'integer', 'exists:damage_categories,id'],
            'officer_id' => ['nullable', 'integer', 'exists:maintenance_officers,id'],
            'status' => ['nullable', 'string', Rule::enum(RepairExecutionStatus::class)],
            'search' => ['nullable', 'string', 'max:100'],
            'export' => ['nullable', 'string', 'in:csv,print'],
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
            'period.in' => 'Periode laporan harus berupa month, quarter, year, atau custom.',
            'start_date.date' => 'Tanggal mulai tidak valid.',
            'end_date.date' => 'Tanggal akhir tidak valid.',
            'end_date.after_or_equal' => 'Tanggal akhir harus sama dengan atau setelah tanggal mulai.',
            'location_id.integer' => 'Lokasi harus berupa ID yang valid.',
            'location_id.exists' => 'Lokasi yang dipilih tidak valid.',
            'building_id.integer' => 'Gedung harus berupa ID yang valid.',
            'building_id.exists' => 'Gedung yang dipilih tidak valid.',
            'facility_id.integer' => 'Fasilitas harus berupa ID yang valid.',
            'facility_id.exists' => 'Fasilitas yang dipilih tidak valid.',
            'facility_category_id.integer' => 'Kategori fasilitas harus berupa ID yang valid.',
            'facility_category_id.exists' => 'Kategori fasilitas yang dipilih tidak valid.',
            'damage_category_id.integer' => 'Kategori kerusakan harus berupa ID yang valid.',
            'damage_category_id.exists' => 'Kategori kerusakan yang dipilih tidak valid.',
            'officer_id.integer' => 'Petugas harus berupa ID yang valid.',
            'officer_id.exists' => 'Petugas yang dipilih tidak valid.',
            'status.Illuminate\Validation\Rules\Enum' => 'Status perbaikan yang dipilih tidak valid.',
            'search.max' => 'Kata kunci pencarian maksimal 100 karakter.',
            'export.in' => 'Format ekspor harus berupa csv atau print.',
        ];
    }
}
