<?php

namespace App\Services\UsersAndAccess\Users;

use App\Enums\NotificationType;
use App\Models\Notification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class NotificationService
{
    /**
     * Send a notification to a specific user.
     * Can be invoked across modules (e.g., Reporting, Maintenance, SLA).
     */
    public function send(
        User|int $user,
        NotificationType|string $type,
        string $title,
        string $message,
        ?string $actionUrl = null
    ): Notification {
        $userId = $user instanceof User ? $user->id : $user;
        $typeEnum = $type instanceof NotificationType ? $type : NotificationType::fromString($type);

        return Notification::create([
            'user_id' => $userId,
            'type' => $typeEnum,
            'title' => $title,
            'message' => $message,
            'action_url' => $actionUrl,
            'is_read' => false,
            'read_at' => null,
        ]);
    }

    /**
     * Mark a single notification as read.
     */
    public function markAsRead(Notification $notification): bool
    {
        return $notification->update([
            'is_read' => true,
            'read_at' => Carbon::now(),
        ]);
    }

    /**
     * Mark all notifications of a user as read.
     */
    public function markAllAsRead(User|int $user): int
    {
        $userId = $user instanceof User ? $user->id : $user;

        return Notification::query()
            ->where('user_id', $userId)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => Carbon::now(),
            ]);
    }

    /**
     * Delete a single notification.
     */
    public function delete(Notification $notification): bool
    {
        return (bool) $notification->delete();
    }

    /**
     * Clear all notifications of a user.
     */
    public function clearAll(User|int $user): int
    {
        $userId = $user instanceof User ? $user->id : $user;

        return Notification::query()
            ->where('user_id', $userId)
            ->delete();
    }

    /**
     * Get paginated notifications for a user, optionally filtered by read/unread.
     *
     * @return LengthAwarePaginator<int, Notification>
     */
    public function getForUser(User|int $user, ?string $filter = null, int $perPage = 15): LengthAwarePaginator
    {
        $userId = $user instanceof User ? $user->id : $user;

        return Notification::query()
            ->where('user_id', $userId)
            ->when($filter === 'unread', fn (Builder $query) => $query->where('is_read', false))
            ->latest('id')
            ->paginate($perPage);
    }

    /**
     * Get recent notifications for a user (e.g. for navbar bell dropdown).
     *
     * @return Collection<int, Notification>
     */
    public function getRecentForUser(User|int $user, int $limit = 5): Collection
    {
        $userId = $user instanceof User ? $user->id : $user;

        return Notification::query()
            ->where('user_id', $userId)
            ->latest('id')
            ->take($limit)
            ->get();
    }

    /**
     * Get the count of unread notifications for a user.
     */
    public function getUnreadCount(User|int $user): int
    {
        $userId = $user instanceof User ? $user->id : $user;

        return Notification::query()
            ->where('user_id', $userId)
            ->where('is_read', false)
            ->count();
    }
}

