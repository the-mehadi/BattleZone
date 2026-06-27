<x-player-layout title="Leaderboard">
    @php
        $podiumStyles = [
            0 => 'border-yellow-400/40 bg-gradient-to-br from-yellow-500/20 via-slate-900/90 to-slate-900/90',
            1 => 'border-slate-300/30 bg-gradient-to-br from-slate-300/15 via-slate-900/90 to-slate-900/90',
            2 => 'border-amber-700/40 bg-gradient-to-br from-amber-700/20 via-slate-900/90 to-slate-900/90',
        ];

        $podiumLabels = [
            0 => 'Gold',
            1 => 'Silver',
            2 => 'Bronze',
        ];
    @endphp

    <section class="mx-auto max-w-7xl px-6 py-14">
        <div class="mb-10 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.24em] text-orange-400">Leaderboard</p>
                <h1 class="mt-2 text-4xl font-black text-white">BattleZone Champions</h1>
                <p class="mt-3 max-w-2xl text-base text-slate-300">
                    Overall rankings are based on finished-room performance by squad leaders across the platform.
                </p>
            </div>

            <div class="rounded-3xl border border-slate-800 bg-slate-900/80 px-5 py-4 text-sm text-slate-300">
                <p>Total Ranked Leaders: <span class="font-semibold text-white">{{ $leaders->count() }}</span></p>
                <p class="mt-1">Recent Finished Matches: <span class="font-semibold text-white">{{ $recentResults->count() }}</span></p>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-3">
            @forelse ($topLeaders as $index => $leader)
                <div class="rounded-[2rem] border p-6 shadow-2xl shadow-slate-950/30 {{ $podiumStyles[$index] }}">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-sm font-semibold uppercase tracking-[0.24em] text-orange-300">{{ $podiumLabels[$index] }} Rank</p>
                            <h2 class="mt-3 text-2xl font-black text-white">{{ $leader->name }}</h2>
                            <p class="mt-2 text-sm text-slate-300">{{ $leader->phone }}</p>
                        </div>

                        <span class="inline-flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-950/70 text-2xl font-black text-white">
                            {{ $index + 1 }}
                        </span>
                    </div>

                    <div class="mt-8 grid gap-3 sm:grid-cols-3">
                        <div class="rounded-2xl border border-slate-800 bg-slate-950/60 px-4 py-4">
                            <p class="text-xs uppercase tracking-[0.18em] text-slate-400">Kills</p>
                            <p class="mt-2 text-xl font-bold text-white">{{ $leader->total_kills }}</p>
                        </div>
                        <div class="rounded-2xl border border-slate-800 bg-slate-950/60 px-4 py-4">
                            <p class="text-xs uppercase tracking-[0.18em] text-slate-400">Points</p>
                            <p class="mt-2 text-xl font-bold text-white">{{ number_format((float) $leader->total_points, 2) }}</p>
                        </div>
                        <div class="rounded-2xl border border-slate-800 bg-slate-950/60 px-4 py-4">
                            <p class="text-xs uppercase tracking-[0.18em] text-slate-400">Prize Won</p>
                            <p class="mt-2 text-xl font-bold text-orange-300">৳ {{ number_format((float) $leader->total_prize_won, 2) }}</p>
                        </div>
                    </div>
                </div>
            @empty
                <div class="rounded-[2rem] border border-slate-800 bg-slate-900/80 p-10 text-center text-slate-300 lg:col-span-3">
                    <p class="text-xl font-semibold text-white">No leaderboard data yet.</p>
                    <p class="mt-3 text-sm text-slate-400">Finished matches will appear here after moderators submit results.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-10 overflow-hidden rounded-[2rem] border border-slate-800 bg-slate-900/80 shadow-2xl shadow-slate-950/30">
            <div class="border-b border-slate-800 px-6 py-5">
                <h2 class="text-xl font-semibold text-white">Overall Standings</h2>
                <p class="mt-1 text-sm text-slate-400">The rest of the leaderboard is ranked after the top 3 podium finishers.</p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-800">
                    <thead class="bg-slate-950/70">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Rank</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Player Name</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Total Kills</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Total Points</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Prize Won</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800 bg-slate-900/70">
                        @forelse ($otherLeaders as $leader)
                            <tr class="hover:bg-slate-800/30">
                                <td class="px-6 py-5 text-sm font-semibold text-white">{{ $loop->iteration + 3 }}</td>
                                <td class="px-6 py-5">
                                    <div class="font-semibold text-white">{{ $leader->name }}</div>
                                    <div class="mt-1 text-sm text-slate-400">{{ $leader->phone }}</div>
                                </td>
                                <td class="px-6 py-5 text-sm text-slate-300">{{ $leader->total_kills }}</td>
                                <td class="px-6 py-5 text-sm font-semibold text-white">{{ number_format((float) $leader->total_points, 2) }}</td>
                                <td class="px-6 py-5 text-sm font-semibold text-orange-300">৳ {{ number_format((float) $leader->total_prize_won, 2) }}</td>
                            </tr>
                        @empty
                            @if ($leaders->isEmpty())
                                <tr>
                                    <td colspan="5" class="px-6 py-10 text-center">
                                        <p class="text-base font-medium text-white">No standings available yet.</p>
                                        <p class="mt-2 text-sm text-slate-400">Finished-room results are required before players appear in this table.</p>
                                    </td>
                                </tr>
                            @elseif ($leaders->count() <= 3)
                                <tr>
                                    <td colspan="5" class="px-6 py-10 text-center">
                                        <p class="text-base font-medium text-white">Only podium positions are available right now.</p>
                                        <p class="mt-2 text-sm text-slate-400">More finished-room results will expand the standings table.</p>
                                    </td>
                                </tr>
                            @endif
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-10 rounded-[2rem] border border-slate-800 bg-slate-900/80 p-6 shadow-2xl shadow-slate-950/30">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.2em] text-orange-400">Recent Match Results</p>
                    <h2 class="mt-2 text-2xl font-black text-white">Last 5 Finished Rooms</h2>
                </div>
                <p class="text-sm text-slate-400">Each room shows the winner submitted through the result entry system.</p>
            </div>

            <div class="mt-6 grid gap-4">
                @forelse ($recentResults as $room)
                    @php
                        $winner = $room->results->first();
                    @endphp

                    <div class="rounded-[1.5rem] border border-slate-800 bg-slate-950/60 p-5">
                        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                            <div>
                                <p class="text-sm font-semibold uppercase tracking-[0.18em] text-orange-300">{{ $room->category?->name ?? 'BattleZone Room' }}</p>
                                <h3 class="mt-2 text-xl font-bold text-white">{{ $room->title }}</h3>
                                <p class="mt-2 text-sm text-slate-400">{{ $room->match_time?->format('d M Y, h:i A') }}</p>
                            </div>

                            <div class="rounded-2xl border border-emerald-500/30 bg-emerald-500/10 px-5 py-4">
                                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-emerald-300">Winner</p>
                                <p class="mt-2 text-lg font-bold text-white">{{ $winner?->squad?->leader?->name ?? 'Unknown Player' }}</p>
                                <p class="mt-1 text-sm text-slate-300">{{ $winner?->squad?->effective_name ?? 'Unknown Squad' }}</p>
                                <p class="mt-2 text-sm text-emerald-200">
                                    {{ number_format((float) ($winner?->total_point ?? 0), 2) }} pts • {{ $winner?->total_kills ?? 0 }} kills
                                </p>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="rounded-[1.5rem] border border-slate-800 bg-slate-950/60 px-6 py-10 text-center">
                        <p class="text-lg font-semibold text-white">No recent finished rooms yet.</p>
                        <p class="mt-2 text-sm text-slate-400">Once finished matches have winners, they will show up here automatically.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>
</x-player-layout>
