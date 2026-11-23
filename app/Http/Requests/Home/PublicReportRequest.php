<?php

namespace App\Http\Requests\Home;

use Illuminate\Foundation\Http\FormRequest;

class PublicReportRequest extends FormRequest
{
    /**
     * Semua pengunjung (termasuk tamu tanpa akun) boleh mengirim laporan publik.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'reporter_name' => ['required', 'string', 'max:150'],
            'reporter_phone' => ['required', 'string', 'max:30'],
            'title' => ['required', 'string', 'max:200'],
            'description' => ['required', 'string', 'max:2000'],
            'location_id' => ['required', 'integer', 'exists:locations,id'],
            'building_id' => ['required', 'integer', 'exists:buildings,id'],
            'room_id' => ['nullable', 'integer', 'exists:rooms,id'],
            'facility_id' => ['required', 'integer', 'exists:facilities,id'],
            'damage_category_id' => ['required', 'integer', 'exists:damage_categories,id'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'reporter_name.required' => 'Nama pelapor wajib diisi.',
            'reporter_name.max' => 'Nama pelapor maksimal 150 karakter.',
            'reporter_phone.required' => 'Nomor telepon wajib diisi.',
            'reporter_phone.max' => 'Nomor telepon maksimal 30 karakter.',
            'title.required' => 'Judul laporan wajib diisi.',
            'title.max' => 'Judul laporan maksimal 200 karakter.',
            'description.required' => 'Deskripsi kerusakan wajib diisi.',
            'description.max' => 'Deskripsi maksimal 2.000 karakter.',
            'location_id.required' => 'Lokasi wajib dipilih.',
            'location_id.exists' => 'Lokasi yang dipilih tidak valid.',
            'building_id.required' => 'Gedung wajib dipilih.',
            'building_id.exists' => 'Gedung yang dipilih tidak valid.',
            'room_id.exists' => 'Ruangan yang dipilih tidak valid.',
            'facility_id.required' => 'Fasilitas yang dilaporkan wajib dipilih.',
            'facility_id.exists' => 'Fasilitas yang dipilih tidak valid.',
            'damage_category_id.required' => 'Kategori kerusakan wajib dipilih.',
            'damage_category_id.exists' => 'Kategori kerusakan yang dipilih tidak valid.',
        ];
    }
}
