<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ResultStoreRequest;
use App\Models\Result;
use App\Models\Room;
use App\Models\Squad;
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
            'existingResults' => $room->results->keyBy('squad_id'),
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
        $positionPrizeTotal = 0;
        $killPrizeTotal = 0;

        try {
            DB::transaction(function () use ($request, $room, &$summary, &$positionPrizeTotal, &$killPrizeTotal): void {
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

                    $result = Result::create([
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
                        $positionPrizeTotal += $prizeWon;

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

                    if ($room->kill_prize_enabled && (float) $room->kill_prize_per_kill > 0) {
                        $killPrize = round($totalKills * (float) $room->kill_prize_per_kill, 2);

                        if ($killPrize > 0) {
                            $this->walletService->credit(
                                $squad->leader,
                                $killPrize,
                                sprintf('Kill prize - %s - %d kills', $room->title, $totalKills),
                                $room->id
                            );

                            $result->increment('prize_won', $killPrize);
                            $killPrizeTotal += $killPrize;
                        }
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
            ->with('success', 'Results saved successfully. '.implode(' | ', $summary))
            ->with('payoutSummary', [
                'position_prizes' => round($positionPrizeTotal, 2),
                'kill_prizes' => round($killPrizeTotal, 2),
                'total_paid_out' => round($positionPrizeTotal + $killPrizeTotal, 2),
            ]);
    }

    public function update(ResultStoreRequest $request, Room $room): RedirectResponse
    {
        $room->loadMissing([
            'category',
            'roomPrizes',
            'results.squad.leader',
        ]);

        if (! $room->results()->exists()) {
            return redirect()
                ->route('admin.results.index', $room)
                ->with('error', 'No saved results were found for this room. Please create results first.');
        }

        $summary = [];

        try {
            DB::transaction(function () use ($request, $room, &$summary): void {
                $room = Room::query()
                    ->lockForUpdate()
                    ->with([
                        'category',
                        'roomPrizes',
                        'results.squad.leader',
                    ])
                    ->findOrFail($room->id);

                $prizesByPosition = $room->roomPrizes->keyBy('position');
                $existingResults = $room->results->keyBy('squad_id');

                foreach ($request->validated('results') as $entry) {
                    $squad = $room->squads()
                        ->where('status', 'approved')
                        ->with('leader')
                        ->findOrFail($entry['squad_id']);

                    $existingResult = $existingResults->get($squad->id);

                    if (! $existingResult) {
                        throw new RuntimeException('A submitted squad does not have an existing result entry to update.');
                    }

                    $calculated = $this->calculateResultValues($room, $entry['position'], $entry['total_kills'], $prizesByPosition);
                    $previousPrize = round((float) $existingResult->prize_won, 2);
                    $prizeDelta = round($calculated['prize_won'] - $previousPrize, 2);

                    if ($prizeDelta > 0) {
                        $this->walletService->credit(
                            $squad->leader,
                            $prizeDelta,
                            sprintf('Result adjustment credit - %s - Squad %s', $room->title, $squad->effective_name),
                            $room->id
                        );

                        $summary[] = sprintf(
                            '%s received an extra %.2f BDT after result correction',
                            $squad->leader->name,
                            $prizeDelta
                        );
                    } elseif ($prizeDelta < 0) {
                        $deducted = $this->walletService->deduct(
                            $squad->leader,
                            abs($prizeDelta),
                            sprintf('Result adjustment debit - %s - Squad %s', $room->title, $squad->effective_name),
                            $room->id
                        );

                        if (! $deducted) {
                            throw new RuntimeException(sprintf(
                                'Cannot reduce the prize for %s because the wallet does not have enough balance for the adjustment.',
                                $squad->leader->name
                            ));
                        }

                        $summary[] = sprintf(
                            '%.2f BDT was adjusted back from %s after result correction',
                            abs($prizeDelta),
                            $squad->leader->name
                        );
                    }

                    $existingResult->update([
                        'position' => $calculated['position'],
                        'total_kills' => $calculated['total_kills'],
                        'kill_point' => $calculated['kill_point'],
                        'rank_point' => $calculated['rank_point'],
                        'total_point' => $calculated['total_point'],
                        'prize_won' => $calculated['prize_won'],
                    ]);
                }
            });
        } catch (RuntimeException $exception) {
            return redirect()
                ->route('admin.results.index', $room)
                ->withInput()
                ->with('error', $exception->getMessage());
        }

        if ($summary === []) {
            $summary[] = 'No prize adjustments were needed for the corrected standings.';
        }

        return redirect()
            ->route('admin.results.index', $room)
            ->with('success', 'Results updated successfully. '.implode(' | ', $summary));
    }

    protected function calculateResultValues(Room $room, int $position, int $totalKills, $prizesByPosition): array
    {
        $prize = $prizesByPosition->get($position);
        $rankPoint = round((float) ($prize?->prize_amount ?? 0), 2);
        $killPoint = round($totalKills * (float) $room->category->kill_point, 2);
        $totalPoint = round($killPoint + $rankPoint, 2);
        $prizeWon = round((float) ($prize?->prize_amount ?? 0), 2);

        return [
            'position' => $position,
            'total_kills' => $totalKills,
            'kill_point' => $killPoint,
            'rank_point' => $rankPoint,
            'total_point' => $totalPoint,
            'prize_won' => $prizeWon,
        ];
    }
}
