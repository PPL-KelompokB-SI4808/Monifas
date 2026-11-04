<?php

namespace App\Http\Controllers\UsersAndAccess\Users;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Services\UsersAndAccess\Users\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function __construct(
        protected NotificationService $notificationService
    ) {}

    /**
     * Display the notification center page or return JSON for AJAX/Alpine.
     */
    public function index(Request $request): View|JsonResponse
    {
        $user = $request->user();
        if ($user === null) {
            abort(401);
        }

        $filter = $request->query('filter', 'all');
        $notifications = $this->notificationService->getForUser($user, $filter, 15);
        $unreadCount = $this->notificationService->getUnreadCount($user);

        if ($request->wantsJson()) {
            return response()->json([
                'notifications' => $notifications->items(),
                'unreadCount' => $unreadCount,
                'hasMore' => $notifications->hasMorePages(),
            ]);
        }

        return view('pages.dashboard.users-and-access.users.notifications', [
            'notifications' => $notifications,
            'unreadCount' => $unreadCount,
            'currentFilter' => $filter,
        ]);
    }

    /**
     * Mark a single notification as read.
     */
    public function markAsRead(Request $request, Notification $notification): JsonResponse|RedirectResponse
    {
        $user = $request->user();
        abort_if($user === null || $notification->user_id !== $user->id, 403, 'Akses tidak diizinkan.');

        $this->notificationService->markAsRead($notification);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Notifikasi telah ditandai sebagai dibaca.',
            ]);
        }

        return back()->with('status', 'Notifikasi telah ditandai sebagai dibaca.');
    }

    /**
     * Mark all notifications of the authenticated user as read.
     */
    public function markAllAsRead(Request $request): JsonResponse|RedirectResponse
    {
        $user = $request->user();
        if ($user === null) {
            abort(401);
        }

        $count = $this->notificationService->markAllAsRead($user);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'count' => $count,
                'message' => 'Semua notifikasi telah ditandai sebagai dibaca.',
            ]);
        }

        return back()->with('status', 'Semua notifikasi telah ditandai sebagai dibaca.');
    }

    /**
     * Delete a single notification.
     */
    public function destroy(Request $request, Notification $notification): JsonResponse|RedirectResponse
    {
        $user = $request->user();
        abort_if($user === null || $notification->user_id !== $user->id, 403, 'Akses tidak diizinkan.');

        $this->notificationService->delete($notification);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Notifikasi berhasil dihapus.',
            ]);
        }

        return back()->with('status', 'Notifikasi berhasil dihapus.');
    }

    /**
     * Clear all notifications of the authenticated user.
     */
    public function clearAll(Request $request): JsonResponse|RedirectResponse
    {
        $user = $request->user();
        if ($user === null) {
            abort(401);
        }

        $count = $this->notificationService->clearAll($user);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'count' => $count,
                'message' => 'Semua notifikasi berhasil dibersihkan.',
            ]);
        }

        return back()->with('status', 'Semua notifikasi berhasil dibersihkan.');
    }
}

