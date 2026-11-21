<?php

use App\Enums\CampaignStatus;
use App\Models\Building;
use App\Models\FacilityCategory;
use App\Models\MaintenanceCampaign;
use App\Models\User;
use App\Services\Supporting\Campaigns\CampaignService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    $this->service = new CampaignService;

    $this->user = User::factory()->create();

    $this->buildingA = Building::factory()->create(['name' => 'Gedung A']);
    $this->buildingB = Building::factory()->create(['name' => 'Gedung B']);

    $this->categoryAC = FacilityCategory::factory()->create(['name' => 'AC']);
    $this->categoryListrik = FacilityCategory::factory()->create(['name' => 'Kelistrikan']);
});

test('CampaignService filterCampaignsQuery filters by status, building_id, facility_category_id, and search', function () {
    $c1 = MaintenanceCampaign::factory()->create([
        'title' => 'Edukasi Penggunaan AC Hemat Energi',
        'content' => 'Atur suhu pada 24 derajat.',
        'building_id' => $this->buildingA->id,
        'facility_category_id' => $this->categoryAC->id,
        'status' => CampaignStatus::Active,
        'created_by' => $this->user->id,
    ]);

    $c2 = MaintenanceCampaign::factory()->create([
        'title' => 'Sosialisasi Bahaya Beban Berlebih Kelistrikan',
        'content' => 'Hindari colokan bertumpuk.',
        'building_id' => $this->buildingB->id,
        'facility_category_id' => $this->categoryListrik->id,
        'status' => CampaignStatus::Scheduled,
        'created_by' => $this->user->id,
    ]);

    // Filter by status
    $statusFiltered = $this->service->filterCampaignsQuery(['status' => CampaignStatus::Active])->get();
    expect($statusFiltered)->toHaveCount(1);
    expect($statusFiltered->first()->id)->toBe($c1->id);

    // Filter by building_id
    $buildingFiltered = $this->service->filterCampaignsQuery(['building_id' => $this->buildingB->id])->get();
    expect($buildingFiltered)->toHaveCount(1);
    expect($buildingFiltered->first()->id)->toBe($c2->id);

    // Filter by facility_category_id
    $categoryFiltered = $this->service->filterCampaignsQuery(['facility_category_id' => $this->categoryAC->id])->get();
    expect($categoryFiltered)->toHaveCount(1);
    expect($categoryFiltered->first()->id)->toBe($c1->id);

    // Filter by search keyword
    $searchFiltered = $this->service->filterCampaignsQuery(['search' => 'Kelistrikan'])->get();
    expect($searchFiltered)->toHaveCount(1);
    expect($searchFiltered->first()->id)->toBe($c2->id);

    // Paginate
    $paginated = $this->service->paginateCampaigns([], 10);
    expect($paginated->total())->toBe(2);
});

test('CampaignService createCampaign and updateCampaign automatically resolve status based on dates', function () {
    // 1. Future dates resolve to scheduled
    $futureCampaign = $this->service->createCampaign([
        'created_by' => $this->user->id,
        'building_id' => $this->buildingA->id,
        'facility_category_id' => $this->categoryAC->id,
        'title' => 'Kampanye Masa Depan',
        'objective' => 'Tujuan edukasi...',
        'content' => 'Materi...',
        'start_date' => now()->addDays(3)->toDateString(),
        'end_date' => now()->addDays(10)->toDateString(),
    ]);
    expect($futureCampaign->status)->toBe(CampaignStatus::Scheduled);

    // 2. Current dates resolve to active
    $currentCampaign = $this->service->createCampaign([
        'created_by' => $this->user->id,
        'building_id' => $this->buildingA->id,
        'facility_category_id' => $this->categoryAC->id,
        'title' => 'Kampanye Hari Ini',
        'objective' => 'Tujuan...',
        'content' => 'Materi...',
        'start_date' => now()->subDay()->toDateString(),
        'end_date' => now()->addDays(5)->toDateString(),
    ]);
    expect($currentCampaign->status)->toBe(CampaignStatus::Active);

    // 3. Past dates resolve to ended
    $pastCampaign = $this->service->createCampaign([
        'created_by' => $this->user->id,
        'building_id' => $this->buildingA->id,
        'facility_category_id' => $this->categoryAC->id,
        'title' => 'Kampanye Telah Lalu',
        'objective' => 'Tujuan...',
        'content' => 'Materi...',
        'start_date' => now()->subDays(10)->toDateString(),
        'end_date' => now()->subDays(2)->toDateString(),
    ]);
    expect($pastCampaign->status)->toBe(CampaignStatus::Ended);

    // 4. Update campaign
    $updated = $this->service->updateCampaign($currentCampaign, [
        'title' => 'Kampanye Hari Ini Diperbarui',
    ]);
    expect($updated->title)->toBe('Kampanye Hari Ini Diperbarui');
});

test('CampaignService activateDueScheduledCampaigns activates scheduled campaigns when start date is reached', function () {
    $dueCampaign = MaintenanceCampaign::factory()->create([
        'status' => CampaignStatus::Scheduled,
        'start_date' => now()->toDateString(),
        'end_date' => now()->addDays(7)->toDateString(),
    ]);

    $futureCampaign = MaintenanceCampaign::factory()->create([
        'status' => CampaignStatus::Scheduled,
        'start_date' => now()->addDays(3)->toDateString(),
        'end_date' => now()->addDays(10)->toDateString(),
    ]);

    $affected = $this->service->activateDueScheduledCampaigns();

    expect($affected)->toBe(1);
    expect($dueCampaign->fresh()->status)->toBe(CampaignStatus::Active);
    expect($futureCampaign->fresh()->status)->toBe(CampaignStatus::Scheduled);
});

test('CampaignService endExpiredCampaigns ends active campaigns past end date', function () {
    $expiredCampaign = MaintenanceCampaign::factory()->create([
        'status' => CampaignStatus::Active,
        'start_date' => now()->subDays(10)->toDateString(),
        'end_date' => now()->subDay()->toDateString(),
    ]);

    $stillActiveCampaign = MaintenanceCampaign::factory()->create([
        'status' => CampaignStatus::Active,
        'start_date' => now()->subDay()->toDateString(),
        'end_date' => now()->addDays(3)->toDateString(),
    ]);

    $affected = $this->service->endExpiredCampaigns();

    expect($affected)->toBe(1);
    expect($expiredCampaign->fresh()->status)->toBe(CampaignStatus::Ended);
    expect($stillActiveCampaign->fresh()->status)->toBe(CampaignStatus::Active);
});

test('CampaignService cancelCampaign and deleteCampaign manage campaign lifecycle', function () {
    $campaign = MaintenanceCampaign::factory()->create(['status' => CampaignStatus::Active]);

    $this->service->cancelCampaign($campaign);
    expect($campaign->fresh()->status)->toBe(CampaignStatus::Cancelled);

    $deleted = $this->service->deleteCampaign($campaign);
    expect($deleted)->toBeTrue();
    expect($campaign->fresh()->trashed())->toBeTrue();
});

test('CampaignService getActiveCampaignsForUser retrieves active campaigns within valid date range', function () {
    $activeCampaign = MaintenanceCampaign::factory()->create([
        'status' => CampaignStatus::Active,
        'start_date' => now()->subDay()->toDateString(),
        'end_date' => now()->addDays(5)->toDateString(),
    ]);

    $endedCampaign = MaintenanceCampaign::factory()->create([
        'status' => CampaignStatus::Ended,
        'start_date' => now()->subDays(10)->toDateString(),
        'end_date' => now()->subDay()->toDateString(),
    ]);

    $activeList = $this->service->getActiveCampaignsForUser($this->user);

    expect($activeList->pluck('id'))->toContain($activeCampaign->id);
    expect($activeList->pluck('id'))->not->toContain($endedCampaign->id);
});
