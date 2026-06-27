<x-player-layout title="Wallet">
    <section class="mx-auto max-w-7xl px-6 py-14">
        <div class="grid gap-8 xl:grid-cols-[0.8fr_1.2fr]">
            <div class="rounded-[2rem] border border-orange-500/20 bg-gradient-to-br from-orange-500/15 via-slate-900/90 to-slate-900/90 p-8 shadow-2xl shadow-orange-950/20">
                <p class="text-sm font-semibold uppercase tracking-[0.24em] text-orange-400">Wallet Balance</p>
                <p class="mt-5 text-5xl font-black text-white">৳ {{ number_format((float) auth()->user()->wallet_balance, 2) }}</p>
                <p class="mt-3 text-sm text-slate-300">Available balance for room entry fees and upcoming tournament actions.</p>

                <a href="{{ route('wallet.deposit') }}" class="mt-8 inline-flex rounded-2xl bg-orange-500 px-6 py-3 text-sm font-bold text-slate-950 transition hover:bg-orange-400">
                    Deposit Balance
                </a>
            </div>

            <div class="rounded-[2rem] border border-slate-800 bg-slate-900/80 p-6 shadow-xl shadow-slate-950/20">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-[0.2em] text-orange-400">Transactions</p>
                        <h1 class="mt-2 text-3xl font-black text-white">Transaction History</h1>
                    </div>

                    <a href="{{ route('wallet.deposit') }}" class="rounded-2xl border border-slate-700 px-5 py-3 text-sm font-bold text-slate-200 transition hover:bg-slate-800 hover:text-white">
                        New Deposit
                    </a>
                </div>

                <div class="mt-6 overflow-hidden rounded-[1.5rem] border border-slate-800">
                    <table class="min-w-full divide-y divide-slate-800">
                        <thead class="bg-slate-950/70">
                            <tr>
                                <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Type</th>
                                <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Amount</th>
                                <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Description</th>
                                <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800 bg-slate-900/70">
                            @forelse ($transactions as $transaction)
                                @php
                                    $amountClass = in_array($transaction->type, ['deposit', 'prize', 'refund'], true)
                                        ? 'text-emerald-300'
                                        : ($transaction->type === 'entry_fee' ? 'text-rose-300' : 'text-slate-200');
                                @endphp
                                <tr>
                                    <td class="px-5 py-4">
                                        <span class="inline-flex rounded-full border border-slate-700 bg-slate-950 px-3 py-1 text-xs font-semibold uppercase tracking-[0.16em] text-slate-300">
                                            {{ str_replace('_', ' ', $transaction->type) }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-4 text-sm font-bold {{ $amountClass }}">
                                        {{ $transaction->signed_amount }}
                                    </td>
                                    <td class="px-5 py-4 text-sm text-slate-300">{{ $transaction->description }}</td>
                                    <td class="px-5 py-4 text-sm text-slate-400">{{ $transaction->created_at->format('d M Y, h:i A') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-5 py-12 text-center">
                                        <p class="text-lg font-semibold text-white">No transactions yet.</p>
                                        <p class="mt-2 text-sm text-slate-400">Approved deposits and room charges will appear here.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($transactions->hasPages())
                    <div class="mt-6">
                        {{ $transactions->links() }}
                    </div>
                @endif
            </div>
        </div>
    </section>
</x-player-layout>
