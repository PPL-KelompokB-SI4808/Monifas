<?php

use App\Enums\NotificationType;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

test('unauthenticated user is redirected when accessing notification center', function () {
    $response = $this->get('/dashboard/users-and-access/notifications');

    $response->assertRedirect('/login');
});

test('authenticated user can view notification center page', function () {
    $user = User::factory()->create();
    Notification::factory()->count(3)->create(['user_id' => $user->id]);

    $response = $this->withoutVite()->actingAs($user)->get('/dashboard/users-and-access/notifications');

    $response->assertStatus(200);
    $response->assertViewIs('pages.dashboard.users-and-access.users.notifications');
    $response->assertViewHas('notifications');
    $response->assertViewHas('unreadCount');
});

test('authenticated user can fetch notifications as JSON', function () {
    $user = User::factory()->create();
    Notification::factory()->count(2)->create([
        'user_id' => $user->id,
        'type' => NotificationType::SystemAlert,
        'title' => 'Server Maintenance',
    ]);

    $response = $this->actingAs($user)->getJson('/dashboard/users-and-access/notifications');

    $response->assertStatus(200);
    $response->assertJsonStructure([
        'notifications',
        'unreadCount',
        'hasMore',
    ]);
    expect(count($response->json('notifications')))->toBe(2);
});

test('user can mark their notification as read via JSON request', function () {
    $user = User::factory()->create();
    $notification = Notification::factory()->unread()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->patchJson("/dashboard/users-and-access/notifications/{$notification->id}/read");

    $response->assertStatus(200);
    $response->assertJson([
        'success' => true,
    ]);

    expect($notification->fresh()->is_read)->toBeTrue();
    expect($notification->fresh()->read_at)->not->toBeNull();
});

test('user cannot mark another users notification as read', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $notification = Notification::factory()->unread()->create(['user_id' => $otherUser->id]);

    $response = $this->actingAs($user)->patchJson("/dashboard/users-and-access/notifications/{$notification->id}/read");

    $response->assertStatus(403);
    expect($notification->fresh()->is_read)->toBeFalse();
});

test('user can mark all their notifications as read', function () {
    $user = User::factory()->create();
    Notification::factory()->count(3)->unread()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->postJson('/dashboard/users-and-access/notifications/mark-all-read');

    $response->assertStatus(200);
    $response->assertJson([
        'success' => true,
        'count' => 3,
    ]);

    expect($user->notifications()->where('is_read', false)->count())->toBe(0);
});

test('user can delete their notification', function () {
    $user = User::factory()->create();
    $notification = Notification::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->deleteJson("/dashboard/users-and-access/notifications/{$notification->id}");

    $response->assertStatus(200);
    $response->assertJson([
        'success' => true,
    ]);

    $this->assertDatabaseMissing('notifications', ['id' => $notification->id]);
});

test('user cannot delete another users notification', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $notification = Notification::factory()->create(['user_id' => $otherUser->id]);

    $response = $this->actingAs($user)->deleteJson("/dashboard/users-and-access/notifications/{$notification->id}");

    $response->assertStatus(403);
    $this->assertDatabaseHas('notifications', ['id' => $notification->id]);
});

test('user can clear all their notifications', function () {
    $user = User::factory()->create();
    Notification::factory()->count(4)->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->postJson('/dashboard/users-and-access/notifications/clear-all');

    $response->assertStatus(200);
    $response->assertJson([
        'success' => true,
        'count' => 4,
    ]);

    expect($user->notifications()->count())->toBe(0);
});

