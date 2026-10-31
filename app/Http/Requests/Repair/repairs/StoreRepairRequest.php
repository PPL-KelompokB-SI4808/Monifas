<?php

namespace App\Http\Requests\Repair\Repairs;

use Illuminate\Foundation\Http\FormRequest;

class StoreRepairRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'assignment_id' => [
                'required',
                'integer',
                'exists:assignments,id',
            ],

            'report_id' => [
                'nullable',
                'integer',
                'exists:reports,id',
            ],

            'facility_id' => [
                'required',
                'integer',
                'exists:facilities,id',
            ],

            'officer_id' => [
                'required',
                'integer',
                'exists:maintenance_officers,id',
            ],

            'started_at' => [
                'required',
                'date',
            ],

            'ended_at' => [
                'nullable',
                'date',
                'after_or_equal:started_at',
            ],

            'technical_action' => [
                'required',
                'string',
            ],

            'repair_result' => [
                'nullable',
                'string',
            ],

            'resulting_facility_condition' => [
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