<?php

namespace App\Http\Controllers\Supporting\Announcements;

use App\Http\Controllers\Controller;
use App\Http\Requests\Supporting\Announcements\StoreAnnouncementRequest;
use App\Http\Requests\Supporting\Announcements\UpdateAnnouncementRequest;
use App\Models\Announcement;
use App\Services\Supporting\Announcements\AnnouncementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;

class AnnouncementController extends Controller implements HasMiddleware
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
        protected AnnouncementService $announcementService,
    ) {}

    /**
     * Display a listing of announcements with optional filters.
     */
    public function index(Request $request): View
    {
        $filters = [
            'status' => $request->input('status'),
            'category' => $request->input('category'),
            'target_role' => $request->input('target_role'),
            'target_building_id' => $request->filled('target_building_id') ? (int) $request->input('target_building_id') : null,
            'search' => $request->input('search'),
        ];

        $announcements = $this->announcementService->paginateAnnouncements($filters, 15);

        return view('pages.dashboard.supporting.announcements.index', compact('announcements', 'filters'));
    }

    /**
     * Show the form for creating a new announcement.
     */
    public function create(): View
    {
        return view('pages.dashboard.supporting.announcements.create');
    }

    /**
     * Store a newly created announcement in storage.
     */
    public function store(StoreAnnouncementRequest $request): RedirectResponse
    {
        $this->announcementService->createAnnouncement($request->toDto());

        return redirect()
            ->route('supporting.announcements.index')
            ->with('success', 'Pengumuman berhasil dibuat.');
    }

    /**
     * Display the specified announcement.
     */
    public function show(Announcement $announcement): View
    {
        $announcement->load(['author', 'targetBuilding']);

        return view('pages.dashboard.supporting.announcements.show', compact('announcement'));
    }

    /**
     * Show the form for editing the specified announcement.
     */
    public function edit(Announcement $announcement): View
    {
        return view('pages.dashboard.supporting.announcements.edit', compact('announcement'));
    }

    /**
     * Update the specified announcement in storage.
     */
    public function update(UpdateAnnouncementRequest $request, Announcement $announcement): RedirectResponse
    {
        $this->announcementService->updateAnnouncement($announcement, $request->toDto());

        return redirect()
            ->route('supporting.announcements.index')
            ->with('success', 'Pengumuman berhasil diperbarui.');
    }

    /**
     * Remove the specified announcement from storage.
     */
    public function destroy(Announcement $announcement): RedirectResponse
    {
        $this->announcementService->deleteAnnouncement($announcement);

        return redirect()
            ->route('supporting.announcements.index')
            ->with('success', 'Pengumuman berhasil dihapus.');
    }

    /**
     * Archive the specified announcement.
     */
    public function archive(Announcement $announcement): RedirectResponse
    {
        $this->announcementService->archiveAnnouncement($announcement);

        return redirect()
            ->route('supporting.announcements.index')
            ->with('success', 'Pengumuman berhasil diarsipkan.');
    }
}

