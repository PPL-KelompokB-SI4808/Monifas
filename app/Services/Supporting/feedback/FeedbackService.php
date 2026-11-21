<?php

namespace App\Services\Supporting\Feedback;

use App\DTOs\Supporting\FeedbackData;
use App\Enums\ReportStatus;
use App\Models\Feedback;
use App\Models\Report;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use InvalidArgumentException;
use RuntimeException;

class FeedbackService
{
    /**
     * Build filtered query for feedback listing.
     * Filter berdasarkan skor bintang (1-5), teknisi penanggung jawab, atau kategori fasilitas.
     *
     * @param  array{rating?: int|string|null, officer_id?: int|string|null, facility_category_id?: int|string|null, search?: string|null}  $filters
     * @return Builder<Feedback>
     */
    public function filterFeedbackQuery(array $filters = []): Builder
    {
        return Feedback::query()
            ->with([
                'user',
                'responder',
                'report.facility.category',
                'report.damageCategory',
                'repair.officer.user',
            ])
            // Filter skor bintang (1-5)
            ->when(! empty($filters['rating']), function (Builder $query) use ($filters) {
                $query->where('rating', (int) $filters['rating']);
            })
            // Filter teknisi penanggung jawab (melalui relasi repair atau assignment report)
            ->when(! empty($filters['officer_id']), function (Builder $query) use ($filters) {
                $officerId = (int) $filters['officer_id'];
                $query->where(function (Builder $q) use ($officerId) {
                    $q->whereHas('repair', function (Builder $repairQuery) use ($officerId) {
                        $repairQuery->where('officer_id', $officerId);
                    })->orWhereHas('report.repairs', function (Builder $repairsQuery) use ($officerId) {
                        $repairsQuery->where('officer_id', $officerId);
                    });
                });
            })
            // Filter kategori fasilitas (melalui relasi report -> facility -> facility_category_id)
            ->when(! empty($filters['facility_category_id']), function (Builder $query) use ($filters) {
                $categoryId = (int) $filters['facility_category_id'];
                $query->whereHas('report.facility', function (Builder $facilityQuery) use ($categoryId) {
                    $facilityQuery->where('facility_category_id', $categoryId);
                });
            })
            // Pencarian kata kunci pada komentar atau admin response
            ->when(! empty($filters['search']), function (Builder $query) use ($filters) {
                $search = trim((string) $filters['search']);
                $query->where(function (Builder $q) use ($search) {
                    $q->where('comment', 'like', "%{$search}%")
                        ->orWhere('admin_response', 'like', "%{$search}%")
                        ->orWhereHas('user', function (Builder $userQuery) use ($search) {
                            $userQuery->where('name', 'like', "%{$search}%");
                        })
                        ->orWhereHas('report', function (Builder $reportQuery) use ($search) {
                            $reportQuery->where('ticket_number', 'like', "%{$search}%")
                                ->orWhere('title', 'like', "%{$search}%");
                        });
                });
            })
            ->latest('id');
    }

    /**
     * Get paginated feedback list based on filters.
     *
     * @param  array{rating?: int|string|null, officer_id?: int|string|null, facility_category_id?: int|string|null, search?: string|null}  $filters
     * @return LengthAwarePaginator<Feedback>
     */
    public function getPaginatedFeedback(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->filterFeedbackQuery($filters)->paginate($perPage)->withQueryString();
    }

    /**
     * Create a new feedback for a report.
     * Verifikasi status laporan harus Completed sebelum pengajuan ulasan.
     *
     * @throws InvalidArgumentException
     */
    public function createFeedback(FeedbackData $data): Feedback
    {
        $report = Report::findOrFail($data->reportId);

        // Verifikasi status laporan harus Completed
        if ($report->status !== ReportStatus::Completed) {
            throw new InvalidArgumentException('Umpan balik hanya dapat diberikan untuk laporan dengan status Selesai (Completed).');
        }

        // Cari repair_id otomatis jika tidak dikirimkan eksplisit
        $repairId = $data->repairId;
        if (! $repairId) {
            $latestRepair = $report->repairs()->latest('id')->first();
            $repairId = $latestRepair?->id;
        }

        return Feedback::create([
            'report_id' => $report->id,
            'repair_id' => $repairId,
            'user_id' => $data->userId,
            'rating' => $data->rating,
            'comment' => $data->comment,
        ]);
    }

    /**
     * Update an existing feedback by the reporter.
     * Penguncian fitur edit milik pelapor apabila pengelola telah memberikan tanggapan resmi (isLocked == true).
     *
     * @throws RuntimeException|InvalidArgumentException
     */
    public function updateFeedback(Feedback $feedback, int $rating, string $comment): Feedback
    {
        // Penguncian fitur edit apabila pengelola telah memberikan tanggapan resmi
        if ($feedback->isLocked()) {
            throw new RuntimeException('Umpan balik telah ditanggapi oleh pihak pengelola dan tidak dapat diubah lagi.');
        }

        // Verifikasi status laporan harus tetap Completed
        $report = $feedback->report ?? Report::find($feedback->report_id);
        if (! $report instanceof Report || $report->status !== ReportStatus::Completed) {
            throw new InvalidArgumentException('Umpan balik hanya valid untuk laporan berstatus Selesai (Completed).');
        }

        if ($rating < 1 || $rating > 5) {
            throw new InvalidArgumentException('Rating harus berada di antara rentang 1 hingga 5.');
        }

        $feedback->update([
            'rating' => $rating,
            'comment' => $comment,
        ]);

        return $feedback;
    }

    /**
     * Delete an existing feedback by the reporter.
     * Penguncian fitur hapus milik pelapor apabila pengelola telah memberikan tanggapan resmi.
     *
     * @throws RuntimeException
     */
    public function deleteFeedback(Feedback $feedback): bool
    {
        // Penguncian fitur hapus apabila pengelola telah memberikan tanggapan resmi
        if ($feedback->isLocked()) {
            throw new RuntimeException('Umpan balik telah ditanggapi oleh pihak pengelola dan tidak dapat dihapus.');
        }

        return (bool) $feedback->delete();
    }

    /**
     * Provide admin / supervisor response to the feedback.
     */
    public function respondFeedback(Feedback $feedback, string $adminResponse, User $responder): Feedback
    {
        $feedback->update([
            'admin_response' => $adminResponse,
            'responded_by' => $responder->id,
            'responded_at' => Carbon::now(),
        ]);

        return $feedback;
    }
}
