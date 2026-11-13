<?php

namespace App\Http\Requests\Reporting\Reports;

use App\DTOs\Reporting\ReportData;
use Illuminate\Foundation\Http\FormRequest;

class StoreReportRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * Semua pengguna yang sudah terautentikasi dapat membuat laporan.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:200'],
            'description' => ['required', 'string'],
            'location_id' => ['required', 'integer', 'exists:locations,id'],
            'building_id' => ['required', 'integer', 'exists:buildings,id'],
            'room_id' => ['nullable', 'integer', 'exists:rooms,id'],
            'facility_id' => ['required', 'integer', 'exists:facilities,id'],
            'damage_category_id' => ['required', 'integer', 'exists:damage_categories,id'],
            'priority_id' => ['required', 'integer', 'exists:report_priorities,id'],
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
            'title.required' => 'Judul laporan wajib diisi.',
            'title.max' => 'Judul laporan maksimal 200 karakter.',
            'description.required' => 'Deskripsi kerusakan wajib diisi.',
            'location_id.required' => 'Lokasi wajib dipilih.',
            'location_id.exists' => 'Lokasi yang dipilih tidak valid.',
            'building_id.required' => 'Gedung wajib dipilih.',
            'building_id.exists' => 'Gedung yang dipilih tidak valid.',
            'room_id.exists' => 'Ruangan yang dipilih tidak valid.',
            'facility_id.required' => 'Fasilitas yang dilaporkan wajib dipilih.',
            'facility_id.exists' => 'Fasilitas yang dipilih tidak valid.',
            'damage_category_id.required' => 'Kategori kerusakan wajib dipilih.',
            'damage_category_id.exists' => 'Kategori kerusakan yang dipilih tidak valid.',
            'priority_id.required' => 'Prioritas laporan wajib dipilih.',
            'priority_id.exists' => 'Prioritas yang dipilih tidak valid.',
        ];
    }

    /**
     * Convert validated request to DTO.
     * Ticket number di-generate oleh service, bukan di sini.
     */
    public function toDto(): ReportData
    {
        return ReportData::fromRequest($this);
    }
}
