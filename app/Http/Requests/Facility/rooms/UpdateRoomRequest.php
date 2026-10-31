<?php

namespace App\Http\Requests\Facility\Rooms;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRoomRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $roomId = $this->route('room')?->id ?? $this->route('room');

        return [
            'building_id' => ['required', 'exists:buildings,id'],
            'code' => ['required', 'string', 'max:30', 'unique:rooms,code,'.$roomId],
            'name' => ['required', 'string', 'max:150'],
            'floor' => ['required', 'integer', 'min:1'],
            'capacity' => ['nullable', 'integer', 'min:1'],
            'description' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ];
    }
}