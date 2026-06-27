<x-player-layout title="Join Room">
    <section class="mx-auto max-w-5xl px-6 py-14">
        <div class="grid gap-8 xl:grid-cols-[1.1fr_0.9fr]">
            <div class="rounded-[2rem] border border-slate-800 bg-slate-900/80 p-8 shadow-2xl shadow-slate-950/30">
                <p class="text-sm font-semibold uppercase tracking-[0.24em] text-orange-400">Join Room</p>
                <h1 class="mt-3 text-4xl font-black text-white">{{ $room->title }}</h1>
                <p class="mt-4 text-base text-slate-300">
                    Fill in every player slot exactly as required for the selected room type, then confirm your entry fee payment.
                </p>

                <div class="mt-8 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                    <div class="rounded-2xl border border-slate-800 bg-slate-950/60 p-4">
                        <p class="text-xs uppercase tracking-[0.18em] text-slate-500">Map</p>
                        <p class="mt-2 text-base font-bold text-white">{{ $room->map }}</p>
                    </div>
                    <div class="rounded-2xl border border-slate-800 bg-slate-950/60 p-4">
                        <p class="text-xs uppercase tracking-[0.18em] text-slate-500">Mode</p>
                        <p class="mt-2 text-base font-bold text-white">{{ ucfirst($room->category->squad_type) }}</p>
                    </div>
                    <div class="rounded-2xl border border-slate-800 bg-slate-950/60 p-4">
                        <p class="text-xs uppercase tracking-[0.18em] text-slate-500">Entry Fee</p>
                        <p class="mt-2 text-base font-bold text-white">৳ {{ number_format((float) $room->entry_fee, 2) }}</p>
                    </div>
                    <div class="rounded-2xl border border-slate-800 bg-slate-950/60 p-4">
                        <p class="text-xs uppercase tracking-[0.18em] text-slate-500">Prize Pool</p>
                        <p class="mt-2 text-base font-bold text-white">৳ {{ number_format((float) $room->total_prize, 2) }}</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('rooms.join.store', $room) }}" class="mt-8 space-y-6">
                    @csrf

                    <div class="rounded-[1.5rem] border border-slate-800 bg-slate-950/60 p-6">
                        <div class="flex items-center justify-between gap-4">
                            <div>
                                <h2 class="text-xl font-bold text-white">Player Slots</h2>
                                <p class="mt-1 text-sm text-slate-400">
                                    This room requires exactly {{ $playerSlots }} {{ \Illuminate\Support\Str::plural('player', $playerSlots) }}.
                                </p>
                            </div>
                        </div>

                        @if ($errors->has('players'))
                            <div class="mt-4 rounded-2xl border border-rose-500/30 bg-rose-500/10 px-4 py-3 text-sm font-semibold text-rose-200">
                                {{ $errors->first('players') }}
                            </div>
                        @endif

                        <div class="mt-6 space-y-5">
                            @for ($i = 0; $i < $playerSlots; $i++)
                                <div class="grid gap-4 rounded-2xl border border-slate-800 bg-slate-900/80 p-5 md:grid-cols-2">
                                    <div>
                                        <label for="players_{{ $i }}_ingame_name" class="mb-2 block text-sm font-semibold text-slate-300">
                                            Player {{ $i + 1 }} In-game Name
                                        </label>
                                        <input
                                            id="players_{{ $i }}_ingame_name"
                                            name="players[{{ $i }}][ingame_name]"
                                            type="text"
                                            value="{{ old("players.$i.ingame_name") }}"
                                            class="block w-full rounded-xl border-slate-700 bg-slate-950 text-slate-100 focus:border-orange-500 focus:ring-orange-500"
                                            required
                                        >
                                        @error("players.$i.ingame_name")
                                            <p class="mt-2 text-sm font-medium text-rose-300">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="players_{{ $i }}_ingame_id" class="mb-2 block text-sm font-semibold text-slate-300">
                                            Player {{ $i + 1 }} In-game ID
                                        </label>
                                        <input
                                            id="players_{{ $i }}_ingame_id"
                                            name="players[{{ $i }}][ingame_id]"
                                            type="text"
                                            value="{{ old("players.$i.ingame_id") }}"
                                            class="block w-full rounded-xl border-slate-700 bg-slate-950 text-slate-100 focus:border-orange-500 focus:ring-orange-500"
                                            required
                                        >
                                        @error("players.$i.ingame_id")
                                            <p class="mt-2 text-sm font-medium text-rose-300">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            @endfor
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center justify-end gap-4">
                        <a href="{{ route('rooms.show', $room) }}" class="rounded-2xl border border-slate-700 px-6 py-3 text-sm font-bold text-slate-200 transition hover:bg-slate-800 hover:text-white">
                            Back To Room
                        </a>
                        <button
                            type="submit"
                            @disabled((float) auth()->user()->wallet_balance < (float) $room->entry_fee)
                            class="rounded-2xl bg-orange-500 px-6 py-3 text-sm font-bold text-slate-950 transition hover:bg-orange-400 disabled:cursor-not-allowed disabled:opacity-50"
                        >
                            Join &amp; Pay {{ number_format((float) $room->entry_fee, 2) }} BDT
                        </button>
                    </div>
                </form>
            </div>

            <div class="space-y-6">
                <div class="rounded-[2rem] border border-slate-800 bg-slate-900/80 p-6 shadow-xl shadow-slate-950/20">
                    <p class="text-sm font-semibold uppercase tracking-[0.2em] text-orange-400">Wallet Status</p>
                    <p class="mt-3 text-4xl font-black text-white">৳ {{ number_format((float) auth()->user()->wallet_balance, 2) }}</p>
                    <p class="mt-2 text-sm text-slate-400">Current wallet balance</p>

                    @if ((float) auth()->user()->wallet_balance < (float) $room->entry_fee)
                        <div class="mt-5 rounded-2xl border border-rose-500/30 bg-rose-500/10 px-4 py-4 text-sm font-semibold text-rose-200">
                            Wallet balance is insufficient for this entry fee.
                        </div>
                    @else
                        <div class="mt-5 rounded-2xl border border-emerald-500/30 bg-emerald-500/10 px-4 py-4 text-sm font-semibold text-emerald-200">
                            Wallet balance is enough to join this room.
                        </div>
                    @endif
                </div>

                <div class="rounded-[2rem] border border-slate-800 bg-slate-900/80 p-6 shadow-xl shadow-slate-950/20">
                    <h2 class="text-xl font-bold text-white">Before You Join</h2>
                    <ul class="mt-5 space-y-3 text-sm text-slate-300">
                        <li>Room must remain in `upcoming` status until your request is submitted.</li>
                        <li>Only one squad join request is allowed per player for the same room.</li>
                        <li>Entry fee is deducted immediately and your squad starts in `pending` review.</li>
                        <li>Every submitted player slot must be filled with a valid in-game name and ID.</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
</x-player-layout>
