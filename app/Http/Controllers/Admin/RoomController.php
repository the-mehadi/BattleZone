<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\RoomRequest;
use App\Models\Category;
use App\Models\Room;
use App\Models\Squad;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class RoomController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->string('status')->toString();

        $rooms = Room::query()
            ->with('category')
            ->when(
                in_array($status, ['upcoming', 'live', 'finished', 'cancelled'], true),
                fn ($query) => $query->where('status', $status)
            )
            ->latest('match_time')
            ->paginate(10)
            ->withQueryString();

        return view('admin.rooms.index', [
            'rooms' => $rooms,
            'status' => $status,
        ]);
    }

    public function create(): View
    {
        $categories = Category::query()
            ->where('status', 'active')
            ->with(['categoryPrizes' => fn ($query) => $query->orderBy('position')])
            ->orderBy('name')
            ->get();

        $selectedCategory = $categories->first();

        return view('admin.rooms.create', [
            'room' => new Room([
                'status' => 'upcoming',
                'is_room_locked' => true,
                'map' => $selectedCategory?->map,
                'entry_fee' => $selectedCategory?->entry_fee,
                'total_prize' => $selectedCategory?->categoryPrizes->sum('prize_amount') ?? 0,
            ]),
            'categories' => $categories,
            'prizes' => $selectedCategory?->categoryPrizes
                ? $this->formatPrizes($selectedCategory->categoryPrizes->toArray())
                : [],
        ]);
    }

    public function store(RoomRequest $request): RedirectResponse
    {
        DB::transaction(function () use ($request): void {
            $category = Category::query()
                ->with(['categoryPrizes' => fn ($query) => $query->orderBy('position')])
                ->findOrFail($request->integer('category_id'));

            $room = Room::create([
                'category_id' => $category->id,
                'title' => $request->string('title')->toString(),
                'map' => $request->string('map')->toString(),
                'room_code' => $request->filled('room_code') ? $request->string('room_code')->toString() : null,
                'room_password' => $request->filled('room_password') ? $request->string('room_password')->toString() : null,
                'match_time' => $request->date('match_time'),
                'entry_fee' => $request->input('entry_fee'),
                'total_prize' => $request->input('total_prize'),
                'is_room_locked' => true,
                'status' => 'upcoming',
                'created_by' => $request->user()->id,
            ]);

            $this->syncRoomPrizes(
                $room,
                $request->validated('prizes') ?: $this->formatPrizes($category->categoryPrizes->toArray())
            );
        });

        return redirect()
            ->route('admin.rooms.index')
            ->with('success', 'Room created successfully.');
    }

    public function show(Room $room): View
    {
        $room->load([
            'category',
            'roomPrizes' => fn ($query) => $query->orderBy('position'),
            'squads' => fn ($query) => $query
                ->with(['leader', 'squadPlayers', 'result'])
                ->latest(),
            'results' => fn ($query) => $query
                ->with('squad')
                ->orderBy('position'),
        ]);

        return view('admin.rooms.show', compact('room'));
    }

    public function edit(Room $room): View
    {
        $categories = Category::query()
            ->where(function ($query) use ($room): void {
                $query->where('status', 'active')
                    ->orWhere('id', $room->category_id);
            })
            ->with(['categoryPrizes' => fn ($query) => $query->orderBy('position')])
            ->orderBy('name')
            ->get();

        $room->load(['roomPrizes' => fn ($query) => $query->orderBy('position')]);

        return view('admin.rooms.edit', [
            'room' => $room,
            'categories' => $categories,
            'prizes' => $this->formatPrizes($room->roomPrizes->toArray()),
        ]);
    }

    public function update(RoomRequest $request, Room $room): RedirectResponse
    {
        DB::transaction(function () use ($request, $room): void {
            $room->update([
                'category_id' => $request->integer('category_id'),
                'title' => $request->string('title')->toString(),
                'map' => $request->string('map')->toString(),
                'room_code' => $request->filled('room_code') ? $request->string('room_code')->toString() : null,
                'room_password' => $request->filled('room_password') ? $request->string('room_password')->toString() : null,
                'match_time' => $request->date('match_time'),
                'entry_fee' => $request->input('entry_fee'),
                'total_prize' => $request->input('total_prize'),
            ]);

            $this->syncRoomPrizes($room, $request->validated('prizes') ?? []);
        });

        return redirect()
            ->route('admin.rooms.show', $room)
            ->with('success', 'Room updated successfully.');
    }

    public function toggleLock(Room $room): RedirectResponse
    {
        $room->update([
            'is_room_locked' => ! $room->is_room_locked,
        ]);

        return redirect()
            ->route('admin.rooms.show', $room)
            ->with('success', $room->is_room_locked ? 'Room details locked successfully.' : 'Room details unlocked successfully.');
    }

    public function updateStatus(Request $request, Room $room): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['upcoming', 'live', 'finished', 'cancelled'])],
        ]);

        $nextStatus = $validated['status'];
        $allowedTransitions = [
            'upcoming' => ['live', 'cancelled'],
            'live' => ['finished', 'cancelled'],
            'finished' => [],
            'cancelled' => [],
        ];

        if (! in_array($nextStatus, $allowedTransitions[$room->status] ?? [], true)) {
            return redirect()
                ->route('admin.rooms.show', $room)
                ->with('error', 'That status transition is not allowed.');
        }

        $room->update(['status' => $nextStatus]);

        return redirect()
            ->route('admin.rooms.show', $room)
            ->with('success', 'Room status updated successfully.');
    }

    public function updateSquadStatus(Request $request, Room $room, Squad $squad): RedirectResponse
    {
        abort_unless($squad->room_id === $room->id, 404);

        $validated = $request->validate([
            'status' => ['required', Rule::in(['approved', 'rejected'])],
        ]);

        $squad->update([
            'status' => $validated['status'],
        ]);

        return redirect()
            ->route('admin.rooms.show', $room)
            ->with('success', 'Squad status updated successfully.');
    }

    public function destroy(Room $room): RedirectResponse
    {
        if ($room->status !== 'upcoming') {
            return redirect()
                ->route('admin.rooms.index')
                ->with('error', 'Only upcoming rooms can be deleted.');
        }

        $room->delete();

        return redirect()
            ->route('admin.rooms.index')
            ->with('success', 'Room deleted successfully.');
    }

    protected function syncRoomPrizes(Room $room, array $prizes): void
    {
        $room->roomPrizes()->delete();

        if ($prizes === []) {
            return;
        }

        $room->roomPrizes()->createMany(
            collect($prizes)
                ->sortBy('position')
                ->map(fn (array $prize) => [
                    'position' => (int) $prize['position'],
                    'prize_amount' => $prize['prize_amount'],
                ])
                ->values()
                ->all()
        );
    }

    protected function formatPrizes(array $prizes): array
    {
        return collect($prizes)
            ->map(fn (array $prize) => [
                'position' => (int) $prize['position'],
                'prize_amount' => (float) $prize['prize_amount'],
            ])
            ->values()
            ->all();
    }
}
