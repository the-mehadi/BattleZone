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
                : [{ position: 1, prize_amount: 0 }];
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
            if (this.prizes.length <= 1) return;

            this.prizes.splice(index, 1);
            this.recalculateTotalPrize();
        },
        recalculateTotalPrize() {
            this.form.total_prize = this.prizes.reduce((carry, prize) => {
                return carry + (Number(prize.prize_amount) || 0);
            }, 0);
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
                        <button type="button" @click="removePrize(index)" class="w-full rounded-xl border border-rose-500/30 bg-rose-500/10 px-4 py-3 text-sm font-semibold text-rose-300 transition hover:bg-rose-500/20">
                            Remove
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

    <div class="flex items-center justify-end gap-3">
        <a href="{{ route('admin.rooms.index') }}" class="rounded-xl border border-slate-700 px-5 py-3 text-sm font-semibold text-slate-300 transition hover:bg-slate-800 hover:text-white">
            Cancel
        </a>
        <button type="submit" class="rounded-xl bg-orange-500 px-5 py-3 text-sm font-semibold text-slate-950 transition hover:bg-orange-400">
            {{ $submitLabel }}
        </button>
    </div>
</div>
