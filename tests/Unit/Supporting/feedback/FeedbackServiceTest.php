<?php

use App\DTOs\Supporting\FeedbackData;
use App\Enums\ReportStatus;
use App\Enums\UserRole;
use App\Models\Facility;
use App\Models\FacilityCategory;
use App\Models\Feedback;
use App\Models\Report;
use App\Models\Role;
use App\Models\User;
use App\Services\Supporting\Feedback\FeedbackService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    $this->service = new FeedbackService;

    $this->adminRole = Role::create([
        'name' => 'Administrator',
        'slug' => UserRole::Admin->value,
    ]);

    $this->facilityUserRole = Role::create([
        'name' => 'Facility User',
        'slug' => UserRole::FacilityUser->value,
    ]);

    $this->admin = User::factory()->create([
        'role_id' => $this->adminRole->id,
        'is_active' => true,
    ]);

    $this->reporter = User::factory()->create([
        'role_id' => $this->facilityUserRole->id,
        'is_active' => true,
    ]);
});

test('FeedbackService createFeedback berhasil saat status laporan Completed', function () {
    $report = Report::factory()->completed()->create([
        'reporter_id' => $this->reporter->id,
    ]);

    $dto = new FeedbackData(
        reportId: $report->id,
        userId: $this->reporter->id,
        rating: 5,
        comment: 'Pengerjaan cepat dan sangat rapi.',
    );

    $feedback = $this->service->createFeedback($dto);

    expect($feedback)->toBeInstanceOf(Feedback::class)
        ->and($feedback->rating)->toBe(5)
        ->and($feedback->comment)->toBe('Pengerjaan cepat dan sangat rapi.')
        ->and($feedback->report_id)->toBe($report->id)
        ->and($feedback->user_id)->toBe($this->reporter->id);

    $this->assertDatabaseHas('feedbacks', [
        'id' => $feedback->id,
        'report_id' => $report->id,
        'rating' => 5,
    ]);
});

test('FeedbackService createFeedback menolak laporan yang belum berstatus Completed', function () {
    $report = Report::factory()->create([
        'reporter_id' => $this->reporter->id,
        'status' => ReportStatus::Pending,
    ]);

    $dto = new FeedbackData(
        reportId: $report->id,
        userId: $this->reporter->id,
        rating: 4,
        comment: 'Cukup baik.',
    );

    $this->service->createFeedback($dto);
})->throws(InvalidArgumentException::class, 'Umpan balik hanya dapat diberikan untuk laporan dengan status Selesai (Completed).');

test('FeedbackService updateFeedback berhasil jika belum ditanggapi pengelola', function () {
    $report = Report::factory()->completed()->create(['reporter_id' => $this->reporter->id]);
    $feedback = Feedback::factory()->create([
        'report_id' => $report->id,
        'user_id' => $this->reporter->id,
        'rating' => 3,
        'comment' => 'Komentar awal',
        'admin_response' => null,
    ]);

    $updated = $this->service->updateFeedback($feedback, 4, 'Komentar revisi yang lebih detail');

    expect($updated->rating)->toBe(4)
        ->and($updated->comment)->toBe('Komentar revisi yang lebih detail');

    $this->assertDatabaseHas('feedbacks', [
        'id' => $feedback->id,
        'rating' => 4,
        'comment' => 'Komentar revisi yang lebih detail',
    ]);
});

test('FeedbackService updateFeedback menolak jika isLocked (sudah ada tanggapan resmi)', function () {
    $report = Report::factory()->completed()->create(['reporter_id' => $this->reporter->id]);
    $feedback = Feedback::factory()->create([
        'report_id' => $report->id,
        'user_id' => $this->reporter->id,
        'rating' => 3,
        'comment' => 'Komentar awal',
        'admin_response' => 'Terima kasih atas masukannya.',
        'responded_by' => $this->admin->id,
        'responded_at' => now(),
    ]);

    $this->service->updateFeedback($feedback, 5, 'Ingin ubah rating');
})->throws(RuntimeException::class, 'Umpan balik telah ditanggapi oleh pihak pengelola dan tidak dapat diubah lagi.');

test('FeedbackService deleteFeedback menolak jika isLocked (sudah ada tanggapan resmi)', function () {
    $report = Report::factory()->completed()->create(['reporter_id' => $this->reporter->id]);
    $feedback = Feedback::factory()->create([
        'report_id' => $report->id,
        'user_id' => $this->reporter->id,
        'rating' => 2,
        'comment' => 'Keluhan',
        'admin_response' => 'Sudah ditindaklanjuti.',
        'responded_by' => $this->admin->id,
        'responded_at' => now(),
    ]);

    $this->service->deleteFeedback($feedback);
})->throws(RuntimeException::class, 'Umpan balik telah ditanggapi oleh pihak pengelola dan tidak dapat dihapus.');

test('FeedbackService respondFeedback berhasil menyimpan tanggapan resmi dan waktu respon', function () {
    $report = Report::factory()->completed()->create(['reporter_id' => $this->reporter->id]);
    $feedback = Feedback::factory()->create([
        'report_id' => $report->id,
        'user_id' => $this->reporter->id,
        'admin_response' => null,
    ]);

    $responded = $this->service->respondFeedback(
        $feedback,
        'Terima kasih atas apresiasinya, fasilitas telah kembali berfungsi normal.',
        $this->admin
    );

    expect($responded->admin_response)->toBe('Terima kasih atas apresiasinya, fasilitas telah kembali berfungsi normal.')
        ->and($responded->responded_by)->toBe($this->admin->id)
        ->and($responded->responded_at)->not->toBeNull()
        ->and($responded->isLocked())->toBeTrue();
});

test('FeedbackService filterFeedbackQuery memfilter berdasarkan rating, search, dan kategori fasilitas', function () {
    $catA = FacilityCategory::create(['code' => 'CAT-A', 'name' => 'Kategori Listrik', 'is_active' => true]);
    $catB = FacilityCategory::create(['code' => 'CAT-B', 'name' => 'Kategori Sanitasi', 'is_active' => true]);

    $facilityA = Facility::factory()->create(['facility_category_id' => $catA->id]);
    $facilityB = Facility::factory()->create(['facility_category_id' => $catB->id]);

    $reportA = Report::factory()->completed()->create([
        'facility_id' => $facilityA->id,
        'reporter_id' => $this->reporter->id,
    ]);
    $reportB = Report::factory()->completed()->create([
        'facility_id' => $facilityB->id,
        'reporter_id' => $this->reporter->id,
    ]);

    $fb1 = Feedback::factory()->create([
        'report_id' => $reportA->id,
        'user_id' => $this->reporter->id,
        'rating' => 5,
        'comment' => 'Lampu LED menyala terang.',
    ]);

    $fb2 = Feedback::factory()->create([
        'report_id' => $reportB->id,
        'user_id' => $this->reporter->id,
        'rating' => 2,
        'comment' => 'Kran air masih menetes sedikit.',
    ]);

    // Filter rating 5
    $filteredRating = $this->service->filterFeedbackQuery(['rating' => 5])->get();
    expect($filteredRating)->toHaveCount(1)
        ->and($filteredRating->first()?->id)->toBe($fb1->id);

    // Filter kategori fasilitas Sanitasi
    $filteredCat = $this->service->filterFeedbackQuery(['facility_category_id' => $catB->id])->get();
    expect($filteredCat)->toHaveCount(1)
        ->and($filteredCat->first()?->id)->toBe($fb2->id);

    // Filter kata kunci search
    $filteredSearch = $this->service->filterFeedbackQuery(['search' => 'Kran air'])->get();
    expect($filteredSearch)->toHaveCount(1)
        ->and($filteredSearch->first()?->id)->toBe($fb2->id);
});
