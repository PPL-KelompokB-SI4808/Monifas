<?php

use App\Enums\NotificationType;
use App\Models\Notification;
use App\Models\User;
use App\Services\UsersAndAccess\Users\NotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

test('notification service can send a notification to a user', function () {
    $user = User::factory()->create();
    $service = new NotificationService;

    $notification = $service->send(
        user: $user,
        type: NotificationType::ReportStatusChanged,
        title: 'Status Laporan Diperbarui',
        message: 'Laporan #REP-01 telah selesai diperbaiki.',
        actionUrl: '/dashboard/reports/1'
    );

    expect($notification)->toBeInstanceOf(Notification::class)
        ->and($notification->user_id)->toBe($user->id)
        ->and($notification->type)->toBe(NotificationType::ReportStatusChanged)
        ->and($notification->title)->toBe('Status Laporan Diperbarui')
        ->and($notification->message)->toBe('Laporan #REP-01 telah selesai diperbaiki.')
        ->and($notification->action_url)->toBe('/dashboard/reports/1')
        ->and($notification->is_read)->toBeFalse()
        ->and($notification->read_at)->toBeNull();

    $this->assertDatabaseHas('notifications', [
        'id' => $notification->id,
        'user_id' => $user->id,
        'title' => 'Status Laporan Diperbarui',
        'is_read' => false,
    ]);
});

test('notification service supports sending by user id and string type', function () {
    $user = User::factory()->create();
    $service = new NotificationService;

    $notification = $service->send(
        user: $user->id,
        type: 'NewAssignment',
        title: 'Penugasan Baru',
        message: 'Anda ditugaskan pada pekerjaan perbaikan AC.'
    );

    expect($notification->type)->toBe(NotificationType::NewAssignment);
    expect($notification->user_id)->toBe($user->id);
});

test('notification service can mark a notification as read', function () {
    $user = User::factory()->create();
    $service = new NotificationService;

    $notification = Notification::factory()->unread()->create([
        'user_id' => $user->id,
    ]);

    expect($notification->is_read)->toBeFalse();

    $result = $service->markAsRead($notification);

    expect($result)->toBeTrue();
    expect($notification->fresh()->is_read)->toBeTrue();
    expect($notification->fresh()->read_at)->not->toBeNull();
});

test('notification service can mark all notifications as read for a user', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $service = new NotificationService;

    Notification::factory()->count(3)->unread()->create(['user_id' => $user->id]);
    $otherNotif = Notification::factory()->unread()->create(['user_id' => $otherUser->id]);

    $updatedCount = $service->markAllAsRead($user);

    expect($updatedCount)->toBe(3);
    expect($user->notifications()->where('is_read', false)->count())->toBe(0);
    expect($otherNotif->fresh()->is_read)->toBeFalse();
});

test('notification service can delete a notification', function () {
    $user = User::factory()->create();
    $service = new NotificationService;

    $notification = Notification::factory()->create(['user_id' => $user->id]);

    $deleted = $service->delete($notification);

    expect($deleted)->toBeTrue();
    $this->assertDatabaseMissing('notifications', ['id' => $notification->id]);
});

test('notification service can clear all notifications for a user', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $service = new NotificationService;

    Notification::factory()->count(4)->create(['user_id' => $user->id]);
    $otherNotif = Notification::factory()->create(['user_id' => $otherUser->id]);

    $clearedCount = $service->clearAll($user);

    expect($clearedCount)->toBe(4);
    expect($user->notifications()->count())->toBe(0);
    $this->assertDatabaseHas('notifications', ['id' => $otherNotif->id]);
});

test('notification service provides unread count and filtered pagination', function () {
    $user = User::factory()->create();
    $service = new NotificationService;

    Notification::factory()->count(2)->read()->create(['user_id' => $user->id]);
    Notification::factory()->count(3)->unread()->create(['user_id' => $user->id]);

    expect($service->getUnreadCount($user))->toBe(3);

    $allList = $service->getForUser($user, 'all');
    expect($allList->total())->toBe(5);

    $unreadList = $service->getForUser($user, 'unread');
    expect($unreadList->total())->toBe(3);
});

