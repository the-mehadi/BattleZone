<x-admin-layout title="Settings">
    <div class="mx-auto max-w-3xl">
        <div class="rounded-3xl border border-slate-800 bg-slate-900/80 p-6 shadow-2xl shadow-slate-950/30">
            <div>
                <h2 class="text-xl font-semibold text-white">Payment Settings</h2>
                <p class="mt-1 text-sm text-slate-400">Update the payment numbers shown to players on the deposit page.</p>
            </div>

            <form method="POST" action="{{ route('admin.settings.update') }}" class="mt-8 space-y-6">
                @csrf

                <div>
                    <label for="bkash_number" class="block text-sm font-semibold text-slate-200">bKash Number</label>
                    <input
                        id="bkash_number"
                        type="text"
                        name="bkash_number"
                        value="{{ old('bkash_number', $bkashNumber) }}"
                        class="mt-2 block w-full rounded-xl border-slate-700 bg-slate-950 text-slate-100 placeholder:text-slate-500 focus:border-orange-500 focus:ring-orange-500"
                        placeholder="Enter bKash number"
                    >
                    @error('bkash_number')
                        <p class="mt-2 text-sm text-rose-300">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="nagad_number" class="block text-sm font-semibold text-slate-200">Nagad Number</label>
                    <input
                        id="nagad_number"
                        type="text"
                        name="nagad_number"
                        value="{{ old('nagad_number', $nagadNumber) }}"
                        class="mt-2 block w-full rounded-xl border-slate-700 bg-slate-950 text-slate-100 placeholder:text-slate-500 focus:border-orange-500 focus:ring-orange-500"
                        placeholder="Enter Nagad number"
                    >
                    @error('nagad_number')
                        <p class="mt-2 text-sm text-rose-300">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="rocket_number" class="block text-sm font-semibold text-slate-200">Rocket Number</label>
                    <input
                        id="rocket_number"
                        type="text"
                        name="rocket_number"
                        value="{{ old('rocket_number', $rocketNumber) }}"
                        class="mt-2 block w-full rounded-xl border-slate-700 bg-slate-950 text-slate-100 placeholder:text-slate-500 focus:border-orange-500 focus:ring-orange-500"
                        placeholder="Enter Rocket number"
                    >
                    @error('rocket_number')
                        <p class="mt-2 text-sm text-rose-300">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="rounded-2xl bg-orange-500 px-6 py-3 text-sm font-semibold text-slate-950 transition hover:bg-orange-400">
                        Save Settings
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
