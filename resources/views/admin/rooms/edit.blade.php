@php
    $categoryOptions = $categories
        ->map(fn ($category) => [
            'id' => $category->id,
            'name' => $category->name,
            'map' => $category->map,
            'entry_fee' => (float) $category->entry_fee,
            'prizes' => $category->categoryPrizes
                ->sortBy('position')
                ->map(fn ($prize) => [
                    'position' => (int) $prize->position,
                    'prize_amount' => (float) $prize->prize_amount,
                ])
                ->values()
                ->all(),
        ])
        ->values()
        ->all();

    $initialPrizes = collect(old('prizes', $prizes ?? []))
        ->map(fn ($prize) => [
            'position' => (int) ($prize['position'] ?? 1),
            'prize_amount' => (float) ($prize['prize_amount'] ?? 0),
        ])
        ->values()
        ->all();

    $selectedCategoryId = (string) old('category_id', $room->category_id ?: ($categories->first()?->id));
@endphp

<x-admin-layout title="Edit Room">
    <div class="mx-auto max-w-6xl">
        <div class="rounded-3xl border border-slate-800 bg-slate-900/80 p-6 shadow-2xl shadow-slate-950/30 md:p-8">
            <div class="mb-8">
                <h2 class="text-2xl font-semibold text-white">Edit Tournament Room</h2>
                <p class="mt-2 text-sm text-slate-400">Update room timing, secure codes, and the reward structure for this match.</p>
            </div>

            <form method="POST" action="{{ route('admin.rooms.update', $room) }}">
                @csrf
                @method('PUT')

                <div
                    x-data="{
                        categories: @js($categoryOptions),
                        selectedCategoryId: @js($selectedCategoryId),
                        form: {
                            map: @js(old('map', $room->map)),
                            entry_fee: @js((float) old('entry_fee', $room->entry_fee ?? 0)),
                            total_prize: @js((float) old('total_prize', $room->total_prize ?? 0)),
                        },
                        prizes: @js($initialPrizes),
                        killPrizeEnabled: @js((bool) old('kill_prize_enabled', $room->kill_prize_enabled)),
                        killPrizePerKill: @js((float) old('kill_prize_per_kill', $room->kill_prize_per_kill ?? 0)),
                        init() {
                            if (!this.prizes.length) {
                                this.applyCategoryDefaults();
                            } else if (!this.form.total_prize) {
                                this.recalculateTotalPrize();
                            }
                        },
                        selectedCategory() {
                            return this.categories.find((category) => String(category.id) === String(this.selectedCategoryId)) ?? null;
                        },
                        applyCategoryDefaults() {
                            const category = this.selectedCategory();

                            if (!category) return;

                            this.form.map = category.map;
                            this.form.entry_fee = category.entry_fee;
                            this.prizes = category.prizes.length
                                ? category.prizes.map((prize) => ({ ...prize }))
                                : [
                                    { position: 1, prize_amount: 0 },
                                    { position: 2, prize_amount: 0 },
                                    { position: 3, prize_amount: 0 },
                                ];
                            this.recalculateTotalPrize();
                        },
                        addPrize() {
                            if (this.prizes.length >= 10) return;

                            const usedPositions = this.prizes.map((prize) => Number(prize.position));
                            let nextPosition = 1;

                            while (usedPositions.includes(nextPosition) && nextPosition <= 10) {
                                nextPosition++;
                            }

                            this.prizes.push({
                                position: nextPosition <= 10 ? nextPosition : 10,
                                prize_amount: 0,
                            });

                            this.recalculateTotalPrize();
                        },
                        removePrize(index) {
                            if (Number(this.prizes[index]?.position) <= 3 || this.prizes.length <= 3) return;

                            this.prizes.splice(index, 1);
                            this.recalculateTotalPrize();
                        },
                        recalculateTotalPrize() {
                            this.form.total_prize = this.prizes.reduce((carry, prize) => {
                                return carry + (Number(prize.prize_amount) || 0);
                            }, 0);
                        },
                        toggleKillPrize() {
                            this.killPrizeEnabled = !this.killPrizeEnabled;

                            if (!this.killPrizeEnabled) {
                                this.killPrizePerKill = 0;
                            }
                        }
                    }"
                    class="space-y-8"
                >
                    <div class="grid gap-6 lg:grid-cols-2">
                        <div class="space-y-2">
                            <x-input-label for="title" :value="__('Room Title')" />
                            <x-text-input id="title" name="title" type="text" class="block w-full border-slate-700 bg-slate-900 text-slate-100" :value="old('title', $room->title)" required />
                            <x-input-error :messages="$errors->get('title')" class="mt-2" />
                        </div>

                        <div class="space-y-2">
                            <x-input-label for="category_id" :value="__('Category')" />
                            <select
                                id="category_id"
                                name="category_id"
                                x-model="selectedCategoryId"
                                @change="applyCategoryDefaults()"
                                class="block w-full rounded-xl border-slate-700 bg-slate-900 text-slate-100 focus:border-orange-500 focus:ring-orange-500"
                                required
                            >
                                <option value="">Select category</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }} ({{ ucfirst($category->squad_type) }})</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
                        </div>

                        <div class="space-y-2">
                            <x-input-label for="map" :value="__('Map')" />
                            <input id="map" name="map" type="text" x-model="form.map" class="block w-full rounded-xl border-slate-700 bg-slate-900 text-slate-100 focus:border-orange-500 focus:ring-orange-500" required>
                            <x-input-error :messages="$errors->get('map')" class="mt-2" />
                        </div>

                        <div class="space-y-2">
                            <x-input-label for="match_time" :value="__('Match Time')" />
                            <x-text-input id="match_time" name="match_time" type="datetime-local" class="block w-full border-slate-700 bg-slate-900 text-slate-100" :value="old('match_time', optional($room->match_time)->format('Y-m-d\\TH:i'))" required />
                            <x-input-error :messages="$errors->get('match_time')" class="mt-2" />
                        </div>

                        <div class="space-y-2">
                            <x-input-label for="entry_fee" :value="__('Entry Fee')" />
                            <input id="entry_fee" name="entry_fee" type="number" min="0" step="0.01" x-model="form.entry_fee" class="block w-full rounded-xl border-slate-700 bg-slate-900 text-slate-100 focus:border-orange-500 focus:ring-orange-500" required>
                            <x-input-error :messages="$errors->get('entry_fee')" class="mt-2" />
                        </div>

                        <div class="space-y-2">
                            <x-input-label for="total_prize" :value="__('Total Prize')" />
                            <input id="total_prize" name="total_prize" type="number" min="0" step="0.01" x-model="form.total_prize" class="block w-full rounded-xl border-slate-700 bg-slate-900 text-slate-100 focus:border-orange-500 focus:ring-orange-500" required>
                            <x-input-error :messages="$errors->get('total_prize')" class="mt-2" />
                        </div>

                        <div class="space-y-2">
                            <x-input-label for="room_code" :value="__('Room Code')" />
                            <x-text-input id="room_code" name="room_code" type="text" class="block w-full border-slate-700 bg-slate-900 text-slate-100" :value="old('room_code', $room->room_code)" />
                            <x-input-error :messages="$errors->get('room_code')" class="mt-2" />
                        </div>

                        <div class="space-y-2">
                            <x-input-label for="room_password" :value="__('Room Password')" />
                            <x-text-input id="room_password" name="room_password" type="text" class="block w-full border-slate-700 bg-slate-900 text-slate-100" :value="old('room_password', $room->room_password)" />
                            <x-input-error :messages="$errors->get('room_password')" class="mt-2" />
                        </div>
                    </div>

                    <div class="rounded-3xl border border-slate-800 bg-slate-900/80 p-5">
                        <div class="mb-4 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-white">Room Prizes</h3>
                                <p class="text-sm text-slate-400">These values are copied into `room_prizes`. Change category to reload defaults.</p>
                            </div>

                            <div class="flex flex-wrap gap-2">
                                <button type="button" @click="applyCategoryDefaults()" class="rounded-xl border border-orange-500/30 bg-orange-500/10 px-4 py-2 text-sm font-semibold text-orange-300 transition hover:bg-orange-500/20">
                                    Load Category Prizes
                                </button>
                                <button type="button" @click="addPrize()" class="rounded-xl bg-orange-500 px-4 py-2 text-sm font-semibold text-slate-950 transition hover:bg-orange-400">
                                    Add Prize Row
                                </button>
                            </div>
                        </div>

                        <div class="mb-4 rounded-2xl border border-orange-500/20 bg-orange-500/10 px-4 py-4">
                            <div class="flex items-center justify-between gap-3">
                                <div>
                                    <p class="text-sm font-semibold text-white">Position Prize - Always Active</p>
                                    <p class="mt-1 text-sm text-orange-200">1st, 2nd, and 3rd position prizes always stay enabled for every room.</p>
                                </div>
                                <span class="inline-flex rounded-full border border-orange-400/30 bg-orange-500/20 px-3 py-1 text-xs font-semibold uppercase tracking-[0.16em] text-orange-200">
                                    Locked
                                </span>
                            </div>
                        </div>

                        <x-input-error :messages="$errors->get('prizes')" class="mb-4" />

                        <div class="space-y-4">
                            <template x-for="(prize, index) in prizes" :key="index">
                                <div class="grid gap-4 rounded-2xl border border-slate-800 bg-slate-950/70 p-4 md:grid-cols-[1fr_1fr_auto]">
                                    <div>
                                        <label class="mb-2 block text-sm font-medium text-slate-300" x-bind:for="`prize-position-${index}`">Position</label>
                                        <input
                                            x-bind:id="`prize-position-${index}`"
                                            x-bind:name="`prizes[${index}][position]`"
                                            x-model.number="prize.position"
                                            type="number"
                                            min="1"
                                            max="10"
                                            class="block w-full rounded-xl border-slate-700 bg-slate-900 text-slate-100 focus:border-orange-500 focus:ring-orange-500"
                                            required
                                        >
                                    </div>

                                    <div>
                                        <label class="mb-2 block text-sm font-medium text-slate-300" x-bind:for="`prize-amount-${index}`">Prize Amount</label>
                                        <input
                                            x-bind:id="`prize-amount-${index}`"
                                            x-bind:name="`prizes[${index}][prize_amount]`"
                                            x-model.number="prize.prize_amount"
                                            @input="recalculateTotalPrize()"
                                            type="number"
                                            min="0"
                                            step="0.01"
                                            class="block w-full rounded-xl border-slate-700 bg-slate-900 text-slate-100 focus:border-orange-500 focus:ring-orange-500"
                                            required
                                        >
                                    </div>

                                    <div class="flex items-end">
                                        <button
                                            type="button"
                                            @click="removePrize(index)"
                                            :disabled="Number(prize.position) <= 3"
                                            class="w-full rounded-xl border px-4 py-3 text-sm font-semibold transition"
                                            :class="Number(prize.position) <= 3
                                                ? 'cursor-not-allowed border-slate-700 bg-slate-800 text-slate-500'
                                                : 'border-rose-500/30 bg-rose-500/10 text-rose-300 hover:bg-rose-500/20'"
                                        >
                                            <span x-text="Number(prize.position) <= 3 ? 'Locked' : 'Remove'"></span>
                                        </button>
                                    </div>
                                </div>
                            </template>
                        </div>

                        @if ($errors->has('prizes.*.position') || $errors->has('prizes.*.prize_amount'))
                            <div class="mt-4 rounded-2xl border border-rose-500/30 bg-rose-500/10 px-4 py-3 text-sm text-rose-200">
                                @foreach ($errors->get('prizes.*.position') as $messages)
                                    @foreach ($messages as $message)
                                        <p>{{ $message }}</p>
                                    @endforeach
                                @endforeach

                                @foreach ($errors->get('prizes.*.prize_amount') as $messages)
                                    @foreach ($messages as $message)
                                        <p>{{ $message }}</p>
                                    @endforeach
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <div class="rounded-3xl border border-slate-800 bg-slate-900/80 p-5">
                        <input type="hidden" name="kill_prize_enabled" :value="killPrizeEnabled ? 1 : 0">

                        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                            <div>
                                <p class="text-lg font-semibold text-white">Kill Point Prize</p>
                                <p class="mt-1 text-sm text-slate-400" x-show="killPrizeEnabled">Each kill earns prize money paid to squad leader.</p>
                                <p class="mt-1 text-sm text-slate-500" x-show="!killPrizeEnabled">Turn this on only when the room should reward every kill with cash.</p>
                            </div>

                            <button
                                type="button"
                                @click="toggleKillPrize()"
                                class="inline-flex items-center gap-3 self-start rounded-full border px-4 py-2 text-sm font-semibold transition md:self-center"
                                :class="killPrizeEnabled ? 'border-orange-400/30 bg-orange-500/15 text-orange-200' : 'border-slate-700 bg-slate-800 text-slate-300'"
                            >
                                <span>Kill Point Prize</span>
                                <span class="relative inline-flex h-7 w-12 items-center rounded-full transition" :class="killPrizeEnabled ? 'bg-orange-500' : 'bg-slate-700'">
                                    <span class="inline-block h-5 w-5 transform rounded-full bg-white transition" :class="killPrizeEnabled ? 'translate-x-6' : 'translate-x-1'"></span>
                                </span>
                            </button>
                        </div>

                        <template x-if="!killPrizeEnabled">
                            <input type="hidden" name="kill_prize_per_kill" value="0">
                        </template>

                        <div x-show="killPrizeEnabled" class="mt-5">
                            <label for="kill_prize_per_kill" class="mb-2 block text-sm font-medium text-slate-300">BDT per kill</label>
                            <input
                                id="kill_prize_per_kill"
                                name="kill_prize_per_kill"
                                type="number"
                                min="1"
                                step="0.01"
                                x-model.number="killPrizePerKill"
                                class="block w-full rounded-xl border-slate-700 bg-slate-900 text-slate-100 focus:border-orange-500 focus:ring-orange-500 md:w-80"
                            >
                            <x-input-error :messages="$errors->get('kill_prize_per_kill')" class="mt-2" />
                        </div>

                        <x-input-error :messages="$errors->get('kill_prize_enabled')" class="mt-3" />
                    </div>

                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('admin.rooms.index') }}" class="rounded-xl border border-slate-700 px-5 py-3 text-sm font-semibold text-slate-300 transition hover:bg-slate-800 hover:text-white">
                            Cancel
                        </a>
                        <button type="submit" class="rounded-xl bg-orange-500 px-5 py-3 text-sm font-semibold text-slate-950 transition hover:bg-orange-400">
                            Update Room
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
