<x-player-layout title="Deposit Balance">
    <section class="mx-auto max-w-6xl px-6 py-14">
        <div class="grid gap-8 xl:grid-cols-[0.9fr_1.1fr]">
            <div class="space-y-6">
                <div class="rounded-[2rem] border border-slate-800 bg-slate-900/80 p-6 shadow-xl shadow-slate-950/20">
                    <p class="text-sm font-semibold uppercase tracking-[0.24em] text-orange-400">Send Money To</p>
                    <h1 class="mt-2 text-3xl font-black text-white">Payment Numbers</h1>
                    <p class="mt-3 text-sm text-slate-300">Use the number below for your preferred payment method, then submit the transaction details for moderator verification.</p>

                    <div class="mt-6 space-y-4">
                        @foreach (['bkash' => 'bKash', 'nagad' => 'Nagad', 'rocket' => 'Rocket'] as $key => $label)
                            <div class="rounded-2xl border border-slate-800 bg-slate-950/70 px-5 py-4">
                                <p class="text-xs uppercase tracking-[0.2em] text-slate-500">{{ $label }}</p>
                                <p class="mt-2 text-lg font-bold text-white">{{ $paymentNumbers[$key] !== '' ? $paymentNumbers[$key] : 'Not configured yet' }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="rounded-[2rem] border border-orange-500/20 bg-orange-500/10 p-6 shadow-xl shadow-orange-950/10">
                    <p class="text-sm font-semibold text-orange-200">Your balance will be added after moderator verification.</p>
                </div>
            </div>

            <div class="rounded-[2rem] border border-slate-800 bg-slate-900/80 p-8 shadow-2xl shadow-slate-950/20">
                <p class="text-sm font-semibold uppercase tracking-[0.24em] text-orange-400">Deposit Request</p>
                <h2 class="mt-2 text-3xl font-black text-white">Submit payment proof</h2>

                <form method="POST" action="{{ route('wallet.deposit.store') }}" class="mt-8 space-y-6">
                    @csrf

                    <div>
                        <label for="method" class="mb-2 block text-sm font-semibold text-slate-300">Method</label>
                        <select id="method" name="method" class="block w-full rounded-2xl border-slate-700 bg-slate-950 text-slate-100 focus:border-orange-500 focus:ring-orange-500" required>
                            <option value="">Select method</option>
                            @foreach (['bkash' => 'bKash', 'nagad' => 'Nagad', 'rocket' => 'Rocket'] as $value => $label)
                                <option value="{{ $value }}" @selected(old('method') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('method')
                            <p class="mt-2 text-sm font-medium text-rose-300">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="sender_number" class="mb-2 block text-sm font-semibold text-slate-300">Sender Number</label>
                        <input id="sender_number" name="sender_number" type="text" value="{{ old('sender_number') }}" class="block w-full rounded-2xl border-slate-700 bg-slate-950 text-slate-100 focus:border-orange-500 focus:ring-orange-500" required>
                        @error('sender_number')
                            <p class="mt-2 text-sm font-medium text-rose-300">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="transaction_id" class="mb-2 block text-sm font-semibold text-slate-300">Transaction ID</label>
                        <input id="transaction_id" name="transaction_id" type="text" value="{{ old('transaction_id') }}" class="block w-full rounded-2xl border-slate-700 bg-slate-950 text-slate-100 focus:border-orange-500 focus:ring-orange-500" required>
                        @error('transaction_id')
                            <p class="mt-2 text-sm font-medium text-rose-300">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="amount" class="mb-2 block text-sm font-semibold text-slate-300">Amount</label>
                        <input id="amount" name="amount" type="number" min="50" step="0.01" value="{{ old('amount') }}" class="block w-full rounded-2xl border-slate-700 bg-slate-950 text-slate-100 focus:border-orange-500 focus:ring-orange-500" required>
                        @error('amount')
                            <p class="mt-2 text-sm font-medium text-rose-300">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex flex-wrap items-center justify-end gap-4">
                        <a href="{{ route('wallet.index') }}" class="rounded-2xl border border-slate-700 px-6 py-3 text-sm font-bold text-slate-200 transition hover:bg-slate-800 hover:text-white">
                            Back To Wallet
                        </a>
                        <button type="submit" class="rounded-2xl bg-orange-500 px-6 py-3 text-sm font-bold text-slate-950 transition hover:bg-orange-400">
                            Submit Deposit Request
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>
</x-player-layout>
