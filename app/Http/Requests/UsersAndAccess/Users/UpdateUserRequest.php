<?php

namespace App\Http\Requests\UsersAndAccess\Users;

use App\DTOs\UsersAndAccess\UserData;
use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends FormRequest
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
        /** @var User|int|string|null $user */
        $user = $this->route('user');

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class, 'email')->ignore($user instanceof User ? $user->id : $user),
            ],
            'password' => ['nullable', 'string', Password::defaults(), 'confirmed'],
            'role_id' => ['nullable', 'integer', 'exists:roles,id'],
            'is_active' => ['nullable', 'boolean'],
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
            'name' => 'nama lengkap',
            'email' => 'alamat email',
            'password' => 'kata sandi',
            'role_id' => 'peran pengguna',
            'is_active' => 'status aktif',
        ];
    }

    /**
     * Transform validated data to UserData DTO.
     */
    public function toDto(): UserData
    {
        return UserData::fromRequest($this);
    }
}
