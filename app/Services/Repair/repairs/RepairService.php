<?php

namespace App\Services\Repair\repairs;

use App\Models\Repair;
use App\Models\Facility;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;

class RepairService
{
    /**
     * Mencatat pelaksanaan perbaikan.
     */
    public function create(array $data): Repair
    {
        return DB::transaction(function () use ($data) {
            return Repair::create($data);
        });
    }

    /**
     * Memperbarui pelaksanaan perbaikan.
     */
    public function update(Repair $repair, array $data): Repair
    {
        return DB::transaction(function () use ($repair, $data) {
            $repair->update($data);

            return $repair->refresh();
        });
    }

    /**
     * Menampilkan riwayat perbaikan dengan filter.
     */
    public function history(array $filters = [])
    {
        return Repair::query()
            ->with([
                'assignment',
                'report',
                'facility',
                'officer',
                'materials',
                'cost',
                'feedback',
            ])
            ->when(
                $filters['facility_id'] ?? null,
                fn (Builder $query, $facilityId) =>
                    $query->where('facility_id', $facilityId)
            )
            ->when(
                $filters['officer_id'] ?? null,
                fn (Builder $query, $officerId) =>
                    $query->where('officer_id', $officerId)
            )
            ->when(
                $filters['location_id'] ?? null,
                fn (Builder $query, $locationId) =>
                    $query->whereHas(
                        'facility',
                        fn (Builder $facilityQuery) =>
                            $facilityQuery->where('location_id', $locationId)
                    )
            )
            ->when(
                $filters['started_from'] ?? null,
                fn (Builder $query, $date) =>
                    $query->whereDate('started_at', '>=', $date)
            )
            ->when(
                $filters['started_until'] ?? null,
                fn (Builder $query, $date) =>
                    $query->whereDate('started_at', '<=', $date)
            )
            ->latest('started_at')
            ->paginate(15)
            ->withQueryString();
    }

    /**
     * Menyelesaikan perbaikan dan memperbarui kondisi fasilitas.
     */
    public function complete(Repair $repair, array $data): Repair
    {
        return DB::transaction(function () use ($repair, $data) {
            $repair->update([
                ...$data,
                'status' => 'completed',
                'ended_at' => $data['ended_at'] ?? now(),
            ]);

            $facility = Facility::query()
                ->lockForUpdate()
                ->findOrFail($repair->facility_id);

            $facility->update([
                'condition' => $repair->resulting_facility_condition,
            ]);

            return $repair->refresh();
        });
    }
}