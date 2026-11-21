<?php

use App\Enums\CampaignStatus;
use App\Enums\UserRole;
use App\Models\Building;
use App\Models\FacilityCategory;
use App\Models\MaintenanceCampaign;
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

    $this->facilityUser = User::factory()->create([
        'name' => 'Pengguna Siti',
        'email' => 'user@monifas.id',
        'role_id' => $this->facilityUserRole->id,
        'is_active' => true,
    ]);

    $this->building = Building::factory()->create([
        'name' => 'Gedung Kuliah Bersama',
        'is_active' => true,
    ]);

    $this->category = FacilityCategory::factory()->create([
        'name' => 'Sistem Pendingin AC',
        'is_active' => true,
    ]);
});

test('tamu tidak dapat mengakses halaman manajemen kampanye', function () {
    $response = $this->get(route('supporting.campaigns.index'));

    $response->assertRedirect('/login');
});

test('pengguna non-admin ditolak saat mengakses manajemen kampanye', function () {
    $response = $this->actingAs($this->facilityUser)->get(route('supporting.campaigns.index'));

    $response->assertStatus(403);
});

test('admin dapat mengakses index dan melihat daftar kampanye edukasi', function () {
    MaintenanceCampaign::factory()->create([
        'title' => 'Edukasi Hemat Energi AC',
        'created_by' => $this->admin->id,
        'building_id' => $this->building->id,
        'facility_category_id' => $this->category->id,
    ]);

    $response = $this->actingAs($this->admin)->get(route('supporting.campaigns.index'));

    $response->assertOk();
    $response->assertSee('Edukasi Hemat Energi AC');
});

test('admin dapat mengakses halaman buat kampanye baru', function () {
    $response = $this->actingAs($this->admin)->get(route('supporting.campaigns.create'));

    $response->assertOk();
    $response->assertSee('Buat Kampanye Baru');
});

test('admin dapat membuat kampanye baru dengan data valid', function () {
    $startDate = now()->toDateString();
    $endDate = now()->addDays(14)->toDateString();

    $response = $this->actingAs($this->admin)->post(route('supporting.campaigns.store'), [
        'building_id' => $this->building->id,
        'facility_category_id' => $this->category->id,
        'title' => 'Kampanye Bersih Toilet & Sanitasi',
        'objective' => 'Meningkatkan kesadaran pengguna akan kebersihan toilet bersama.',
        'content' => 'Gunakan air secukupnya dan buang sampah pada tempat yang disediakan.',
        'banner_path' => 'campaigns/banners/toilet.jpg',
        'start_date' => $startDate,
        'end_date' => $endDate,
        'status' => 'active',
    ]);

    $response->assertRedirect(route('supporting.campaigns.index'));
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('maintenance_campaigns', [
        'title' => 'Kampanye Bersih Toilet & Sanitasi',
        'building_id' => $this->building->id,
        'facility_category_id' => $this->category->id,
        'created_by' => $this->admin->id,
        'status' => CampaignStatus::Active->value,
    ]);
});

test('validasi menolak saat end_date lebih awal dari start_date', function () {
    $startDate = now()->addDays(5)->toDateString();
    $endDate = now()->addDays(2)->toDateString();

    $response = $this->actingAs($this->admin)->post(route('supporting.campaigns.store'), [
        'building_id' => $this->building->id,
        'facility_category_id' => $this->category->id,
        'title' => 'Kampanye Tanggal Tidak Valid',
        'objective' => 'Tujuan singkat...',
        'content' => 'Materi edukasi...',
        'start_date' => $startDate,
        'end_date' => $endDate,
    ]);

    $response->assertSessionHasErrors(['end_date']);
});

test('validasi mewajibkan field building_id, facility_category_id, title, objective, content, start_date, dan end_date', function () {
    $response = $this->actingAs($this->admin)->post(route('supporting.campaigns.store'), []);

    $response->assertSessionHasErrors([
        'building_id',
        'facility_category_id',
        'title',
        'objective',
        'content',
        'start_date',
        'end_date',
    ]);
});

test('admin dapat melihat halaman detail kampanye', function () {
    $campaign = MaintenanceCampaign::factory()->create([
        'title' => 'Kampanye Detail Preview',
        'created_by' => $this->admin->id,
        'building_id' => $this->building->id,
        'facility_category_id' => $this->category->id,
    ]);

    $response = $this->actingAs($this->admin)->get(route('supporting.campaigns.show', $campaign));

    $response->assertOk();
    $response->assertSee('Kampanye Detail Preview');
});

test('admin dapat melihat halaman edit kampanye', function () {
    $campaign = MaintenanceCampaign::factory()->create([
        'title' => 'Kampanye Edit Preview',
        'created_by' => $this->admin->id,
        'building_id' => $this->building->id,
        'facility_category_id' => $this->category->id,
    ]);

    $response = $this->actingAs($this->admin)->get(route('supporting.campaigns.edit', $campaign));

    $response->assertOk();
    $response->assertSee('Kampanye Edit Preview');
});

test('admin dapat memperbarui data kampanye', function () {
    $campaign = MaintenanceCampaign::factory()->create([
        'created_by' => $this->admin->id,
        'building_id' => $this->building->id,
        'facility_category_id' => $this->category->id,
    ]);

    $response = $this->actingAs($this->admin)->put(route('supporting.campaigns.update', $campaign), [
        'title' => 'Judul Kampanye Terupdate',
        'objective' => 'Tujuan terupdate',
        'content' => 'Konten terupdate lengkap.',
        'start_date' => now()->toDateString(),
        'end_date' => now()->addDays(7)->toDateString(),
        'building_id' => $this->building->id,
        'facility_category_id' => $this->category->id,
    ]);

    $response->assertRedirect(route('supporting.campaigns.index'));
    $this->assertDatabaseHas('maintenance_campaigns', [
        'id' => $campaign->id,
        'title' => 'Judul Kampanye Terupdate',
    ]);
});

test('admin dapat membatalkan kampanye', function () {
    $campaign = MaintenanceCampaign::factory()->create([
        'created_by' => $this->admin->id,
        'building_id' => $this->building->id,
        'facility_category_id' => $this->category->id,
        'status' => CampaignStatus::Active,
    ]);

    $response = $this->actingAs($this->admin)->patch(route('supporting.campaigns.cancel', $campaign));

    $response->assertRedirect(route('supporting.campaigns.index'));
    $this->assertDatabaseHas('maintenance_campaigns', [
        'id' => $campaign->id,
        'status' => CampaignStatus::Cancelled->value,
    ]);
});

test('admin dapat menghapus kampanye secara soft delete', function () {
    $campaign = MaintenanceCampaign::factory()->create([
        'created_by' => $this->admin->id,
        'building_id' => $this->building->id,
        'facility_category_id' => $this->category->id,
    ]);

    $response = $this->actingAs($this->admin)->delete(route('supporting.campaigns.destroy', $campaign));

    $response->assertRedirect(route('supporting.campaigns.index'));
    $this->assertSoftDeleted('maintenance_campaigns', [
        'id' => $campaign->id,
    ]);
});
