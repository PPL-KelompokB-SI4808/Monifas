<?php

namespace App\Http\Requests\Repair\Repairs;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRepairRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'report_id' => [
                'sometimes',
                'nullable',
                'integer',
                'exists:reports,id',
            ],

            'started_at' => [
                'sometimes',
                'required',
                'date',
            ],

            'ended_at' => [
                'sometimes',
                'nullable',
                'date',
                'after_or_equal:started_at',
            ],

            'technical_action' => [
                'sometimes',
                'required',
                'string',
            ],

            'repair_result' => [
                'sometimes',
                'nullable',
                'string',
            ],

            'resulting_facility_condition' => [
                'sometimes',
                'required',
                'string',
                'max:30',
            ],

            'status' => [
                'sometimes',
                'in:draft,in_progress,completed',
            ],
        ];
    }
}