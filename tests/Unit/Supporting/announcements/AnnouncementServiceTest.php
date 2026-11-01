<?php

use App\DTOs\Supporting\AnnouncementData;
use App\Enums\AnnouncementStatus;
use App\Enums\UserRole;
use App\Models\Announcement;
use App\Models\Role;
use App\Models\User;
use App\Services\Supporting\Announcements\AnnouncementService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    $this->service = new AnnouncementService;

    $this->adminRole = Role::create([
        'name' => 'Administrator',
        'slug' => UserRole::Admin->value,
    ]);

    $this->technicianRole = Role::create([
        'name' => 'Technician',
        'slug' => UserRole::Technician->value,
    ]);

    $this->admin = User::factory()->create([
        'role_id' => $this->adminRole->id,
        'is_active' => true,
    ]);

    $this->technician = User::factory()->create([
        'role_id' => $this->technicianRole->id,
        'is_active' => true,
    ]);
});

test('AnnouncementService filterAnnouncementsQuery filters by status, category, target_role, and search', function () {
    $a1 = Announcement::factory()->create([
        'title' => 'Pemeliharaan AC Tower A',
        'content' => 'Pembersihan filter udara.',
        'category' => 'Pemeliharaan Rutin',
        'status' => AnnouncementStatus::Published,
        'target_role' => UserRole::Technician->value,
        'created_by' => $this->admin->id,
    ]);

    $a2 = Announcement::factory()->create([
        'title' => 'Evakuasi Kebakaran Darurat',
        'content' => 'Alarm berbunyi pukul 10.',
        'category' => 'Keadaan Darurat',
        'status' => AnnouncementStatus::Draft,
        'target_role' => null,
        'created_by' => $this->admin->id,
    ]);

    // Filter by status
    $statusFiltered = $this->service->filterAnnouncementsQuery(['status' => AnnouncementStatus::Published])->get();
    expect($statusFiltered)->toHaveCount(1);
    expect($statusFiltered->first()->id)->toBe($a1->id);

    // Filter by category
    $categoryFiltered = $this->service->filterAnnouncementsQuery(['category' => 'Keadaan Darurat'])->get();
    expect($categoryFiltered)->toHaveCount(1);
    expect($categoryFiltered->first()->id)->toBe($a2->id);

    // Filter by target_role
    $roleFiltered = $this->service->filterAnnouncementsQuery(['target_role' => UserRole::Technician->value])->get();
    expect($roleFiltered)->toHaveCount(1);
    expect($roleFiltered->first()->id)->toBe($a1->id);

    // Search keyword
    $searchFiltered = $this->service->filterAnnouncementsQuery(['search' => 'Evakuasi'])->get();
    expect($searchFiltered)->toHaveCount(1);
    expect($searchFiltered->first()->id)->toBe($a2->id);

    // Paginate
    $paginated = $this->service->paginateAnnouncements([], 10);
    expect($paginated->total())->toBe(2);
});

test('AnnouncementService resolves scheduled vs published status automatically during create and update', function () {
    // 1. Direct publish when publish_at is past/current
    $directData = AnnouncementData::fromArray([
        'title' => 'Pengumuman Langsung',
        'content' => 'Konten langsung...',
        'category' => 'Informasi Umum',
        'created_by' => $this->admin->id,
        'publish_at' => now()->subHour()->toDateTimeString(),
        'expires_at' => now()->addDays(2)->toDateTimeString(),
        'status' => 'published',
    ]);

    $directAnnouncement = $this->service->createAnnouncement($directData);
    expect($directAnnouncement->status)->toBe(AnnouncementStatus::Published);

    // 2. Scheduled when publish_at is in future
    $futureData = AnnouncementData::fromArray([
        'title' => 'Pengumuman Terjadwal',
        'content' => 'Konten masa depan...',
        'category' => 'Informasi Umum',
        'created_by' => $this->admin->id,
        'publish_at' => now()->addDays(2)->toDateTimeString(),
        'expires_at' => now()->addDays(5)->toDateTimeString(),
        'status' => 'published',
    ]);

    $futureAnnouncement = $this->service->createAnnouncement($futureData);
    expect($futureAnnouncement->status)->toBe(AnnouncementStatus::Scheduled);

    // 3. Draft status is retained
    $draftData = AnnouncementData::fromArray([
        'title' => 'Pengumuman Konsep',
        'content' => 'Masih draft...',
        'category' => 'Informasi Umum',
        'created_by' => $this->admin->id,
        'publish_at' => now()->subHour()->toDateTimeString(),
        'expires_at' => now()->addDays(2)->toDateTimeString(),
        'status' => 'draft',
    ]);

    $draftAnnouncement = $this->service->createAnnouncement($draftData);
    expect($draftAnnouncement->status)->toBe(AnnouncementStatus::Draft);
});

test('AnnouncementService publishDueScheduledAnnouncements publishes due scheduled announcements', function () {
    // Pengumuman terjadwal yang sudah waktunya tayang
    $dueAnnouncement = Announcement::factory()->create([
        'status' => AnnouncementStatus::Scheduled,
        'publish_at' => now()->subMinutes(10),
        'expires_at' => now()->addDays(2),
    ]);

    // Pengumuman terjadwal yang belum waktunya tayang
    $futureAnnouncement = Announcement::factory()->create([
        'status' => AnnouncementStatus::Scheduled,
        'publish_at' => now()->addDays(2),
        'expires_at' => now()->addDays(5),
    ]);

    $count = $this->service->publishDueScheduledAnnouncements();

    expect($count)->toBe(1);
    expect($dueAnnouncement->fresh()->status)->toBe(AnnouncementStatus::Published);
    expect($futureAnnouncement->fresh()->status)->toBe(AnnouncementStatus::Scheduled);
});

test('AnnouncementService archiveExpiredAnnouncements archives expired active announcements', function () {
    // Pengumuman yang sudah kedaluwarsa
    $expiredAnnouncement = Announcement::factory()->create([
        'status' => AnnouncementStatus::Published,
        'publish_at' => now()->subDays(5),
        'expires_at' => now()->subHour(),
    ]);

    // Pengumuman yang masih aktif
    $activeAnnouncement = Announcement::factory()->create([
        'status' => AnnouncementStatus::Published,
        'publish_at' => now()->subDay(),
        'expires_at' => now()->addDays(3),
    ]);

    $count = $this->service->archiveExpiredAnnouncements();

    expect($count)->toBe(1);
    expect($expiredAnnouncement->fresh()->status)->toBe(AnnouncementStatus::Archived);
    expect($activeAnnouncement->fresh()->status)->toBe(AnnouncementStatus::Published);
});

test('AnnouncementService archiveAnnouncement and cancelAnnouncement update status accordingly', function () {
    $announcement = Announcement::factory()->create(['status' => AnnouncementStatus::Published]);

    $this->service->archiveAnnouncement($announcement);
    expect($announcement->fresh()->status)->toBe(AnnouncementStatus::Archived);

    $this->service->cancelAnnouncement($announcement);
    expect($announcement->fresh()->status)->toBe(AnnouncementStatus::Cancelled);
});

test('AnnouncementService getActiveBannersForUser retrieves only relevant announcements for authenticated user', function () {
    // Khusus teknisi
    $technicianOnly = Announcement::factory()->create([
        'title' => 'Pengumuman Teknisi',
        'target_role' => UserRole::Technician->value,
        'status' => AnnouncementStatus::Published,
        'publish_at' => now()->subHour(),
        'expires_at' => now()->addDays(2),
    ]);

    // Khusus supervisor
    $supervisorOnly = Announcement::factory()->create([
        'title' => 'Pengumuman Supervisor',
        'target_role' => UserRole::Supervisor->value,
        'status' => AnnouncementStatus::Published,
        'publish_at' => now()->subHour(),
        'expires_at' => now()->addDays(2),
    ]);

    // Banners for technician
    $banners = $this->service->getActiveBannersForUser($this->technician);

    expect($banners->pluck('id'))->toContain($technicianOnly->id);
    expect($banners->pluck('id'))->not->toContain($supervisorOnly->id);
});
