<x-player-layout title="{{ $room->title }}">
    <section class="mx-auto max-w-7xl px-6 py-14">
        <div class="grid gap-8 xl:grid-cols-[1.2fr_0.8fr]">
            <div class="rounded-[2rem] border border-slate-800 bg-slate-900/80 p-8 shadow-2xl shadow-slate-950/30">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-[0.24em] text-orange-400">{{ $room->category->name }}</p>
                        <h1 class="mt-3 text-4xl font-black text-white">{{ $room->title }}</h1>
                        <p class="mt-4 max-w-2xl text-base text-slate-300">
                            Prepare your squad, watch the countdown, and join this BattleZone room before the match begins.
                        </p>
                    </div>

                    <span class="{{ match ($room->status) { 'upcoming' => 'border-sky-500/30 bg-sky-500/10 text-sky-300', 'live' => 'border-emerald-500/30 bg-emerald-500/10 text-emerald-300', 'finished' => 'border-violet-500/30 bg-violet-500/10 text-violet-300', default => 'border-slate-700 bg-slate-800 text-slate-300' } }} rounded-full border px-4 py-2 text-xs font-semibold uppercase tracking-[0.16em]">
                        {{ $room->status }}
                    </span>
                </div>

                <div class="mt-8 rounded-[1.5rem] border border-slate-800 bg-slate-950/70 p-6" x-data="countdownTimer('{{ $room->match_time->toIso8601String() }}')">
                    <p class="text-xs uppercase tracking-[0.2em] text-slate-500">Countdown</p>
                    <p class="mt-3 text-3xl font-black tracking-[0.18em] text-white sm:text-4xl" x-text="display"></p>
                </div>

                <div class="mt-8 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                    <div class="rounded-2xl border border-slate-800 bg-slate-950/60 p-4">
                        <p class="text-xs uppercase tracking-[0.18em] text-slate-500">Map</p>
                        <p class="mt-2 text-base font-bold text-white">{{ $room->map }}</p>
                    </div>
                    <div class="rounded-2xl border border-slate-800 bg-slate-950/60 p-4">
                        <p class="text-xs uppercase tracking-[0.18em] text-slate-500">Match Time</p>
                        <p class="mt-2 text-base font-bold text-white">{{ $room->match_time->format('d M Y, h:i A') }}</p>
                    </div>
                    <div class="rounded-2xl border border-slate-800 bg-slate-950/60 p-4">
                        <p class="text-xs uppercase tracking-[0.18em] text-slate-500">Entry Fee</p>
                        <p class="mt-2 text-base font-bold text-white">৳ {{ number_format((float) $room->entry_fee, 2) }}</p>
                    </div>
                    <div class="rounded-2xl border border-slate-800 bg-slate-950/60 p-4">
                        <p class="text-xs uppercase tracking-[0.18em] text-slate-500">Squads Joined</p>
                        <p class="mt-2 text-base font-bold text-white">{{ $room->squads_count }}</p>
                    </div>
                </div>

                <div class="mt-8 rounded-[1.5rem] border border-slate-800 bg-slate-950/70 p-6">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <p class="text-lg font-bold text-white">Room Access</p>
                            <p class="mt-1 text-sm text-slate-400">Credentials become visible depending on moderator room lock status.</p>
                        </div>
                    </div>

                    @if ($room->is_room_locked)
                        <div class="mt-5 rounded-2xl border border-orange-500/20 bg-orange-500/10 px-5 py-4 text-sm font-semibold text-orange-200">
                            Room ID will be revealed before match.
                        </div>
                    @else
                        <div class="mt-5 grid gap-4 md:grid-cols-2">
                            <div class="rounded-2xl border border-slate-800 bg-slate-900 px-5 py-4">
                                <p class="text-xs uppercase tracking-[0.18em] text-slate-500">Room Code</p>
                                <p class="mt-2 font-mono text-lg font-bold text-white">{{ $room->room_code ?: 'Not set' }}</p>
                            </div>
                            <div class="rounded-2xl border border-slate-800 bg-slate-900 px-5 py-4">
                                <p class="text-xs uppercase tracking-[0.18em] text-slate-500">Room Password</p>
                                <p class="mt-2 font-mono text-lg font-bold text-white">{{ $room->room_password ?: 'Not set' }}</p>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="mt-8 flex flex-wrap gap-4">
                    @guest
                        <a href="{{ route('login') }}" class="rounded-2xl bg-orange-500 px-6 py-3 text-sm font-bold text-slate-950 transition hover:bg-orange-400">
                            Login To Join
                        </a>
                    @endguest

                    @auth
                        <a href="{{ route('rooms.join', $room) }}" class="rounded-2xl bg-orange-500 px-6 py-3 text-sm font-bold text-slate-950 transition hover:bg-orange-400">
                            Join This Room
                        </a>
                    @endauth

                    <a href="{{ route('rooms.index') }}" class="rounded-2xl border border-slate-700 px-6 py-3 text-sm font-bold text-slate-200 transition hover:bg-slate-800 hover:text-white">
                        Back To Rooms
                    </a>
                </div>
            </div>

            <div class="space-y-8">
                <div class="rounded-[2rem] border border-slate-800 bg-slate-900/80 p-6 shadow-xl shadow-slate-950/20">
                    <h2 class="text-xl font-bold text-white">Prize Pool</h2>
                    <div class="mt-5 space-y-3">
                        @forelse ($room->roomPrizes as $prize)
                            <div class="flex items-center justify-between rounded-2xl border border-slate-800 bg-slate-950/70 px-4 py-3">
                                <span class="text-sm font-semibold text-slate-300">Position {{ $prize->position }}</span>
                                <span class="text-sm font-bold text-white">৳ {{ number_format((float) $prize->prize_amount, 2) }}</span>
                            </div>
                        @empty
                            <div class="rounded-2xl border border-slate-800 bg-slate-950/70 px-4 py-5 text-sm text-slate-400">
                                Prize details are not available yet.
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="rounded-[2rem] border border-slate-800 bg-slate-900/80 p-6 shadow-xl shadow-slate-950/20">
                    <h2 class="text-xl font-bold text-white">Room Snapshot</h2>
                    <div class="mt-5 space-y-4 text-sm text-slate-300">
                        <div class="flex items-center justify-between">
                            <span>Status</span>
                            <span class="font-bold text-white">{{ ucfirst($room->status) }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span>Category</span>
                            <span class="font-bold text-white">{{ $room->category->name }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span>Prize Pool</span>
                            <span class="font-bold text-white">৳ {{ number_format((float) $room->total_prize, 2) }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span>Registered Squads</span>
                            <span class="font-bold text-white">{{ $room->squads_count }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </section>
</x-player-layout>
