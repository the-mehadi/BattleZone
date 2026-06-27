<x-player-layout title="Home">
    <section class="mx-auto max-w-7xl px-6 py-16 lg:py-24">
        <div class="grid items-center gap-10 lg:grid-cols-[1.2fr_0.8fr]">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.28em] text-orange-400">BattleZone Tournament Hub</p>
                <h1 class="mt-4 max-w-4xl text-4xl font-black leading-tight text-white sm:text-5xl lg:text-6xl">
                    Join competitive Free Fire rooms and climb the seasonal leaderboard.
                </h1>
                <p class="mt-6 max-w-2xl text-base leading-7 text-slate-300 sm:text-lg">
                    Discover upcoming matches, watch live countdowns, and prepare your squad before room details go live.
                </p>

                <div class="mt-8 flex flex-wrap gap-4">
                    <a href="{{ route('rooms.index') }}" class="rounded-2xl bg-orange-500 px-6 py-3 text-sm font-bold text-slate-950 transition hover:bg-orange-400">
                        Explore Rooms
                    </a>
                    <a href="{{ route('leaderboard.index') }}" class="rounded-2xl border border-slate-700 px-6 py-3 text-sm font-bold text-slate-200 transition hover:bg-slate-800 hover:text-white">
                        View Leaderboard
                    </a>
                </div>
            </div>

            <div class="rounded-[2rem] border border-orange-500/20 bg-slate-900/70 p-6 shadow-2xl shadow-orange-950/20">
                <p class="text-sm font-semibold uppercase tracking-[0.24em] text-orange-400">Next Drop</p>

                @if ($upcomingRooms->isNotEmpty())
                    @php($featuredRoom = $upcomingRooms->first())

                    <div class="mt-5 rounded-[1.5rem] border border-slate-800 bg-slate-950/70 p-6" x-data="countdownTimer('{{ $featuredRoom->match_time->toIso8601String() }}')">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <h2 class="text-2xl font-bold text-white">{{ $featuredRoom->title }}</h2>
                                <p class="mt-2 text-sm text-slate-400">{{ $featuredRoom->category->name }} • {{ $featuredRoom->map }}</p>
                            </div>

                            <span class="rounded-full border border-orange-500/30 bg-orange-500/10 px-3 py-1 text-xs font-semibold uppercase tracking-[0.16em] text-orange-300">
                                {{ $featuredRoom->status }}
                            </span>
                        </div>

                        <div class="mt-6 rounded-2xl border border-slate-800 bg-slate-900 px-4 py-5 text-center">
                            <p class="text-xs uppercase tracking-[0.22em] text-slate-500">Countdown</p>
                            <p class="mt-3 text-2xl font-black tracking-[0.18em] text-white sm:text-3xl" x-text="display"></p>
                        </div>

                        <div class="mt-6 grid gap-3 sm:grid-cols-3">
                            <div class="rounded-2xl border border-slate-800 bg-slate-900 px-4 py-3">
                                <p class="text-xs uppercase tracking-[0.18em] text-slate-500">Match Time</p>
                                <p class="mt-2 text-sm font-semibold text-white">{{ $featuredRoom->match_time->format('d M Y, h:i A') }}</p>
                            </div>
                            <div class="rounded-2xl border border-slate-800 bg-slate-900 px-4 py-3">
                                <p class="text-xs uppercase tracking-[0.18em] text-slate-500">Entry Fee</p>
                                <p class="mt-2 text-sm font-semibold text-white">৳ {{ number_format((float) $featuredRoom->entry_fee, 2) }}</p>
                            </div>
                            <div class="rounded-2xl border border-slate-800 bg-slate-900 px-4 py-3">
                                <p class="text-xs uppercase tracking-[0.18em] text-slate-500">Prize Pool</p>
                                <p class="mt-2 text-sm font-semibold text-white">৳ {{ number_format((float) $featuredRoom->total_prize, 2) }}</p>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="mt-5 rounded-[1.5rem] border border-slate-800 bg-slate-950/70 px-6 py-10 text-center">
                        <p class="text-lg font-semibold text-white">No upcoming rooms right now.</p>
                        <p class="mt-2 text-sm text-slate-400">Check back soon for fresh BattleZone drops.</p>
                    </div>
                @endif
            </div>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-6 pb-20">
        <div class="mb-8 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.24em] text-orange-400">Upcoming Rooms</p>
                <h2 class="mt-2 text-3xl font-bold text-white">Next 3 Matches</h2>
            </div>
            <a href="{{ route('rooms.index') }}" class="text-sm font-semibold text-orange-300 transition hover:text-orange-200">
                Browse all rooms
            </a>
        </div>

        <div class="grid gap-6 lg:grid-cols-3">
            @forelse ($upcomingRooms as $room)
                <article class="rounded-[2rem] border border-slate-800 bg-slate-900/75 p-6 shadow-xl shadow-slate-950/20" x-data="countdownTimer('{{ $room->match_time->toIso8601String() }}')">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h3 class="text-xl font-bold text-white">{{ $room->title }}</h3>
                            <p class="mt-2 text-sm text-slate-400">{{ $room->category->name }} • {{ ucfirst($room->status) }}</p>
                        </div>

                        <span class="rounded-full border border-orange-500/30 bg-orange-500/10 px-3 py-1 text-xs font-semibold uppercase tracking-[0.16em] text-orange-300">
                            {{ $room->map }}
                        </span>
                    </div>

                    <div class="mt-5 rounded-2xl border border-slate-800 bg-slate-950/80 px-4 py-4">
                        <p class="text-xs uppercase tracking-[0.2em] text-slate-500">Countdown</p>
                        <p class="mt-2 text-lg font-black tracking-[0.16em] text-white" x-text="display"></p>
                    </div>

                    <div class="mt-5 space-y-3 text-sm text-slate-300">
                        <div class="flex items-center justify-between">
                            <span>Match Time</span>
                            <span class="font-semibold text-white">{{ $room->match_time->format('d M Y, h:i A') }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span>Entry Fee</span>
                            <span class="font-semibold text-white">৳ {{ number_format((float) $room->entry_fee, 2) }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span>Prize Pool</span>
                            <span class="font-semibold text-white">৳ {{ number_format((float) $room->total_prize, 2) }}</span>
                        </div>
                    </div>

                    <a href="{{ route('rooms.show', $room) }}" class="mt-6 inline-flex w-full items-center justify-center rounded-2xl bg-orange-500 px-5 py-3 text-sm font-bold text-slate-950 transition hover:bg-orange-400">
                        View Room
                    </a>
                </article>
            @empty
                <div class="rounded-[2rem] border border-slate-800 bg-slate-900/75 px-6 py-12 text-center text-slate-300 lg:col-span-3">
                    No upcoming rooms available.
                </div>
            @endforelse
        </div>
    </section>
</x-player-layout>
