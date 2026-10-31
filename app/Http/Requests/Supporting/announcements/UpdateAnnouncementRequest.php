<?php

namespace App\Http\Requests\Supporting\Announcements;

use App\DTOs\Supporting\AnnouncementData;
use App\Enums\AnnouncementStatus;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

class UpdateAnnouncementRequest extends FormRequest
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
        return [
            'title' => ['required', 'string', 'max:200'],
            'content' => ['required', 'string'],
            'category' => ['required', 'string', 'max:50'],
            'target_role' => ['nullable', 'string', 'max:50'],
            'target_building_id' => ['nullable', 'integer'],
            'publish_at' => ['required', 'date'],
            'expires_at' => [
                'required',
                'date',
                'after:publish_at',
                function (string $attribute, mixed $value, Closure $fail): void {
                    if ($this->filled('publish_at') && $value) {
                        $publishAt = Carbon::parse($this->input('publish_at'));
                        $expiresAt = Carbon::parse($value);

                        if ($expiresAt->lessThanOrEqualTo($publishAt)) {
                            $fail('Waktu berakhir (expires_at) harus berada setelah waktu tayang (publish_at).');
                        }
                    }
                },
            ],
            'status' => ['nullable', Rule::enum(AnnouncementStatus::class)],
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
            'expires_at.after' => 'Waktu berakhir harus berada setelah waktu tayang.',
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
            'title' => 'judul pengumuman',
            'content' => 'isi pengumuman',
            'category' => 'kategori',
            'target_role' => 'target peran',
            'target_building_id' => 'target gedung',
            'publish_at' => 'waktu tayang',
            'expires_at' => 'waktu berakhir',
            'status' => 'status pengumuman',
        ];
    }

    /**
     * Transform validated data to AnnouncementData DTO.
     */
    public function toDto(): AnnouncementData
    {
        return AnnouncementData::fromRequest($this);
    }
}

