<?php

namespace App\Http\Controllers\Player;

use App\Http\Controllers\Controller;
use App\Http\Requests\SquadStoreRequest;
use App\Models\Room;
use App\Models\Squad;
use App\Services\WalletService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use RuntimeException;

class SquadController extends Controller
{
    public function __construct(protected WalletService $walletService)
    {
    }

    public function index(): View
    {
        $squads = auth()->user()
            ->squads()
            ->with([
                'room.category',
                'squadPlayers',
            ])
            ->latest()
            ->get();

        return view('player.squads.index', compact('squads'));
    }

    public function showJoinForm(Room $room): View|RedirectResponse
    {
        $room->load('category');

        if ($response = $this->ensurePlayerCanJoin($room, false)) {
            return $response;
        }

        return view('player.squads.join', [
            'room' => $room,
            'playerSlots' => $this->resolvePlayerSlots($room),
        ]);
    }

    public function store(SquadStoreRequest $request, Room $room): RedirectResponse
    {
        $room->load('category');

        if ($response = $this->ensurePlayerCanJoin($room)) {
            return $response;
        }

        try {
            DB::transaction(function () use ($request, $room): void {
                /** @var \App\Models\User $user */
                $user = $request->user();

                $squad = Squad::create([
                    'room_id' => $room->id,
                    'leader_user_id' => $user->id,
                    'squad_name' => null,
                    'total_paid' => $room->entry_fee,
                    'status' => 'pending',
                ]);

                $squad->squadPlayers()->createMany(
                    collect($request->validated('players'))
                        ->map(fn (array $player) => [
                            'ingame_name' => $player['ingame_name'],
                            'ingame_id' => $player['ingame_id'],
                        ])
                        ->all()
                );

                $deducted = $this->walletService->deduct(
                    $user,
                    (float) $room->entry_fee,
                    sprintf('Room entry fee for %s', $room->title),
                    $room->id
                );

                if (! $deducted) {
                    throw new RuntimeException('Insufficient wallet balance.');
                }
            });
        } catch (RuntimeException) {
            return redirect()
                ->route('rooms.show', $room)
                ->with('error', 'Your wallet balance is no longer enough to join this room.');
        }

        return redirect()
            ->route('rooms.show', $room)
            ->with('success', 'Squad joined successfully. Entry fee has been deducted from your wallet.');
    }

    protected function ensurePlayerCanJoin(Room $room, bool $checkWallet = true): ?RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        if ($room->status !== 'upcoming') {
            return redirect()
                ->route('rooms.show', $room)
                ->with('error', 'You can only join upcoming rooms.');
        }

        if ($room->squads()->where('leader_user_id', $user->id)->exists()) {
            return redirect()
                ->route('rooms.show', $room)
                ->with('error', 'You have already joined this room.');
        }

        if ($checkWallet && (float) $user->wallet_balance < (float) $room->entry_fee) {
            return redirect()
                ->route('rooms.show', $room)
                ->with('error', 'You do not have enough wallet balance to join this room.');
        }

        return null;
    }

    protected function resolvePlayerSlots(Room $room): int
    {
        return match ($room->category->squad_type) {
            'solo' => 1,
            'duo' => 2,
            default => 4,
        };
    }
}
