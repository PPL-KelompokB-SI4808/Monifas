<?php

namespace App\Services\Supporting\Campaigns;

use App\Enums\CampaignStatus;
use App\Models\MaintenanceCampaign;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class CampaignService
{
    /**
     * Build filtered query for campaign listing.
     *
     * @param  array{status?: CampaignStatus|string|null,
     *                building_id?: int|null,
     *                facility_category_id?: int|null,
     *                search?: string|null,
     *                active_only?: bool|null}  $filters
     * @return Builder<MaintenanceCampaign>
     */
    public function filterCampaignsQuery(array $filters = []): Builder
    {
        return MaintenanceCampaign::query()
            ->when(! empty($filters['status']), function (Builder $q) use ($filters) {
                $status = $filters['status'] instanceof CampaignStatus
                    ? $filters['status']->value
                    : (string) $filters['status'];
                $q->where('status', $status);
            })
            ->when(! empty($filters['building_id']), fn (Builder $q) => $q->where('building_id', $filters['building_id']))
            ->when(! empty($filters['facility_category_id']), fn (Builder $q) => $q->where('facility_category_id', $filters['facility_category_id']))
            ->when(! empty($filters['search']), function (Builder $q) use ($filters) {
                $search = trim((string) $filters['search']);
                $q->where(function (Builder $inner) use ($search) {
                    $inner->where('title', 'like', "%{$search}%")
                          ->orWhere('content', 'like', "%{$search}%");
                });
            })
            ->when(! empty($filters['active_only']), function (Builder $q) {
                $today = Carbon::today()->toDateString();
                $q->where('status', CampaignStatus::Active->value)
                  ->whereDate('start_date', '<=', $today)
                  ->whereDate('end_date', '>=', $today);
            })
            ->latest('id');
    }

    /**
     * Paginate campaigns based on filters.
     */
    public function paginateCampaigns(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->filterCampaignsQuery($filters)->paginate($perPage);
    }

    /**
     * Create a new campaign.
     */
    public function createCampaign(array $data): MaintenanceCampaign
    {
        $payload = $data;
        $payload['status'] = $this->resolveStatusOnSave($data);
        return MaintenanceCampaign::create($payload);
    }

    /**
     * Update an existing campaign.
     */
    public function updateCampaign(MaintenanceCampaign $campaign, array $data): MaintenanceCampaign
    {
        $payload = $data;
        $payload['status'] = $this->resolveStatusOnSave($data, $campaign);
        $campaign->update($payload);
        return $campaign->fresh() ?? $campaign;
    }

    /**
     * Soft‑delete a campaign.
     */
    public function deleteCampaign(MaintenanceCampaign $campaign): bool
    {
        return (bool) $campaign->delete();
    }

    /**
     * Cancel a campaign explicitly.
     */
    public function cancelCampaign(MaintenanceCampaign $campaign): MaintenanceCampaign
    {
        $campaign->update(['status' => CampaignStatus::Cancelled]);
        return $campaign;
    }

    /**
     * Get active campaigns for a specific user.
     * (Further role/building logic can be added later.)
     *
     * @return Collection<int, MaintenanceCampaign>
     */
    public function getActiveCampaignsForUser(User $user, int $limit = 5): Collection
    {
        $today = Carbon::today()->toDateString();
        return MaintenanceCampaign::query()
            ->where('status', CampaignStatus::Active->value)
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->latest('start_date')
            ->limit($limit)
            ->get();
    }

    /**
     * Auto‑activate scheduled campaigns whose start_date has arrived.
     */
    public function activateDueScheduledCampaigns(): int
    {
        $today = Carbon::today()->toDateString();
        return MaintenanceCampaign::query()
            ->where('status', CampaignStatus::Scheduled->value)
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->update(['status' => CampaignStatus::Active]);
    }

    /**
     * Auto‑mark campaigns as ended when end_date passed.
     */
    public function endExpiredCampaigns(): int
    {
        $today = Carbon::today()->toDateString();
        return MaintenanceCampaign::query()
            ->where('status', CampaignStatus::Active->value)
            ->whereDate('end_date', '<', $today)
            ->update(['status' => CampaignStatus::Ended]);
    }

    /**
     * Resolve status on save based on supplied dates and optional explicit status.
     */
    protected function resolveStatusOnSave(array $data, ?MaintenanceCampaign $existing = null): string
    {
        if (! empty($data['status'])) {
            $explicit = CampaignStatus::tryFrom((string) $data['status']);
            if ($explicit) {
                return $explicit->value;
            }
        }

        $start = $data['start_date'] ?? $existing?->start_date;
        $end   = $data['end_date']   ?? $existing?->end_date;

        if ($start && $end) {
            $today = Carbon::today()->toDateString();
            if ($today < $start) {
                return CampaignStatus::Scheduled->value;
            }
            if ($today >= $start && $today <= $end) {
                return CampaignStatus::Active->value;
            }
            return CampaignStatus::Ended->value;
        }

        if (! empty($data['start_date'])) {
            return Carbon::parse($data['start_date'])->isFuture()
                ? CampaignStatus::Scheduled->value
                : CampaignStatus::Active->value;
        }

        return CampaignStatus::Active->value;
    }
}
