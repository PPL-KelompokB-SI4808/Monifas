<?php

use App\Enums\AnnouncementStatus;
use App\Enums\UserRole;
use App\Models\Announcement;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    $this->adminRole = Role::create([
        'name' => 'Administrator',
        'slug' => UserRole::Admin->value,
        'description' => 'Akses penuh ke seluruh sistem',
    ]);

    $this->technicianRole = Role::create([
        'name' => 'Teknisi Lapangan',
        'slug' => UserRole::Technician->value,
        'description' => 'Petugas pemeliharaan fisik',
    ]);

    $this->facilityUserRole = Role::create([
        'name' => 'Pengguna Fasilitas',
        'slug' => UserRole::FacilityUser->value,
        'description' => 'Penghuni / Pelapor',
    ]);

    $this->admin = User::factory()->create([
        'name' => 'Admin Monifas',
        'email' => 'admin@monifas.id',
        'role_id' => $this->adminRole->id,
        'is_active' => true,
    ]);

    $this->technician = User::factory()->create([
        'name' => 'Teknisi Budi',
        'email' => 'technician@monifas.id',
        'role_id' => $this->technicianRole->id,
        'is_active' => true,
    ]);

    $this->facilityUser = User::factory()->create([
        'name' => 'Pengguna Siti',
        'email' => 'user@monifas.id',
        'role_id' => $this->facilityUserRole->id,
        'is_active' => true,
    ]);
});

test('tamu tidak dapat mengakses halaman manajemen pengumuman', function () {
    $response = $this->get(route('supporting.announcements.index'));

    $response->assertRedirect('/login');
});

test('pengguna non-admin ditolak saat mengakses manajemen pengumuman', function () {
    $response = $this->actingAs($this->facilityUser)->get(route('supporting.announcements.index'));

    $response->assertStatus(403);
});

test('admin dapat mengakses index dan melihat daftar pengumuman', function () {
    Announcement::factory()->create([
        'title' => 'Pemeliharaan AC Gedung A',
        'created_by' => $this->admin->id,
    ]);

    $response = $this->actingAs($this->admin)->get(route('supporting.announcements.index'));

    $response->assertOk();
    $response->assertSee('Pemeliharaan AC Gedung A');
});

test('admin dapat mengakses form buat pengumuman', function () {
    $response = $this->actingAs($this->admin)->get(route('supporting.announcements.create'));

    $response->assertOk();
    $response->assertSee('Buat Pengumuman Baru');
});

test('admin dapat membuat pengumuman baru dengan status langsung published saat publish_at saat ini', function () {
    $publishAt = now()->format('Y-m-d H:i:s');
    $expiresAt = now()->addDays(3)->format('Y-m-d H:i:s');

    $response = $this->actingAs($this->admin)->post(route('supporting.announcements.store'), [
        'title' => 'Pembersihan Tandon Air',
        'category' => 'Pemeliharaan Rutin',
        'content' => 'Pembersihan tandon air bersih akan dilakukan mulai pukul 08:00.',
        'target_role' => null,
        'publish_at' => $publishAt,
        'expires_at' => $expiresAt,
        'status' => 'published',
    ]);

    $response->assertRedirect(route('supporting.announcements.index'));
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('announcements', [
        'title' => 'Pembersihan Tandon Air',
        'category' => 'Pemeliharaan Rutin',
        'status' => AnnouncementStatus::Published->value,
    ]);
});

test('pengumuman dengan waktu publish_at di masa depan otomatis menjadi scheduled', function () {
    $publishAt = now()->addDays(2)->format('Y-m-d H:i:s');
    $expiresAt = now()->addDays(5)->format('Y-m-d H:i:s');

    $response = $this->actingAs($this->admin)->post(route('supporting.announcements.store'), [
        'title' => 'Jadwal Simulasi Gempa',
        'category' => 'Informasi Umum',
        'content' => 'Simulasi evakuasi kebakaran dan gempa akan diselenggarakan.',
        'publish_at' => $publishAt,
        'expires_at' => $expiresAt,
        'status' => 'published', // meskipun disubmit published, karena masa depan harus scheduled
    ]);

    $response->assertRedirect(route('supporting.announcements.index'));

    $this->assertDatabaseHas('announcements', [
        'title' => 'Jadwal Simulasi Gempa',
        'status' => AnnouncementStatus::Scheduled->value,
    ]);
});

test('validasi menolak rentang tanggal abnormal ketika expires_at sebelum atau sama dengan publish_at', function () {
    $publishAt = now()->addDays(5)->format('Y-m-d H:i:s');

    // Kasus 1: expires_at sebelum publish_at
    $response1 = $this->actingAs($this->admin)->post(route('supporting.announcements.store'), [
        'title' => 'Pengumuman Rentang Tidak Valid',
        'category' => 'Informasi Umum',
        'content' => 'Konten pengumuman...',
        'publish_at' => $publishAt,
        'expires_at' => now()->addDays(2)->format('Y-m-d H:i:s'),
    ]);

    $response1->assertSessionHasErrors(['expires_at']);

    // Kasus 2: expires_at sama persis dengan publish_at
    $response2 = $this->actingAs($this->admin)->post(route('supporting.announcements.store'), [
        'title' => 'Pengumuman Rentang Sama',
        'category' => 'Informasi Umum',
        'content' => 'Konten pengumuman...',
        'publish_at' => $publishAt,
        'expires_at' => $publishAt,
    ]);

    $response2->assertSessionHasErrors(['expires_at']);
});

test('validasi mewajibkan field judul, konten, kategori, publish_at, dan expires_at', function () {
    $response = $this->actingAs($this->admin)->post(route('supporting.announcements.store'), []);

    $response->assertSessionHasErrors(['title', 'content', 'category', 'publish_at', 'expires_at']);
});

test('kecocokan target role: pengumuman spesifik teknisi hanya tampil untuk teknisi', function () {
    // Pengumuman khusus teknisi
    $technicianAnnouncement = Announcement::factory()->create([
        'title' => 'Briefing Khusus Teknisi Lift',
        'target_role' => UserRole::Technician->value,
        'status' => AnnouncementStatus::Published,
        'publish_at' => now()->subHour(),
        'expires_at' => now()->addDays(2),
    ]);

    // Pengumuman global
    $globalAnnouncement = Announcement::factory()->create([
        'title' => 'Info Libur Fasilitas Umum',
        'target_role' => null,
        'status' => AnnouncementStatus::Published,
        'publish_at' => now()->subHour(),
        'expires_at' => now()->addDays(2),
    ]);

    // Pengumuman pengguna fasilitas
    $facilityUserAnnouncement = Announcement::factory()->create([
        'title' => 'Tagihan Air Bersih Bulanan',
        'target_role' => UserRole::FacilityUser->value,
        'status' => AnnouncementStatus::Published,
        'publish_at' => now()->subHour(),
        'expires_at' => now()->addDays(2),
    ]);

    // Teknisi query
    $technicianFeed = Announcement::activeForUser($this->technician)->get();
    expect($technicianFeed->pluck('id'))->toContain($technicianAnnouncement->id);
    expect($technicianFeed->pluck('id'))->toContain($globalAnnouncement->id);
    expect($technicianFeed->pluck('id'))->not->toContain($facilityUserAnnouncement->id);

    // Pengguna fasilitas query
    $userFeed = Announcement::activeForUser($this->facilityUser)->get();
    expect($userFeed->pluck('id'))->toContain($globalAnnouncement->id);
    expect($userFeed->pluck('id'))->toContain($facilityUserAnnouncement->id);
    expect($userFeed->pluck('id'))->not->toContain($technicianAnnouncement->id);
});

test('admin dapat melihat halaman edit pengumuman', function () {
    $announcement = Announcement::factory()->create(['created_by' => $this->admin->id]);

    $response = $this->actingAs($this->admin)->get(route('supporting.announcements.edit', $announcement));

    $response->assertOk();
    $response->assertSee($announcement->title);
});

test('admin dapat memperbarui data pengumuman', function () {
    $announcement = Announcement::factory()->create(['created_by' => $this->admin->id]);

    $response = $this->actingAs($this->admin)->put(route('supporting.announcements.update', $announcement), [
        'title' => 'Judul Baru Diperbarui',
        'category' => 'Keadaan Darurat',
        'content' => 'Konten darurat diperbarui.',
        'target_role' => null,
        'publish_at' => now()->subHour()->format('Y-m-d H:i:s'),
        'expires_at' => now()->addDays(1)->format('Y-m-d H:i:s'),
        'status' => 'published',
    ]);

    $response->assertRedirect(route('supporting.announcements.index'));
    $this->assertDatabaseHas('announcements', [
        'id' => $announcement->id,
        'title' => 'Judul Baru Diperbarui',
        'category' => 'Keadaan Darurat',
    ]);
});

test('admin dapat mengarsipkan pengumuman', function () {
    $announcement = Announcement::factory()->create([
        'status' => AnnouncementStatus::Published,
        'created_by' => $this->admin->id,
    ]);

    $response = $this->actingAs($this->admin)->patch(route('supporting.announcements.archive', $announcement));

    $response->assertRedirect(route('supporting.announcements.index'));
    $this->assertDatabaseHas('announcements', [
        'id' => $announcement->id,
        'status' => AnnouncementStatus::Archived->value,
    ]);
});

test('admin dapat menghapus pengumuman secara soft delete', function () {
    $announcement = Announcement::factory()->create(['created_by' => $this->admin->id]);

    $response = $this->actingAs($this->admin)->delete(route('supporting.announcements.destroy', $announcement));

    $response->assertRedirect(route('supporting.announcements.index'));
    $this->assertSoftDeleted('announcements', [
        'id' => $announcement->id,
    ]);
});
