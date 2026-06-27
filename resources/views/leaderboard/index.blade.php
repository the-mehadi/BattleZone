<x-player-layout title="Leaderboard">
    <section class="mx-auto max-w-7xl px-6 py-14">
        <div class="mb-10">
            <p class="text-sm font-semibold uppercase tracking-[0.24em] text-orange-400">Leaderboard</p>
            <h1 class="mt-2 text-4xl font-black text-white">Top BattleZone Performers</h1>
            <p class="mt-3 max-w-2xl text-base text-slate-300">
                Rankings are based on finished room results, total points, and prize winnings.
            </p>
        </div>

        <div class="overflow-hidden rounded-[2rem] border border-slate-800 bg-slate-900/80 shadow-2xl shadow-slate-950/30">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-800">
                    <thead class="bg-slate-950/70">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Rank</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Squad</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Room</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Position</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Kills</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Points</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Prize Won</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
                        @forelse ($leaders as $leader)
                            <tr class="hover:bg-slate-800/30">
                                <td class="px-6 py-5">
                                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-orange-500/15 text-sm font-black text-orange-300">
                                        {{ $loop->iteration }}
                                    </span>
                                </td>
                                <td class="px-6 py-5">
                                    <div class="font-bold text-white">{{ $leader->squad?->effective_name ?? 'Unknown Squad' }}</div>
                                    <div class="mt-1 text-sm text-slate-400">{{ $leader->room?->category?->name ?? 'Unknown Category' }}</div>
                                </td>
                                <td class="px-6 py-5 text-sm text-slate-300">{{ $leader->room?->title ?? 'Unknown Room' }}</td>
                                <td class="px-6 py-5 text-sm font-semibold text-white">{{ $leader->position }}</td>
                                <td class="px-6 py-5 text-sm text-slate-300">{{ $leader->total_kills }}</td>
                                <td class="px-6 py-5 text-sm font-semibold text-white">{{ number_format((float) $leader->total_point, 2) }}</td>
                                <td class="px-6 py-5 text-sm font-semibold text-orange-300">৳ {{ number_format((float) $leader->prize_won, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <p class="text-lg font-semibold text-white">No leaderboard data yet.</p>
                                    <p class="mt-2 text-sm text-slate-400">Finished rooms will appear here once match results are submitted.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</x-player-layout>
