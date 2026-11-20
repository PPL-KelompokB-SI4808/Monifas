<?php

namespace App\Http\Controllers\Supporting\Campaigns;

use App\Http\Controllers\Controller;
use App\Http\Requests\Supporting\Campaigns\StoreCampaignRequest;
use App\Http\Requests\Supporting\Campaigns\UpdateCampaignRequest;
use App\Models\Building;
use App\Models\FacilityCategory;
use App\Models\MaintenanceCampaign;
use App\Services\Supporting\Campaigns\CampaignService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;

class CampaignController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            'auth',
            new Middleware('role:admin'),
        ];
    }

    public function __construct(
        protected CampaignService $campaignService,
    ) {}

    /**
     * Display a listing of campaigns with optional filters.
     */
    public function index(Request $request): View
    {
        $filters = [
            'status' => $request->input('status'),
            'building_id' => $request->filled('building_id') ? (int) $request->input('building_id') : null,
            'facility_category_id' => $request->filled('facility_category_id') ? (int) $request->input('facility_category_id') : null,
            'search' => $request->input('search'),
        ];

        $campaigns = $this->campaignService->paginateCampaigns($filters, 12);
        $buildings = Building::query()->where('is_active', true)->orderBy('name')->get(['id', 'name']);
        $categories = FacilityCategory::query()->where('is_active', true)->orderBy('name')->get(['id', 'name']);

        return view('pages.dashboard.supporting.campaigns.index', compact('campaigns', 'filters', 'buildings', 'categories'));
    }

    /**
     * Show the form for creating a new campaign.
     */
    public function create(): View
    {
        $buildings = Building::query()->where('is_active', true)->orderBy('name')->get(['id', 'name']);
        $categories = FacilityCategory::query()->where('is_active', true)->orderBy('name')->get(['id', 'name']);

        return view('pages.dashboard.supporting.campaigns.create', compact('buildings', 'categories'));
    }

    /**
     * Store a newly created campaign in storage.
     */
    public function store(StoreCampaignRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['created_by'] = $request->user()->id;

        $this->campaignService->createCampaign($data);

        return redirect()
            ->route('supporting.campaigns.index')
            ->with('success', 'Kampanye edukasi berhasil dibuat.');
    }

    /**
     * Display the specified campaign.
     */
    public function show(MaintenanceCampaign $campaign): View
    {
        $campaign->load(['creator', 'building', 'facilityCategory']);

        return view('pages.dashboard.supporting.campaigns.show', compact('campaign'));
    }

    /**
     * Show the form for editing the specified campaign.
     */
    public function edit(MaintenanceCampaign $campaign): View
    {
        $buildings = Building::query()->where('is_active', true)->orderBy('name')->get(['id', 'name']);
        $categories = FacilityCategory::query()->where('is_active', true)->orderBy('name')->get(['id', 'name']);

        return view('pages.dashboard.supporting.campaigns.edit', compact('campaign', 'buildings', 'categories'));
    }

    /**
     * Update the specified campaign in storage.
     */
    public function update(UpdateCampaignRequest $request, MaintenanceCampaign $campaign): RedirectResponse
    {
        $this->campaignService->updateCampaign($campaign, $request->validated());

        return redirect()
            ->route('supporting.campaigns.index')
            ->with('success', 'Kampanye edukasi berhasil diperbarui.');
    }

    /**
     * Remove the specified campaign from storage (soft delete).
     */
    public function destroy(MaintenanceCampaign $campaign): RedirectResponse
    {
        $this->campaignService->deleteCampaign($campaign);

        return redirect()
            ->route('supporting.campaigns.index')
            ->with('success', 'Kampanye edukasi berhasil dihapus.');
    }

    /**
     * Cancel the specified campaign.
     */
    public function cancel(MaintenanceCampaign $campaign): RedirectResponse
    {
        $this->campaignService->cancelCampaign($campaign);

        return redirect()
            ->route('supporting.campaigns.index')
            ->with('success', 'Kampanye edukasi berhasil dibatalkan.');
    }
}
