<x-admin-layout title="Room Details">
    <div class="space-y-6">
        <div class="grid gap-6 xl:grid-cols-[2fr_1fr]">
            <div class="rounded-3xl border border-slate-800 bg-slate-900/80 p-6 shadow-2xl shadow-slate-950/30">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                    <div>
                        <p class="text-sm font-medium uppercase tracking-[0.2em] text-orange-400">{{ $room->category->name }}</p>
                        <h2 class="mt-2 text-3xl font-semibold text-white">{{ $room->title }}</h2>
                        <p class="mt-3 max-w-2xl text-sm text-slate-400">
                            Manage room visibility, change match state, and review squads and results from one place.
                        </p>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('admin.rooms.edit', $room) }}" class="rounded-xl border border-slate-700 px-4 py-2 text-sm font-semibold text-slate-300 transition hover:bg-slate-800 hover:text-white">
                            Edit Room
                        </a>

                        @if ($room->status === 'finished' || $room->results->isNotEmpty())
                            <a href="{{ route('admin.results.index', $room) }}" class="rounded-xl bg-orange-500 px-4 py-2 text-sm font-semibold text-slate-950 transition hover:bg-orange-400">
                                {{ $room->results->isNotEmpty() ? 'View Results' : 'Enter Results' }}
                            </a>
                        @endif

                        @if ($room->status === 'upcoming')
                            <form method="POST" action="{{ route('admin.rooms.destroy', $room) }}" onsubmit="return confirm('Delete this room?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="rounded-xl border border-rose-500/30 bg-rose-500/10 px-4 py-2 text-sm font-semibold text-rose-300 transition hover:bg-rose-500/20">
                                    Delete Room
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                <div class="mt-8 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                    <div class="rounded-2xl border border-slate-800 bg-slate-950/60 p-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Map</p>
                        <p class="mt-3 text-lg font-semibold text-white">{{ $room->map }}</p>
                    </div>
                    <div class="rounded-2xl border border-slate-800 bg-slate-950/60 p-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Match Time</p>
                        <p class="mt-3 text-lg font-semibold text-white">{{ $room->match_time->format('d M Y, h:i A') }}</p>
                    </div>
                    <div class="rounded-2xl border border-slate-800 bg-slate-950/60 p-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Entry Fee</p>
                        <p class="mt-3 text-lg font-semibold text-white">৳ {{ number_format((float) $room->entry_fee, 2) }}</p>
                    </div>
                    <div class="rounded-2xl border border-slate-800 bg-slate-950/60 p-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Prize Pool</p>
                        <p class="mt-3 text-lg font-semibold text-white">৳ {{ number_format((float) $room->total_prize, 2) }}</p>
                    </div>
                </div>

                <div class="mt-8 grid gap-4 lg:grid-cols-2">
                    <div class="rounded-2xl border border-slate-800 bg-slate-950/60 p-5">
                        <div class="flex items-center justify-between gap-4">
                            <div>
                                <p class="text-sm font-semibold text-white">Room Credentials</p>
                                <p class="mt-1 text-xs uppercase tracking-[0.2em] text-slate-500">
                                    {{ $room->is_room_locked ? 'Locked' : 'Unlocked' }}
                                </p>
                            </div>

                            <form method="POST" action="{{ route('admin.rooms.toggle-lock', $room) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="rounded-xl {{ $room->is_room_locked ? 'bg-orange-500 text-slate-950 hover:bg-orange-400' : 'bg-slate-700 text-white hover:bg-slate-600' }} px-4 py-2 text-sm font-semibold transition">
                                    {{ $room->is_room_locked ? 'Unlock' : 'Lock' }}
                                </button>
                            </form>
                        </div>

                        <div class="mt-5 space-y-4 text-sm">
                            <div class="rounded-xl border border-slate-800 bg-slate-900 px-4 py-3">
                                <p class="text-xs uppercase tracking-[0.2em] text-slate-500">Room Code</p>
                                <p class="mt-2 font-mono text-base text-white">
                                    {{ $room->is_room_locked ? '••••••••' : ($room->room_code ?: 'Not assigned') }}
                                </p>
                            </div>
                            <div class="rounded-xl border border-slate-800 bg-slate-900 px-4 py-3">
                                <p class="text-xs uppercase tracking-[0.2em] text-slate-500">Room Password</p>
                                <p class="mt-2 font-mono text-base text-white">
                                    {{ $room->is_room_locked ? '••••••••' : ($room->room_password ?: 'Not assigned') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-slate-800 bg-slate-950/60 p-5">
                        <p class="text-sm font-semibold text-white">Status Controls</p>
                        <p class="mt-1 text-xs uppercase tracking-[0.2em] text-slate-500">Current: {{ $room->status }}</p>

                        @php
                            $statusActions = match ($room->status) {
                                'upcoming' => ['live' => 'Start Live', 'cancelled' => 'Cancel Room'],
                                'live' => ['finished' => 'Mark Finished', 'cancelled' => 'Cancel Room'],
                                default => [],
                            };
                        @endphp

                        <div class="mt-5 flex flex-wrap gap-3">
                            @forelse ($statusActions as $value => $label)
                                <form method="POST" action="{{ route('admin.rooms.update-status', $room) }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="{{ $value }}">
                                    <button type="submit" class="rounded-xl border border-slate-700 bg-slate-900 px-4 py-2 text-sm font-semibold text-slate-200 transition hover:bg-slate-800 hover:text-white">
                                        {{ $label }}
                                    </button>
                                </form>
                            @empty
                                <div class="rounded-xl border border-slate-800 bg-slate-900 px-4 py-3 text-sm text-slate-400">
                                    No further status changes are available.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="rounded-3xl border border-slate-800 bg-slate-900/80 p-6 shadow-2xl shadow-slate-950/30">
                    <h3 class="text-lg font-semibold text-white">Prize Breakdown</h3>
                    <div class="mt-4 space-y-3">
                        @forelse ($room->roomPrizes as $prize)
                            <div class="flex items-center justify-between rounded-2xl border border-slate-800 bg-slate-950/60 px-4 py-3">
                                <span class="text-sm font-medium text-slate-300">Position {{ $prize->position }}</span>
                                <span class="text-sm font-semibold text-white">৳ {{ number_format((float) $prize->prize_amount, 2) }}</span>
                            </div>
                        @empty
                            <p class="rounded-2xl border border-slate-800 bg-slate-950/60 px-4 py-5 text-sm text-slate-400">
                                No room prizes configured.
                            </p>
                        @endforelse
                    </div>
                </div>

                <div class="rounded-3xl border border-slate-800 bg-slate-900/80 p-6 shadow-2xl shadow-slate-950/30">
                    <h3 class="text-lg font-semibold text-white">Quick Stats</h3>
                    <div class="mt-4 grid gap-3">
                        <div class="rounded-2xl border border-slate-800 bg-slate-950/60 px-4 py-3">
                            <p class="text-xs uppercase tracking-[0.2em] text-slate-500">Total Squads</p>
                            <p class="mt-2 text-2xl font-semibold text-white">{{ $room->squads->count() }}</p>
                        </div>
                        <div class="rounded-2xl border border-slate-800 bg-slate-950/60 px-4 py-3">
                            <p class="text-xs uppercase tracking-[0.2em] text-slate-500">Approved Squads</p>
                            <p class="mt-2 text-2xl font-semibold text-white">{{ $room->squads->where('status', 'approved')->count() }}</p>
                        </div>
                        <div class="rounded-2xl border border-slate-800 bg-slate-950/60 px-4 py-3">
                            <p class="text-xs uppercase tracking-[0.2em] text-slate-500">Results Entered</p>
                            <p class="mt-2 text-2xl font-semibold text-white">{{ $room->results->count() }}</p>
                        </div>
                    </div>

                    @if ($room->status === 'finished' || $room->results->isNotEmpty())
                        <a href="{{ route('admin.results.index', $room) }}" class="mt-4 inline-flex w-full items-center justify-center rounded-2xl border border-orange-500/30 bg-orange-500/10 px-4 py-3 text-sm font-semibold text-orange-300 transition hover:bg-orange-500/20">
                            {{ $room->results->isNotEmpty() ? 'Open Result Summary' : 'Open Result Entry' }}
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <div class="rounded-3xl border border-slate-800 bg-slate-900/80 p-6 shadow-2xl shadow-slate-950/30">
            <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                <div>
                    <h3 class="text-xl font-semibold text-white">Squad List</h3>
                    <p class="mt-1 text-sm text-slate-400">Review squad rosters and moderate approval statuses before the match starts.</p>
                </div>
            </div>

            <div class="mt-6 space-y-4">
                @forelse ($room->squads as $squad)
                    <div class="rounded-3xl border border-slate-800 bg-slate-950/60 p-5">
                        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                            <div>
                                <div class="flex flex-wrap items-center gap-3">
                                    <h4 class="text-lg font-semibold text-white">{{ $squad->effective_name }}</h4>
                                    <span class="{{ match ($squad->status) { 'approved' => 'border-emerald-500/30 bg-emerald-500/10 text-emerald-300', 'rejected' => 'border-rose-500/30 bg-rose-500/10 text-rose-300', default => 'border-amber-500/30 bg-amber-500/10 text-amber-300' } }} inline-flex rounded-full border px-3 py-1 text-xs font-semibold uppercase tracking-wide">
                                        {{ $squad->status }}
                                    </span>
                                </div>
                                <p class="mt-2 text-sm text-slate-400">
                                    Leader: <span class="font-medium text-slate-200">{{ $squad->leader?->name ?? 'Unknown' }}</span>
                                </p>
                                <p class="mt-1 text-sm text-slate-400">
                                    Total Paid: <span class="font-medium text-slate-200">৳ {{ number_format((float) $squad->total_paid, 2) }}</span>
                                </p>
                            </div>

                            <div class="flex flex-wrap gap-2">
                                <form method="POST" action="{{ route('admin.rooms.squads.update-status', [$room, $squad]) }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="approved">
                                    <button type="submit" class="rounded-xl border border-emerald-500/30 bg-emerald-500/10 px-4 py-2 text-sm font-semibold text-emerald-300 transition hover:bg-emerald-500/20">
                                        Approve
                                    </button>
                                </form>

                                <form method="POST" action="{{ route('admin.rooms.squads.update-status', [$room, $squad]) }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="rejected">
                                    <button type="submit" class="rounded-xl border border-rose-500/30 bg-rose-500/10 px-4 py-2 text-sm font-semibold text-rose-300 transition hover:bg-rose-500/20">
                                        Reject
                                    </button>
                                </form>
                            </div>
                        </div>

                        <div class="mt-5 grid gap-3 md:grid-cols-2 xl:grid-cols-4">
                            @forelse ($squad->squadPlayers as $player)
                                <div class="rounded-2xl border border-slate-800 bg-slate-900 px-4 py-3">
                                    <p class="text-sm font-semibold text-white">{{ $player->ingame_name }}</p>
                                    <p class="mt-1 text-xs uppercase tracking-[0.2em] text-slate-500">{{ $player->ingame_id }}</p>
                                </div>
                            @empty
                                <div class="rounded-2xl border border-slate-800 bg-slate-900 px-4 py-5 text-sm text-slate-400 md:col-span-2 xl:col-span-4">
                                    No players have been added to this squad yet.
                                </div>
                            @endforelse
                        </div>
                    </div>
                @empty
                    <div class="rounded-3xl border border-slate-800 bg-slate-950/60 px-6 py-10 text-center">
                        <p class="text-lg font-medium text-slate-200">No squads joined this room yet.</p>
                        <p class="mt-2 text-sm text-slate-400">Squads will appear here once players register for the match.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <div class="rounded-3xl border border-slate-800 bg-slate-900/80 p-6 shadow-2xl shadow-slate-950/30">
            <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                <div>
                    <h3 class="text-xl font-semibold text-white">Result Entry Section</h3>
                    <p class="mt-1 text-sm text-slate-400">Review entered results and track room standings once the match is finished.</p>
                </div>

                <span class="rounded-full border border-slate-700 bg-slate-950 px-4 py-2 text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">
                    Status: {{ $room->status }}
                </span>
            </div>

            <div class="mt-6 overflow-hidden rounded-3xl border border-slate-800">
                <table class="min-w-full divide-y divide-slate-800">
                    <thead class="bg-slate-950/70">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Squad</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Position</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Kills</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Total Point</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Prize Won</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800 bg-slate-900/60">
                        @forelse ($room->results as $result)
                            <tr>
                                <td class="px-6 py-4 text-sm font-medium text-white">{{ $result->squad?->effective_name ?? 'Unknown Squad' }}</td>
                                <td class="px-6 py-4 text-sm text-slate-300">{{ $result->position }}</td>
                                <td class="px-6 py-4 text-sm text-slate-300">{{ $result->total_kills }}</td>
                                <td class="px-6 py-4 text-sm text-slate-300">{{ number_format((float) $result->total_point, 2) }}</td>
                                <td class="px-6 py-4 text-sm text-slate-300">৳ {{ number_format((float) $result->prize_won, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center">
                                    <p class="text-lg font-medium text-slate-200">No results entered yet.</p>
                                    <p class="mt-2 text-sm text-slate-400">Finish the room and enter match results to populate this section.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-admin-layout>
