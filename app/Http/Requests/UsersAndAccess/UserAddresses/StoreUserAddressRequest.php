<?php

namespace App\Http\Requests\UsersAndAccess\UserAddresses;

use App\DTOs\UsersAndAccess\UserAddressData;
use App\Enums\UserRole;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreUserAddressRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();

        if (! $user || ! $user->is_active) {
            return false;
        }

        // Admin can add address for any user; non-admin can only add for themselves
        if ($user->hasRole(UserRole::Admin)) {
            return true;
        }

        return (int) $this->input('user_id') === (int) $user->id;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_primary' => $this->boolean('is_primary'),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'label' => ['required', 'string', 'max:100'],
            'building_or_unit' => ['nullable', 'string', 'max:150'],
            'address_detail' => ['required', 'string', 'max:1000'],
            'contact_phone' => ['nullable', 'string', 'max:30'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'is_primary' => ['nullable', 'boolean'],
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
            'user_id' => 'pengguna',
            'label' => 'label alamat',
            'building_or_unit' => 'gedung atau unit',
            'address_detail' => 'detail alamat',
            'contact_phone' => 'nomor telepon kontak',
            'postal_code' => 'kode pos',
            'is_primary' => 'alamat utama',
        ];
    }

    /**
     * Create a DTO instance from this request.
     */
    public function toDto(): UserAddressData
    {
        return UserAddressData::fromRequest($this);
    }
}
