@php
    $initialPrizes = collect(old('prizes', $prizes ?? []))
        ->map(fn ($prize) => [
            'position' => (int) ($prize['position'] ?? 1),
            'prize_amount' => (float) ($prize['prize_amount'] ?? 0),
        ])
        ->values()
        ->all();
@endphp

<div
    x-data="{
        prizes: @js($initialPrizes),
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
        },
        removePrize(index) {
            if (this.prizes.length <= 3) return;

            this.prizes.splice(index, 1);
        }
    }"
    class="space-y-8"
>
    <div class="grid gap-6 lg:grid-cols-2">
        <div class="space-y-2">
            <x-input-label for="name" :value="__('Category Name')" />
            <x-text-input id="name" name="name" type="text" class="block w-full border-slate-700 bg-slate-900 text-slate-100" :value="old('name', $category->name)" required />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div class="space-y-2">
            <x-input-label for="squad_type" :value="__('Squad Type')" />
            <select id="squad_type" name="squad_type" class="block w-full rounded-xl border-slate-700 bg-slate-900 text-slate-100 focus:border-orange-500 focus:ring-orange-500" required>
                @foreach (['squad' => 'Squad', 'duo' => 'Duo', 'solo' => 'Solo'] as $value => $label)
                    <option value="{{ $value }}" @selected(old('squad_type', $category->squad_type) === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('squad_type')" class="mt-2" />
        </div>

        <div class="space-y-2">
            <x-input-label for="map" :value="__('Map')" />
            <x-text-input id="map" name="map" type="text" class="block w-full border-slate-700 bg-slate-900 text-slate-100" :value="old('map', $category->map)" required />
            <x-input-error :messages="$errors->get('map')" class="mt-2" />
        </div>

        <div class="space-y-2">
            <x-input-label for="entry_fee" :value="__('Entry Fee')" />
            <x-text-input id="entry_fee" name="entry_fee" type="number" step="0.01" min="0" class="block w-full border-slate-700 bg-slate-900 text-slate-100" :value="old('entry_fee', $category->entry_fee)" required />
            <x-input-error :messages="$errors->get('entry_fee')" class="mt-2" />
        </div>

        <div class="space-y-2">
            <x-input-label for="kill_point" :value="__('Kill Point')" />
            <x-text-input id="kill_point" name="kill_point" type="number" step="0.01" min="0" class="block w-full border-slate-700 bg-slate-900 text-slate-100" :value="old('kill_point', $category->kill_point)" required />
            <x-input-error :messages="$errors->get('kill_point')" class="mt-2" />
        </div>

        <div class="space-y-2">
            <x-input-label for="match_duration" :value="__('Match Duration (Minutes)')" />
            <x-text-input id="match_duration" name="match_duration" type="number" min="1" class="block w-full border-slate-700 bg-slate-900 text-slate-100" :value="old('match_duration', $category->match_duration)" required />
            <x-input-error :messages="$errors->get('match_duration')" class="mt-2" />
        </div>
    </div>

    <div class="space-y-2">
        <x-input-label for="rules" :value="__('Rules')" />
        <textarea id="rules" name="rules" rows="5" class="block w-full rounded-2xl border-slate-700 bg-slate-900 text-slate-100 focus:border-orange-500 focus:ring-orange-500" required>{{ old('rules', $category->rules) }}</textarea>
        <x-input-error :messages="$errors->get('rules')" class="mt-2" />
    </div>

    <div class="rounded-3xl border border-slate-800 bg-slate-900/80 p-5">
        <div class="mb-4 flex items-center justify-between gap-4">
            <div>
                <h3 class="text-lg font-semibold text-white">Prize Structure</h3>
                <p class="text-sm text-slate-400">Set at least 1st, 2nd, and 3rd prizes. You can add up to 10 positions.</p>
            </div>

            <button type="button" @click="addPrize()" class="rounded-xl bg-orange-500 px-4 py-2 text-sm font-semibold text-slate-950 transition hover:bg-orange-400">
                Add Prize Row
            </button>
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
        <a href="{{ route('admin.categories.index') }}" class="rounded-xl border border-slate-700 px-5 py-3 text-sm font-semibold text-slate-300 transition hover:bg-slate-800 hover:text-white">
            Cancel
        </a>
        <button type="submit" class="rounded-xl bg-orange-500 px-5 py-3 text-sm font-semibold text-slate-950 transition hover:bg-orange-400">
            {{ $submitLabel }}
        </button>
    </div>
</div>
