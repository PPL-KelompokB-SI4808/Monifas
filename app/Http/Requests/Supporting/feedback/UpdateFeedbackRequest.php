<?php

namespace App\Http\Requests\Supporting\Feedback;

use App\Enums\ReportStatus;
use App\Models\Feedback;
use App\Models\Report;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateFeedbackRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * Menolak perubahan jika:
     * - User bukan pemilik feedback, ATAU
     * - Status laporan belum Completed, ATAU
     * - isLocked() === true (sudah ada admin_response)
     */
    public function authorize(): bool
    {
        $user = $this->user();

        if ($user === null) {
            return false;
        }

        /** @var Feedback|null $feedback */
        $feedback = $this->route('feedback');

        if (! $feedback instanceof Feedback) {
            $feedbackId = $this->route('feedback');
            $feedback = is_numeric($feedbackId) ? Feedback::find($feedbackId) : null;
        }

        if (! $feedback instanceof Feedback) {
            return false;
        }

        // Cek kepemilikan: hanya pelapor yang membuat feedback
        if ((int) $feedback->user_id !== (int) $user->id) {
            return false;
        }

        // Menolak jika feedback terkunci (sudah ada tanggapan admin)
        if ($feedback->isLocked()) {
            return false;
        }

        // Menolak jika status laporan belum/bukan Completed
        $report = $feedback->report ?? Report::find($feedback->report_id);
        if (! $report instanceof Report || $report->status !== ReportStatus::Completed) {
            return false;
        }

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
            'rating.required' => 'Rating wajib dipilih.',
            'rating.integer' => 'Rating harus berupa angka.',
            'rating.min' => 'Rating minimal 1 bintang.',
            'rating.max' => 'Rating maksimal 5 bintang.',
            'comment.required' => 'Komentar umpan balik wajib diisi.',
            'comment.max' => 'Komentar maksimal 1000 karakter.',
        ];
    }
}
