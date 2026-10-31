<?php

namespace App\Http\Controllers\Facility;

use App\Http\Controllers\Controller;
use App\Http\Requests\Facility\Rooms\StoreRoomRequest;
use App\Http\Requests\Facility\Rooms\UpdateRoomRequest;
use App\Models\Building;
use App\Models\Room;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class RoomController extends Controller
{
    public function index(): View
    {
        $rooms = Room::with('building')->latest()->paginate(10);
        return view('facility.rooms.index', compact('rooms'));
    }

    public function create(): View
    {
        $buildings = Building::all();
        return view('facility.rooms.create', compact('buildings'));
    }

    public function store(StoreRoomRequest $request): RedirectResponse
    {
        Room::create($request->validated());

        return redirect()->route('facility.rooms.index')
            ->with('success', 'Ruangan berhasil ditambahkan.');
    }

    public function show(Room $room): View
    {
        return view('facility.rooms.show', compact('room'));
    }

    public function edit(Room $room): View
    {
        $buildings = Building::all();
        return view('facility.rooms.edit', compact('room', 'buildings'));
    }

    public function update(UpdateRoomRequest $request, Room $room): RedirectResponse
    {
        $room->update($request->validated());

        return redirect()->route('facility.rooms.index')
            ->with('success', 'Ruangan berhasil diperbarui.');
    }

    public function destroy(Room $room): RedirectResponse
    {
        $room->delete();

        return redirect()->route('facility.rooms.index')
            ->with('success', 'Ruangan berhasil dihapus.');
    }
}