<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RoomController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->string('status')->toString();

        $rooms = Room::query()
            ->with('category')
            ->withCount('squads')
            ->when(
                in_array($status, ['upcoming', 'live', 'finished'], true),
                fn ($query) => $query->where('status', $status)
            )
            ->whereIn('status', ['upcoming', 'live', 'finished'])
            ->orderByRaw("case when status = 'live' then 0 when status = 'upcoming' then 1 else 2 end")
            ->orderBy('match_time')
            ->paginate(9)
            ->withQueryString();

        return view('rooms.index', [
            'rooms' => $rooms,
            'status' => $status,
        ]);
    }

    public function show(Room $room): View
    {
        $room->load([
            'category',
            'roomPrizes' => fn ($query) => $query->orderBy('position'),
            'squads' => fn ($query) => $query
                ->with(['leader', 'squadPlayers'])
                ->latest(),
        ])->loadCount('squads');

        return view('rooms.show', compact('room'));
    }
}
