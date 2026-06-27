<x-player-layout title="My Squads">
    <section class="mx-auto max-w-7xl px-6 py-14">
        <div class="mb-10">
            <p class="text-sm font-semibold uppercase tracking-[0.24em] text-orange-400">My Squads</p>
            <h1 class="mt-2 text-4xl font-black text-white">Your joined rooms</h1>
            <p class="mt-3 max-w-2xl text-base text-slate-300">
                Review every squad you have joined, the room schedule, and current approval status.
            </p>
        </div>

        <div class="space-y-6">
            @forelse ($squads as $squad)
                <article class="rounded-[2rem] border border-slate-800 bg-slate-900/80 p-6 shadow-xl shadow-slate-950/20">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                        <div>
                            <p class="text-sm font-semibold uppercase tracking-[0.18em] text-orange-400">{{ $squad->room->category->name }}</p>
                            <h2 class="mt-2 text-2xl font-bold text-white">{{ $squad->room->title }}</h2>
                            <p class="mt-2 text-sm text-slate-400">
                                Match Time: {{ $squad->room->match_time->format('d M Y, h:i A') }}
                            </p>
                        </div>

                        <div class="flex flex-wrap gap-3">
                            <span class="{{ match ($squad->status) { 'approved' => 'border-emerald-500/30 bg-emerald-500/10 text-emerald-300', 'rejected' => 'border-rose-500/30 bg-rose-500/10 text-rose-300', default => 'border-amber-500/30 bg-amber-500/10 text-amber-300' } }} rounded-full border px-4 py-2 text-xs font-semibold uppercase tracking-[0.16em]">
                                {{ $squad->status }}
                            </span>

                            <a href="{{ route('rooms.show', $squad->room) }}" class="rounded-2xl border border-slate-700 px-4 py-2 text-sm font-bold text-slate-200 transition hover:bg-slate-800 hover:text-white">
                                View Room
                            </a>
                        </div>
                    </div>

                    <div class="mt-6 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                        @forelse ($squad->squadPlayers as $player)
                            <div class="rounded-2xl border border-slate-800 bg-slate-950/70 px-4 py-4">
                                <p class="text-sm font-bold text-white">{{ $player->ingame_name }}</p>
                                <p class="mt-1 text-xs uppercase tracking-[0.18em] text-slate-500">{{ $player->ingame_id }}</p>
                            </div>
                        @empty
                            <div class="rounded-2xl border border-slate-800 bg-slate-950/70 px-4 py-5 text-sm text-slate-400 md:col-span-2 xl:col-span-4">
                                No squad members were found for this room.
                            </div>
                        @endforelse
                    </div>
                </article>
            @empty
                <div class="rounded-[2rem] border border-slate-800 bg-slate-900/80 px-6 py-14 text-center shadow-xl shadow-slate-950/20">
                    <p class="text-xl font-bold text-white">You have not joined any squads yet.</p>
                    <p class="mt-3 text-sm text-slate-400">Browse available rooms and register your squad to get started.</p>
                    <a href="{{ route('rooms.index') }}" class="mt-6 inline-flex rounded-2xl bg-orange-500 px-6 py-3 text-sm font-bold text-slate-950 transition hover:bg-orange-400">
                        Browse Rooms
                    </a>
                </div>
            @endforelse
        </div>
    </section>
</x-player-layout>
