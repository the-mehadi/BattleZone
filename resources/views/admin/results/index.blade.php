<x-admin-layout title="Room Results">
    <div class="space-y-6">
        <div class="grid gap-6 xl:grid-cols-[1.4fr_0.6fr]">
            <div class="rounded-3xl border border-slate-800 bg-slate-900/80 p-6 shadow-2xl shadow-slate-950/30">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-[0.2em] text-orange-400">{{ $room->category->name }}</p>
                        <h2 class="mt-2 text-3xl font-semibold text-white">{{ $room->title }}</h2>
                        <p class="mt-2 text-sm text-slate-400">
                            Enter final standings and kills for approved squads. Prize distribution runs automatically after submit.
                        </p>
                    </div>

                    <div class="text-sm text-slate-300">
                        <p>Match Time: <span class="font-semibold text-white">{{ $room->match_time->format('d M Y, h:i A') }}</span></p>
                        <p class="mt-1">Status: <span class="font-semibold text-white">{{ $room->status }}</span></p>
                    </div>
                </div>

                @if ($room->kill_prize_enabled)
                    <div class="mt-6 rounded-3xl border border-orange-500/30 bg-orange-500/10 px-5 py-4">
                        <p class="text-sm font-semibold uppercase tracking-[0.18em] text-orange-300">Kill Prize Active</p>
                        <p class="mt-2 text-base font-semibold text-white">⚡ Kill Prize Active - {{ number_format((float) $room->kill_prize_per_kill, 2) }} BDT per kill</p>
                        <p class="mt-1 text-sm text-orange-100">Every approved squad earns additional cash based on submitted kills.</p>
                    </div>
                @endif

                @if (session('payoutSummary'))
                    <div class="mt-6 rounded-3xl border border-emerald-500/30 bg-emerald-500/10 p-5">
                        <h3 class="text-lg font-semibold text-emerald-200">Payout Summary</h3>
                        <div class="mt-4 grid gap-3 md:grid-cols-3">
                            <div class="rounded-2xl border border-emerald-500/20 bg-slate-950/40 px-4 py-4">
                                <p class="text-xs uppercase tracking-[0.18em] text-emerald-300">Position Prizes</p>
                                <p class="mt-2 text-xl font-semibold text-white">৳ {{ number_format((float) session('payoutSummary.position_prizes'), 2) }}</p>
                            </div>
                            <div class="rounded-2xl border border-emerald-500/20 bg-slate-950/40 px-4 py-4">
                                <p class="text-xs uppercase tracking-[0.18em] text-emerald-300">Kill Prizes</p>
                                <p class="mt-2 text-xl font-semibold text-white">৳ {{ number_format((float) session('payoutSummary.kill_prizes'), 2) }}</p>
                            </div>
                            <div class="rounded-2xl border border-emerald-500/20 bg-slate-950/40 px-4 py-4">
                                <p class="text-xs uppercase tracking-[0.18em] text-emerald-300">Total Paid Out</p>
                                <p class="mt-2 text-xl font-semibold text-white">৳ {{ number_format((float) session('payoutSummary.total_paid_out'), 2) }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                @if ($hasResults)
                    <div class="mt-8 rounded-3xl border border-emerald-500/30 bg-emerald-500/10 p-5">
                        <h3 class="text-lg font-semibold text-emerald-200">Results Already Entered</h3>
                        <p class="mt-2 text-sm text-emerald-100">
                            Prize distribution has already been completed for this room. You can still correct mistakes below and wallet adjustments will be applied automatically.
                        </p>

                        <div class="mt-5 overflow-hidden rounded-2xl border border-slate-800">
                            <table class="min-w-full divide-y divide-slate-800">
                                <thead class="bg-slate-950/60">
                                    <tr>
                                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Squad</th>
                                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Leader</th>
                                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Position</th>
                                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Kills</th>
                                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Prize</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-800 bg-slate-900/60">
                                    @foreach ($room->results as $result)
                                        <tr>
                                            <td class="px-5 py-4 text-sm font-medium text-white">{{ $result->squad?->effective_name ?? 'Unknown Squad' }}</td>
                                            <td class="px-5 py-4 text-sm text-slate-300">{{ $result->squad?->leader?->name ?? 'Unknown Leader' }}</td>
                                            <td class="px-5 py-4 text-sm text-slate-300">{{ $result->position }}</td>
                                            <td class="px-5 py-4 text-sm text-slate-300">{{ $result->total_kills }}</td>
                                            <td class="px-5 py-4 text-sm font-semibold text-emerald-300">৳ {{ number_format((float) $result->prize_won, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                @if ($approvedSquads->isEmpty())
                    <div class="mt-8 rounded-3xl border border-slate-800 bg-slate-950/60 px-6 py-10 text-center">
                        <p class="text-lg font-semibold text-white">No approved squads available.</p>
                        <p class="mt-2 text-sm text-slate-400">Approve squads for this room before entering results.</p>
                    </div>
                @else
                    <form method="POST" action="{{ $hasResults ? route('admin.results.update', $room) : route('admin.results.store', $room) }}" class="mt-8">
                        @csrf
                        @if ($hasResults)
                            @method('PUT')
                        @endif

                        @if ($errors->has('results'))
                            <div class="mb-5 rounded-2xl border border-rose-500/30 bg-rose-500/10 px-4 py-3 text-sm text-rose-200">
                                {{ $errors->first('results') }}
                            </div>
                        @endif

                        <div class="overflow-hidden rounded-3xl border border-slate-800">
                            <table class="min-w-full divide-y divide-slate-800">
                                <thead class="bg-slate-950/60">
                                    <tr>
                                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Squad</th>
                                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Leader</th>
                                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Players</th>
                                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Position</th>
                                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Total Kills</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-800 bg-slate-900/60">
                                    @foreach ($approvedSquads as $index => $squad)
                                        <tr>
                                            <td class="px-5 py-4">
                                                <input type="hidden" name="results[{{ $index }}][squad_id]" value="{{ $squad->id }}">
                                                <p class="text-sm font-semibold text-white">{{ $squad->effective_name }}</p>
                                            </td>
                                            <td class="px-5 py-4 text-sm text-slate-300">{{ $squad->leader?->name ?? 'Unknown Leader' }}</td>
                                            <td class="px-5 py-4">
                                                <div class="space-y-1 text-xs text-slate-400">
                                                    @foreach ($squad->squadPlayers as $player)
                                                        <p>{{ $player->ingame_name }} ({{ $player->ingame_id }})</p>
                                                    @endforeach
                                                </div>
                                            </td>
                                            <td class="px-5 py-4">
                                                <input
                                                    type="number"
                                                    name="results[{{ $index }}][position]"
                                                    min="1"
                                                    value="{{ old("results.$index.position", $existingResults->get($squad->id)?->position) }}"
                                                    class="block w-28 rounded-xl border-slate-700 bg-slate-950 text-slate-100 focus:border-orange-500 focus:ring-orange-500"
                                                    required
                                                >
                                                @error("results.$index.position")
                                                    <p class="mt-2 text-xs text-rose-300">{{ $message }}</p>
                                                @enderror
                                            </td>
                                            <td class="px-5 py-4">
                                                <input
                                                    type="number"
                                                    name="results[{{ $index }}][total_kills]"
                                                    min="0"
                                                    value="{{ old("results.$index.total_kills", $existingResults->get($squad->id)?->total_kills ?? 0) }}"
                                                    class="block w-28 rounded-xl border-slate-700 bg-slate-950 text-slate-100 focus:border-orange-500 focus:ring-orange-500"
                                                    required
                                                >
                                                @error("results.$index.total_kills")
                                                    <p class="mt-2 text-xs text-rose-300">{{ $message }}</p>
                                                @enderror
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-6 flex justify-end">
                            <button type="submit" class="rounded-2xl bg-orange-500 px-6 py-3 text-sm font-semibold text-slate-950 transition hover:bg-orange-400">
                                {{ $hasResults ? 'Update Results & Adjust Prizes' : 'Save Results & Distribute Prizes' }}
                            </button>
                        </div>
                    </form>
                @endif
            </div>

            <div class="space-y-6">
                <div class="rounded-3xl border border-slate-800 bg-slate-900/80 p-6 shadow-2xl shadow-slate-950/30">
                    <h3 class="text-lg font-semibold text-white">Prize Table</h3>
                    <div class="mt-4 space-y-3">
                        @forelse ($room->roomPrizes as $prize)
                            <div class="flex items-center justify-between rounded-2xl border border-slate-800 bg-slate-950/60 px-4 py-3">
                                <span class="text-sm font-medium text-slate-300">Position {{ $prize->position }}</span>
                                <span class="text-sm font-semibold text-white">৳ {{ number_format((float) $prize->prize_amount, 2) }}</span>
                            </div>
                        @empty
                            <div class="rounded-2xl border border-slate-800 bg-slate-950/60 px-4 py-5 text-sm text-slate-400">
                                No prize distribution has been configured for this room.
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="rounded-3xl border border-slate-800 bg-slate-900/80 p-6 shadow-2xl shadow-slate-950/30">
                    <h3 class="text-lg font-semibold text-white">Scoring Rules</h3>
                    <div class="mt-4 space-y-3 text-sm text-slate-300">
                        <p>Kill point per kill: <span class="font-semibold text-white">{{ number_format((float) $room->category->kill_point, 2) }}</span></p>
                        <p>Rank point basis: <span class="font-semibold text-white">room prize amount for the submitted position</span></p>
                        <p>Total point formula: <span class="font-semibold text-white">kill_point + rank_point</span></p>
                        <p>Prize payout target: <span class="font-semibold text-white">squad leader wallet</span></p>
                        @if ($room->kill_prize_enabled)
                            <p>Kill prize payout: <span class="font-semibold text-white">{{ number_format((float) $room->kill_prize_per_kill, 2) }} BDT per kill</span></p>
                        @endif
                    </div>
                </div>

                <div class="rounded-3xl border border-slate-800 bg-slate-900/80 p-6 shadow-2xl shadow-slate-950/30">
                    <a href="{{ route('admin.rooms.show', $room) }}" class="inline-flex w-full items-center justify-center rounded-2xl border border-slate-700 px-5 py-3 text-sm font-semibold text-slate-300 transition hover:bg-slate-800 hover:text-white">
                        Back To Room
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
