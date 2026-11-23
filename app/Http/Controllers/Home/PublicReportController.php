<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use App\Http\Requests\Home\PublicReportRequest;
use App\Models\Building;
use App\Models\Facility;
use App\Models\Room;
use App\Services\Reporting\Reports\ReportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PublicReportController extends Controller
{
    public function __construct(
        protected ReportService $reportService
    ) {}

    /**
     * Simpan laporan publik (tanpa autentikasi) ke database dan kirim notifikasi.
     */
    public function store(PublicReportRequest $request): RedirectResponse
    {
        $report = $this->reportService->storePublic($request->validated());

        return redirect()
            ->to(route('home').'#laporan')
            ->with('success', "Laporan Anda berhasil dikirim! Nomor tiket: #{$report->ticket_number}. Tim kami akan segera menindaklanjuti.");
    }

    /**
     * Kembalikan daftar gedung berdasarkan lokasi (AJAX untuk dropdown bertingkat).
     */
    public function buildingsByLocation(Request $request): JsonResponse
    {
        $locationId = (int) $request->query('location_id', 0);

        $buildings = Building::query()
            ->where('location_id', $locationId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json($buildings);
    }

    /**
     * Kembalikan daftar ruangan berdasarkan gedung (AJAX untuk dropdown bertingkat).
     */
    public function roomsByBuilding(Request $request): JsonResponse
    {
        $buildingId = (int) $request->query('building_id', 0);

        $rooms = Room::query()
            ->where('building_id', $buildingId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json($rooms);
    }

    /**
     * Kembalikan daftar fasilitas berdasarkan gedung dan/atau ruangan (AJAX).
     */
    public function facilitiesByRoom(Request $request): JsonResponse
    {
        $buildingId = (int) $request->query('building_id', 0);
        $roomId = $request->query('room_id');

        $facilities = Facility::query()
            ->where('building_id', $buildingId)
            ->when($roomId !== null && $roomId !== '', fn ($q) => $q->where('room_id', (int) $roomId))
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json($facilities);
    }
}
