<?php

namespace App\Http\Requests\Supporting\Feedback;

use App\Enums\ReportStatus;
use App\Models\Report;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreFeedbackRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * Hanya pemilik laporan (reporter) yang dapat memberikan feedback,
     * dan status tiket laporan terkait harus sudah 'completed'.
     */
    public function authorize(): bool
    {
        $user = $this->user();

        if ($user === null) {
            return false;
        }

        $reportId = $this->input('report_id');

        if (! $reportId) {
            return false;
        }

        $report = Report::find($reportId);

        if (! $report instanceof Report) {
            return false;
        }

        // Memastikan pelapor adalah pemilik tiket laporan terkait dan status tiket sudah Completed
        $isOwner = (int) $report->reporter_id === (int) $user->id;
        $isCompleted = $report->status === ReportStatus::Completed;

        return $isOwner && $isCompleted;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'report_id' => [
                'required',
                'integer',
                'exists:reports,id',
                'unique:feedbacks,report_id',
            ],
            'repair_id' => [
                'nullable',
                'integer',
                'exists:repairs,id',
            ],
            'rating' => [
                'required',
                'integer',
                'min:1',
                'max:5',
            ],
            'comment' => [
                'required',
                'string',
                'max:1000',
            ],
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
            'report_id.required' => 'ID laporan wajib diisi.',
            'report_id.exists' => 'Laporan tidak ditemukan.',
            'report_id.unique' => 'Feedback untuk laporan ini sudah pernah diberikan.',
            'repair_id.exists' => 'Data perbaikan tidak ditemukan.',
            'rating.required' => 'Rating wajib dipilih.',
            'rating.integer' => 'Rating harus berupa angka.',
            'rating.min' => 'Rating minimal 1 bintang.',
            'rating.max' => 'Rating maksimal 5 bintang.',
            'comment.required' => 'Komentar umpan balik wajib diisi.',
            'comment.max' => 'Komentar maksimal 1000 karakter.',
        ];
    }
}
