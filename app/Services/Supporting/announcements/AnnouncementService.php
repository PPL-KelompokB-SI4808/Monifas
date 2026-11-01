<?php

namespace App\Services\Supporting\Announcements;

use App\DTOs\Supporting\AnnouncementData;
use App\Enums\AnnouncementStatus;
use App\Models\Announcement;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;

class AnnouncementService
{
    /**
     * Build filtered query for announcements listing.
     *
     * @param  array{status?: AnnouncementStatus|string|null, category?: string|null, target_role?: string|null, target_building_id?: int|null, search?: string|null, active_only?: bool|null}  $filters
     * @return Builder<Announcement>
     */
    public function filterAnnouncementsQuery(array $filters = []): Builder
    {
        return Announcement::query()
            ->with(['author', 'targetBuilding'])
            ->when(! empty($filters['status']), function (Builder $query) use ($filters) {
                $status = $filters['status'] instanceof AnnouncementStatus
                    ? $filters['status']->value
                    : (string) $filters['status'];
                $query->where('status', $status);
            })
            ->when(! empty($filters['category']), function (Builder $query) use ($filters) {
                $query->where('category', (string) $filters['category']);
            })
            ->when(! empty($filters['target_role']), function (Builder $query) use ($filters) {
                $query->where('target_role', (string) $filters['target_role']);
            })
            ->when(isset($filters['target_building_id']), function (Builder $query) use ($filters) {
                $query->where('target_building_id', $filters['target_building_id']);
            })
            ->when(! empty($filters['search']), function (Builder $query) use ($filters) {
                $search = trim((string) $filters['search']);
                $query->where(function (Builder $q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('content', 'like', "%{$search}%");
                });
            })
            ->when(! empty($filters['active_only']), function (Builder $query) {
                $now = Carbon::now();
                $query->where('status', AnnouncementStatus::Published)
                    ->where('publish_at', '<=', $now)
                    ->where('expires_at', '>=', $now);
            })
            ->latest('id');
    }

    /**
     * Get paginated announcements list based on filters.
     *
     * @param  array{status?: AnnouncementStatus|string|null, category?: string|null, target_role?: string|null, target_building_id?: int|null, search?: string|null, active_only?: bool|null}  $filters
     * @return LengthAwarePaginator<int, Announcement>
     */
    public function paginateAnnouncements(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->filterAnnouncementsQuery($filters)->paginate($perPage);
    }

    /**
     * Create a new announcement with automatic publishing vs scheduling resolution.
     */
    public function createAnnouncement(AnnouncementData $data): Announcement
    {
        $payload = $data->toArray();
        $payload['status'] = $this->resolveStatusOnSave($data);

        return Announcement::create($payload);
    }

    /**
     * Update an existing announcement.
     */
    public function updateAnnouncement(Announcement $announcement, AnnouncementData $data): Announcement
    {
        $payload = $data->toArray();
        $payload['status'] = $this->resolveStatusOnSave($data);

        $announcement->update($payload);

        return $announcement->fresh(['author', 'targetBuilding']) ?? $announcement;
    }

    /**
     * Delete an announcement.
     */
    public function deleteAnnouncement(Announcement $announcement): bool
    {
        return (bool) $announcement->delete();
    }

    /**
     * Cancel an announcement explicitly.
     */
    public function cancelAnnouncement(Announcement $announcement): Announcement
    {
        $announcement->update([
            'status' => AnnouncementStatus::Cancelled,
        ]);

        return $announcement;
    }

    /**
     * Archive an announcement explicitly.
     */
    public function archiveAnnouncement(Announcement $announcement): Announcement
    {
        $announcement->update([
            'status' => AnnouncementStatus::Archived,
        ]);

        return $announcement;
    }

    /**
     * Auto-publish scheduled announcements whose publish_at has passed.
     */
    public function publishDueScheduledAnnouncements(): int
    {
        $now = Carbon::now();

        return Announcement::query()
            ->where('status', AnnouncementStatus::Scheduled)
            ->where('publish_at', '<=', $now)
            ->where('expires_at', '>', $now)
            ->update([
                'status' => AnnouncementStatus::Published,
            ]);
    }

    /**
     * Auto-archive active/scheduled announcements that have expired.
     */
    public function archiveExpiredAnnouncements(): int
    {
        $now = Carbon::now();

        return Announcement::query()
            ->whereIn('status', [AnnouncementStatus::Published, AnnouncementStatus::Scheduled])
            ->where('expires_at', '<=', $now)
            ->update([
                'status' => AnnouncementStatus::Archived,
            ]);
    }

    /**
     * Get active announcement banners targeted for an authenticated user.
     *
     * @return Collection<int, Announcement>
     */
    public function getActiveBannersForUser(User $user, int $limit = 5): Collection
    {
        return Announcement::query()
            ->with(['author', 'targetBuilding'])
            ->activeForUser($user)
            ->latest('publish_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Resolve the status based on publication timing (Direct publish vs Auto-schedule).
     */
    protected function resolveStatusOnSave(AnnouncementData $data): AnnouncementStatus
    {
        $requestedStatus = $data->status instanceof AnnouncementStatus
            ? $data->status
            : AnnouncementStatus::tryFrom((string) $data->status);

        // Retain draft, cancelled, or archived status if explicitly specified.
        if (in_array($requestedStatus, [AnnouncementStatus::Draft, AnnouncementStatus::Cancelled, AnnouncementStatus::Archived], true)) {
            return $requestedStatus;
        }

        // If not explicitly draft/cancelled/archived, evaluate publish_at timing.
        if (! empty($data->publishAt)) {
            $publishAt = Carbon::parse($data->publishAt);
            $now = Carbon::now();

            if ($publishAt->isFuture()) {
                return AnnouncementStatus::Scheduled;
            }

            return AnnouncementStatus::Published;
        }

        return $requestedStatus ?? AnnouncementStatus::Draft;
    }
}

