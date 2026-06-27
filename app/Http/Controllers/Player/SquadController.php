<?php

namespace App\Http\Controllers\Player;

use App\Exceptions\InsufficientWalletBalanceException;
use App\Http\Controllers\Controller;
use App\Http\Requests\SquadStoreRequest;
use App\Models\Room;
use App\Models\Squad;
use App\Services\WalletService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Throwable;

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
                // #region debug-point A:join-transaction-start
                $this->reportDebug('A', 'SquadController@store:start', '[DEBUG] Join transaction started', [
                    'room_id' => $room->id,
                    'entry_fee' => (float) $room->entry_fee,
                    'user_id' => $user?->id,
                    'wallet_balance' => (float) $user?->wallet_balance,
                    'players_count' => count($request->validated('players')),
                ]);
                // #endregion

                $squad = Squad::create([
                    'room_id' => $room->id,
                    'leader_user_id' => $user->id,
                    'squad_name' => null,
                    'total_paid' => $room->entry_fee,
                    'status' => 'pending',
                ]);
                // #region debug-point B:squad-created
                $this->reportDebug('B', 'SquadController@store:squad-created', '[DEBUG] Squad row created', [
                    'room_id' => $room->id,
                    'squad_id' => $squad->id,
                    'user_id' => $user?->id,
                ]);
                // #endregion

                $squad->squadPlayers()->createMany(
                    collect($request->validated('players'))
                        ->map(fn (array $player) => [
                            'ingame_name' => $player['ingame_name'],
                            'ingame_id' => $player['ingame_id'],
                        ])
                        ->all()
                );
                // #region debug-point C:players-created
                $this->reportDebug('C', 'SquadController@store:players-created', '[DEBUG] Squad players inserted', [
                    'squad_id' => $squad->id,
                    'inserted_players' => $squad->squadPlayers()->count(),
                ]);
                // #endregion

                $deducted = $this->walletService->deduct(
                    $user,
                    (float) $room->entry_fee,
                    sprintf('Room entry fee for %s', $room->title),
                    $room->id
                );
                // #region debug-point D:wallet-deduct-result
                $this->reportDebug('D', 'SquadController@store:wallet-deduct-result', '[DEBUG] Wallet deduction completed', [
                    'room_id' => $room->id,
                    'user_id' => $user?->id,
                    'deducted' => $deducted,
                    'wallet_balance_after_local' => (float) $user?->wallet_balance,
                ]);
                // #endregion

                if (! $deducted) {
                    throw new InsufficientWalletBalanceException();
                }
            });
        } catch (InsufficientWalletBalanceException) {
            return redirect()
                ->route('rooms.show', $room)
                ->with('error', 'Your wallet balance is no longer enough to join this room.');
        } catch (Throwable $exception) {
            // #region debug-point E:join-exception
            $this->reportDebug('E', 'SquadController@store:catch', '[DEBUG] Join transaction threw exception', [
                'room_id' => $room->id,
                'exception_class' => $exception::class,
                'exception_message' => $exception->getMessage(),
                'exception_file' => $exception->getFile(),
                'exception_line' => $exception->getLine(),
            ]);
            // #endregion
            report($exception);

            return redirect()
                ->route('rooms.show', $room)
                ->with('error', 'We could not complete your join request right now. Please try again.');
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

    protected function reportDebug(string $hypothesisId, string $location, string $message, array $data = []): void
    {
        $url = 'http://127.0.0.1:7777/event';
        $sessionId = 'join-payment-failure';
        $envPath = base_path('.dbg/join-payment-failure.env');

        if (is_file($envPath)) {
            $envContents = file_get_contents($envPath) ?: '';
            preg_match('/^DEBUG_SERVER_URL=(.+)$/m', $envContents, $urlMatches);
            preg_match('/^DEBUG_SESSION_ID=(.+)$/m', $envContents, $sessionMatches);
            $url = $urlMatches[1] ?? $url;
            $sessionId = $sessionMatches[1] ?? $sessionId;
        }

        $payload = json_encode([
            'sessionId' => $sessionId,
            'runId' => 'pre-fix',
            'hypothesisId' => $hypothesisId,
            'location' => $location,
            'msg' => $message,
            'data' => $data,
            'ts' => (int) round(microtime(true) * 1000),
        ]);

        if ($payload === false) {
            return;
        }

        @file_get_contents($url, false, stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => "Content-Type: application/json\r\n",
                'content' => $payload,
                'ignore_errors' => true,
                'timeout' => 1,
            ],
        ]));
    }
}
