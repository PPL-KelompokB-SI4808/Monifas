<?php

namespace App\Http\Controllers\Supporting\Feedback;

use App\DTOs\Supporting\FeedbackData;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Supporting\Feedback\RespondFeedbackRequest;
use App\Http\Requests\Supporting\Feedback\StoreFeedbackRequest;
use App\Http\Requests\Supporting\Feedback\UpdateFeedbackRequest;
use App\Models\FacilityCategory;
use App\Models\Feedback;
use App\Models\MaintenanceOfficer;
use App\Models\Report;
use App\Services\Supporting\Feedback\FeedbackService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FeedbackController extends Controller
{
    public function __construct(
        protected FeedbackService $feedbackService
    ) {}

    /**
     * Display a listing of feedback.
     * Admin/Supervisor dapat melihat semua feedback dan filter.
     * Pelapor umum melihat umpan balik miliknya atau daftar umpan balik.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $isStaff = $user?->hasRole(UserRole::Admin) || $user?->hasRole(UserRole::Supervisor);

        $filters = [
            'rating' => $request->input('rating'),
            'officer_id' => $request->input('officer_id'),
            'facility_category_id' => $request->input('facility_category_id'),
            'search' => $request->input('search'),
        ];

        $feedbacks = $this->feedbackService->getPaginatedFeedback($filters);
        $facilityCategories = FacilityCategory::where('is_active', true)->orderBy('name')->get();
        $technicians = MaintenanceOfficer::with('user')->where('is_active', true)->get();

        return view('pages.dashboard.supporting.feedback.index', compact(
            'feedbacks',
            'facilityCategories',
            'technicians',
            'filters',
            'isStaff'
        ));
    }

    /**
     * Show the form for creating a new feedback.
     */
    public function create(Request $request): View|RedirectResponse
    {
        $reportId = $request->input('report_id');
        $report = $reportId ? Report::with(['facility', 'damageCategory', 'repairs'])->find($reportId) : null;

        if ($report && $report->feedback()->exists()) {
            return redirect()->route('supporting.feedback.show', $report->feedback)
                ->with('warning', 'Laporan ini sudah memiliki ulasan feedback.');
        }

        return view('pages.dashboard.supporting.feedback.create', compact('report'));
    }

    /**
     * Store a newly created feedback in storage.
     */
    public function store(StoreFeedbackRequest $request): RedirectResponse
    {
        try {
            $data = FeedbackData::fromRequest($request);
            $feedback = $this->feedbackService->createFeedback($data);

            return redirect()->route('supporting.feedback.show', $feedback)
                ->with('success', 'Ulasan dan penilaian Anda berhasil dikirim.');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Display the specified feedback.
     */
    public function show(Feedback $feedback): View
    {
        $feedback->load([
            'user',
            'responder',
            'report.facility.category',
            'report.damageCategory',
            'repair.officer.user',
        ]);

        return view('pages.dashboard.supporting.feedback.show', compact('feedback'));
    }

    /**
     * Show the form for editing the specified feedback.
     */
    public function edit(Feedback $feedback, Request $request): View|RedirectResponse
    {
        $user = $request->user();

        // Cek kepemilikan dan apakah terkunci
        if ((int) $feedback->user_id !== (int) $user?->id) {
            abort(403, 'Anda tidak berhak mengubah ulasan ini.');
        }

        if ($feedback->isLocked()) {
            return redirect()->route('supporting.feedback.show', $feedback)
                ->with('error', 'Ulasan telah ditanggapi oleh pihak pengelola dan tidak dapat diubah.');
        }

        $feedback->load(['report.facility', 'report.damageCategory']);

        return view('pages.dashboard.supporting.feedback.edit', compact('feedback'));
    }

    /**
     * Update the specified feedback in storage.
     */
    public function update(UpdateFeedbackRequest $request, Feedback $feedback): RedirectResponse
    {
        try {
            $this->feedbackService->updateFeedback(
                $feedback,
                (int) $request->input('rating'),
                (string) $request->input('comment')
            );

            return redirect()->route('supporting.feedback.show', $feedback)
                ->with('success', 'Ulasan berhasil diperbarui.');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Remove the specified feedback from storage.
     */
    public function destroy(Feedback $feedback, Request $request): RedirectResponse
    {
        $user = $request->user();

        // Hanya pembuat ulasan atau admin yang dapat menghapus
        $isOwner = (int) $feedback->user_id === (int) $user?->id;
        $isAdmin = $user?->hasRole(UserRole::Admin);

        if (! $isOwner && ! $isAdmin) {
            abort(403, 'Anda tidak berhak menghapus ulasan ini.');
        }

        try {
            $this->feedbackService->deleteFeedback($feedback);

            return redirect()->route('supporting.feedback.index')
                ->with('success', 'Ulasan berhasil dihapus.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Respond to the feedback (Admin/Supervisor).
     */
    public function respond(RespondFeedbackRequest $request, Feedback $feedback): RedirectResponse
    {
        $user = $request->user();

        if (! $user) {
            abort(401);
        }

        try {
            $this->feedbackService->respondFeedback(
                $feedback,
                (string) $request->input('admin_response'),
                $user
            );

            return redirect()->route('supporting.feedback.show', $feedback)
                ->with('success', 'Tanggapan resmi berhasil disimpan.');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }
}
