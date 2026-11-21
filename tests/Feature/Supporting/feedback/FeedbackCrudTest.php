<?php

use App\Enums\ReportStatus;
use App\Enums\UserRole;
use App\Models\Feedback;
use App\Models\Report;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    $this->adminRole = Role::create([
        'name' => 'Administrator',
        'slug' => UserRole::Admin->value,
    ]);

    $this->supervisorRole = Role::create([
        'name' => 'Supervisor',
        'slug' => UserRole::Supervisor->value,
    ]);

    $this->facilityUserRole = Role::create([
        'name' => 'Facility User',
        'slug' => UserRole::FacilityUser->value,
    ]);

    $this->admin = User::factory()->create([
        'role_id' => $this->adminRole->id,
        'is_active' => true,
    ]);

    $this->supervisor = User::factory()->create([
        'role_id' => $this->supervisorRole->id,
        'is_active' => true,
    ]);

    $this->reporter = User::factory()->create([
        'role_id' => $this->facilityUserRole->id,
        'is_active' => true,
    ]);

    $this->otherUser = User::factory()->create([
        'role_id' => $this->facilityUserRole->id,
        'is_active' => true,
    ]);
});

test('tamu diarahkan ke login saat mengakses halaman index feedback', function () {
    $response = $this->get(route('supporting.feedback.index'));

    $response->assertRedirect('/login');
});

test('pengguna terotentikasi dapat melihat daftar umpan balik', function () {
    $response = $this->actingAs($this->reporter)->get(route('supporting.feedback.index'));

    $response->assertOk();
    $response->assertViewIs('pages.dashboard.supporting.feedback.index');
});

test('pelapor dapat mengirim feedback untuk laporannya yang berstatus Completed', function () {
    $report = Report::factory()->completed()->create([
        'reporter_id' => $this->reporter->id,
    ]);

    $response = $this->actingAs($this->reporter)->post(route('supporting.feedback.store'), [
        'report_id' => $report->id,
        'rating' => 5,
        'comment' => 'Perbaikan sangat memuaskan dan AC kembali dingin.',
    ]);

    $feedback = Feedback::where('report_id', $report->id)->first();
    expect($feedback)->not->toBeNull();

    $response->assertRedirect(route('supporting.feedback.show', $feedback));
    $this->assertDatabaseHas('feedbacks', [
        'report_id' => $report->id,
        'user_id' => $this->reporter->id,
        'rating' => 5,
    ]);
});

test('pengguna lain dilarang mengirim feedback untuk laporan yang bukan miliknya', function () {
    $report = Report::factory()->completed()->create([
        'reporter_id' => $this->reporter->id,
    ]);

    $response = $this->actingAs($this->otherUser)->post(route('supporting.feedback.store'), [
        'report_id' => $report->id,
        'rating' => 4,
        'comment' => 'Mencoba menilai laporan orang lain.',
    ]);

    $response->assertForbidden();
});

test('pelapor tidak dapat mengirim feedback jika laporan belum Completed', function () {
    $report = Report::factory()->create([
        'reporter_id' => $this->reporter->id,
        'status' => ReportStatus::Pending,
    ]);

    $response = $this->actingAs($this->reporter)->post(route('supporting.feedback.store'), [
        'report_id' => $report->id,
        'rating' => 3,
        'comment' => 'Belum selesai tapi mencoba kirim review.',
    ]);

    $response->assertForbidden();
});

test('pelapor dapat mengedit ulasan miliknya sebelum ada tanggapan pengelola', function () {
    $report = Report::factory()->completed()->create(['reporter_id' => $this->reporter->id]);
    $feedback = Feedback::factory()->create([
        'report_id' => $report->id,
        'user_id' => $this->reporter->id,
        'rating' => 3,
        'comment' => 'Komentar lama',
        'admin_response' => null,
    ]);

    $response = $this->actingAs($this->reporter)->put(route('supporting.feedback.update', $feedback), [
        'rating' => 5,
        'comment' => 'Komentar baru setelah dicek ulang',
    ]);

    $response->assertRedirect(route('supporting.feedback.show', $feedback));
    $this->assertDatabaseHas('feedbacks', [
        'id' => $feedback->id,
        'rating' => 5,
        'comment' => 'Komentar baru setelah dicek ulang',
    ]);
});

test('pelapor dilarang mengedit ulasan yang sudah ditanggapi pengelola (isLocked == true)', function () {
    $report = Report::factory()->completed()->create(['reporter_id' => $this->reporter->id]);
    $feedback = Feedback::factory()->create([
        'report_id' => $report->id,
        'user_id' => $this->reporter->id,
        'rating' => 3,
        'comment' => 'Komentar awal',
        'admin_response' => 'Terima kasih atas penilaiannya.',
        'responded_by' => $this->admin->id,
        'responded_at' => now(),
    ]);

    $response = $this->actingAs($this->reporter)->put(route('supporting.feedback.update', $feedback), [
        'rating' => 1,
        'comment' => 'Ingin merusak rating setelah ditanggapi',
    ]);

    $response->assertForbidden();
});

test('pelapor dilarang menghapus ulasan yang sudah ditanggapi pengelola', function () {
    $report = Report::factory()->completed()->create(['reporter_id' => $this->reporter->id]);
    $feedback = Feedback::factory()->create([
        'report_id' => $report->id,
        'user_id' => $this->reporter->id,
        'rating' => 3,
        'admin_response' => 'Sudah kami evaluasi.',
        'responded_by' => $this->admin->id,
        'responded_at' => now(),
    ]);

    $response = $this->actingAs($this->reporter)->delete(route('supporting.feedback.destroy', $feedback));

    $response->assertSessionHas('error');
    $this->assertDatabaseHas('feedbacks', ['id' => $feedback->id]);
});

test('admin dan supervisor dapat memberikan tanggapan resmi pada feedback', function () {
    $report = Report::factory()->completed()->create(['reporter_id' => $this->reporter->id]);
    $feedback = Feedback::factory()->create([
        'report_id' => $report->id,
        'user_id' => $this->reporter->id,
        'admin_response' => null,
    ]);

    $response = $this->actingAs($this->supervisor)->post(route('supporting.feedback.respond', $feedback), [
        'admin_response' => 'Terima kasih atas apresiasinya. Teknisi kami akan terus meningkatkan kualitas layanan.',
    ]);

    $response->assertRedirect(route('supporting.feedback.show', $feedback));
    $this->assertDatabaseHas('feedbacks', [
        'id' => $feedback->id,
        'admin_response' => 'Terima kasih atas apresiasinya. Teknisi kami akan terus meningkatkan kualitas layanan.',
        'responded_by' => $this->supervisor->id,
    ]);
});

test('pengguna biasa ditolak saat mencoba memberikan tanggapan resmi pengelola', function () {
    $report = Report::factory()->completed()->create(['reporter_id' => $this->reporter->id]);
    $feedback = Feedback::factory()->create([
        'report_id' => $report->id,
        'user_id' => $this->reporter->id,
    ]);

    $response = $this->actingAs($this->otherUser)->post(route('supporting.feedback.respond', $feedback), [
        'admin_response' => 'Mencoba membalas sebagai user biasa.',
    ]);

    $response->assertForbidden();
});
