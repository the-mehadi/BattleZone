<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ResultStoreRequest;
use App\Models\Result;
use App\Models\Room;
use App\Services\WalletService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use RuntimeException;

class ResultController extends Controller
{
    public function __construct(protected WalletService $walletService)
    {
    }

    public function index(Room $room): View|RedirectResponse
    {
        $room->load([
            'category',
            'roomPrizes' => fn ($query) => $query->orderBy('position'),
            'squads' => fn ($query) => $query
                ->where('status', 'approved')
                ->with(['leader', 'squadPlayers'])
                ->orderBy('id'),
            'results' => fn ($query) => $query
                ->with('squad.leader')
                ->orderBy('position'),
        ]);

        if ($room->status !== 'finished') {
            return redirect()
                ->route('admin.rooms.show', $room)
                ->with('error', 'Results can only be entered for finished rooms.');
        }

        return view('admin.results.index', [
            'room' => $room,
            'approvedSquads' => $room->squads,
            'hasResults' => $room->results->isNotEmpty(),
        ]);
    }

    public function store(ResultStoreRequest $request, Room $room): RedirectResponse
    {
        $room->loadMissing([
            'category',
            'roomPrizes',
        ]);

        if ($room->results()->exists()) {
            return redirect()
                ->route('admin.results.index', $room)
                ->with('error', 'Results have already been entered for this room. Prize distribution cannot run twice.');
        }

        $summary = [];

        try {
            DB::transaction(function () use ($request, $room, &$summary): void {
                $room = Room::query()
                    ->lockForUpdate()
                    ->with([
                        'category',
                        'roomPrizes',
                        'results',
                    ])
                    ->findOrFail($room->id);

                if ($room->results->isNotEmpty()) {
                    throw new RuntimeException('Results already exist for this room.');
                }

                $prizesByPosition = $room->roomPrizes
                    ->keyBy('position');

                foreach ($request->validated('results') as $entry) {
                    $squad = $room->squads()
                        ->where('status', 'approved')
                        ->with('leader')
                        ->findOrFail($entry['squad_id']);

                    $position = (int) $entry['position'];
                    $totalKills = (int) $entry['total_kills'];
                    $prize = $prizesByPosition->get($position);
                    $rankPoint = (float) ($prize?->prize_amount ?? 0);
                    $killPoint = round($totalKills * (float) $room->category->kill_point, 2);
                    $totalPoint = round($killPoint + $rankPoint, 2);
                    $prizeWon = round((float) ($prize?->prize_amount ?? 0), 2);

                    Result::create([
                        'room_id' => $room->id,
                        'squad_id' => $squad->id,
                        'position' => $position,
                        'total_kills' => $totalKills,
                        'kill_point' => $killPoint,
                        'rank_point' => $rankPoint,
                        'total_point' => $totalPoint,
                        'prize_won' => $prizeWon,
                    ]);

                    if ($prizeWon > 0) {
                        $this->walletService->credit(
                            $squad->leader,
                            $prizeWon,
                            sprintf('Prize won - %s - Position %d', $room->title, $position),
                            $room->id
                        );

                        $summary[] = sprintf(
                            '%s won %.2f BDT for position %d',
                            $squad->leader->name,
                            $prizeWon,
                            $position
                        );
                    }
                }

                if ($room->status !== 'finished') {
                    $room->update(['status' => 'finished']);
                }
            });
        } catch (RuntimeException) {
            return redirect()
                ->route('admin.results.index', $room)
                ->with('error', 'Results have already been entered for this room. Prize distribution cannot run twice.');
        }

        if ($summary === []) {
            $summary[] = 'No prize amounts were configured for the submitted positions.';
        }

        return redirect()
            ->route('admin.results.index', $room)
            ->with('success', 'Results saved successfully. '.implode(' | ', $summary));
    }
}
