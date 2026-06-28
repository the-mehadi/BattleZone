<x-player-layout title="Rooms">
    <section class="mx-auto max-w-7xl px-6 py-14">
        <div class="mb-10 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.24em] text-orange-400">Rooms</p>
                <h1 class="mt-2 text-4xl font-black text-white">Find your next match</h1>
                <p class="mt-3 max-w-2xl text-base text-slate-300">
                    Filter by room status and track every match with live countdowns before the drop goes live.
                </p>
            </div>

            <form method="GET" action="{{ route('rooms.index') }}" class="grid gap-3 sm:grid-cols-[220px_auto_auto]">
                <select name="status" class="rounded-2xl border border-slate-700 bg-slate-900 px-4 py-3 text-sm font-semibold text-slate-100 focus:border-orange-500 focus:ring-orange-500">
                    <option value="">All statuses</option>
                    @foreach (['upcoming', 'live', 'finished'] as $value)
                        <option value="{{ $value }}" @selected($status === $value)>{{ ucfirst($value) }}</option>
                    @endforeach
                </select>

                <button type="submit" class="rounded-2xl bg-orange-500 px-5 py-3 text-sm font-bold text-slate-950 transition hover:bg-orange-400">
                    Apply
                </button>

                <a href="{{ route('rooms.index') }}" class="rounded-2xl border border-slate-700 px-5 py-3 text-center text-sm font-bold text-slate-200 transition hover:bg-slate-800 hover:text-white">
                    Reset
                </a>
            </form>
        </div>

        <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
            @forelse ($rooms as $room)
                <article class="rounded-[2rem] border border-slate-800 bg-slate-900/75 p-6 shadow-xl shadow-slate-950/20" x-data="countdownTimer('{{ $room->match_time->toIso8601String() }}')">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-sm font-semibold uppercase tracking-[0.18em] text-orange-400">{{ $room->category->name }}</p>
                        </div>                        

                        <span class="{{ match ($room->status) { 'upcoming' => 'border-sky-500/30 bg-sky-500/10 text-sky-300', 'live' => 'border-emerald-500/30 bg-emerald-500/10 text-emerald-300', default => 'border-violet-500/30 bg-violet-500/10 text-violet-300' } }} rounded-full border px-3 py-1 text-xs font-semibold uppercase tracking-[0.16em]">
                            {{ $room->status }}
                        </span>
                    </div>

                    <div>
                        <h2 class="mt-2 text-2xl font-bold text-white">{{ $room->title }}</h2>
                        <div class="mt-4">
                            <x-slot-progress :room="$room" />
                        </div>
                    </div>

                    <div class="mt-5 rounded-2xl border border-slate-800 bg-slate-950/80 px-4 py-4">
                        <p class="text-xs uppercase tracking-[0.2em] text-slate-500">Countdown</p>
                        <p class="mt-2 text-lg font-black tracking-[0.16em] text-white" x-text="display"></p>
                    </div>

                    <div class="mt-5 space-y-3 text-sm text-slate-300">
                        <div class="flex items-center justify-between">
                            <span>Map</span>
                            <span class="font-semibold text-white">{{ $room->map }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span>Match Time</span>
                            <span class="font-semibold text-white">{{ $room->match_time->format('d M Y, h:i A') }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span>Entry Fee</span>
                            <span class="font-semibold text-white">৳ {{ number_format((float) $room->entry_fee, 2) }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span>Squads Joined</span>
                            <span class="font-semibold text-white">{{ $room->squads_count }}</span>
                        </div>
                    </div>

                    <div class="mt-6 grid gap-3">
                        <a href="{{ route('rooms.show', $room) }}" class="inline-flex w-full items-center justify-center rounded-2xl border border-slate-700 px-5 py-3 text-sm font-bold text-slate-200 transition hover:bg-slate-800 hover:text-white">
                            View Details
                        </a>

                        @if ($room->isFull())
                            <button type="button" disabled class="inline-flex w-full cursor-not-allowed items-center justify-center rounded-2xl border border-red-500/30 bg-red-500/10 px-5 py-3 text-sm font-bold text-red-300 opacity-90">
                                Room Full
                            </button>
                        @else
                            <a href="{{ route('rooms.join', $room) }}" class="inline-flex w-full items-center justify-center rounded-2xl bg-orange-500 px-5 py-3 text-sm font-bold text-slate-950 transition hover:bg-orange-400">
                                Join Match
                            </a>
                        @endif
                    </div>
                </article>
            @empty
                <div class="rounded-[2rem] border border-slate-800 bg-slate-900/75 px-6 py-12 text-center text-slate-300 md:col-span-2 xl:col-span-3">
                    No rooms matched the selected filter.
                </div>
            @endforelse
        </div>

        @if ($rooms->hasPages())
            <div class="mt-10">
                {{ $rooms->links() }}
            </div>
        @endif
    </section>
</x-player-layout>
