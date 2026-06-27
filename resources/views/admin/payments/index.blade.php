<x-admin-layout title="Payments">
    <div class="space-y-6">
        <div class="flex flex-col gap-4 rounded-3xl border border-slate-800 bg-slate-900/70 p-6 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <h2 class="text-xl font-semibold text-white">Deposit Requests</h2>
                <p class="mt-1 text-sm text-slate-400">Review player deposit submissions and approve or reject them after verification.</p>
            </div>

            <form method="GET" action="{{ route('admin.payments.index') }}" class="grid gap-3 sm:grid-cols-[220px_auto_auto]">
                <select name="status" class="rounded-xl border-slate-700 bg-slate-950 text-slate-100 focus:border-orange-500 focus:ring-orange-500">
                    <option value="">All statuses</option>
                    @foreach (['pending', 'approved', 'rejected'] as $value)
                        <option value="{{ $value }}" @selected($status === $value)>{{ ucfirst($value) }}</option>
                    @endforeach
                </select>

                <button type="submit" class="rounded-xl bg-orange-500 px-4 py-3 text-sm font-semibold text-slate-950 transition hover:bg-orange-400">
                    Apply
                </button>

                <a href="{{ route('admin.payments.index') }}" class="rounded-xl border border-slate-700 px-4 py-3 text-center text-sm font-semibold text-slate-300 transition hover:bg-slate-800 hover:text-white">
                    Reset
                </a>
            </form>
        </div>

        <div class="overflow-hidden rounded-3xl border border-slate-800 bg-slate-900/80 shadow-2xl shadow-slate-950/30">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-800">
                    <thead class="bg-slate-950/60">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Player</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Method</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Transaction ID</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Amount</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Status</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
                        @forelse ($payments as $payment)
                            <tr class="align-top hover:bg-slate-800/30">
                                <td class="px-6 py-5">
                                    <div class="font-semibold text-white">{{ $payment->user->name }}</div>
                                    <div class="mt-1 text-sm text-slate-400">{{ $payment->user->phone }}</div>
                                    <div class="mt-1 text-xs text-slate-500">{{ $payment->created_at->format('d M Y, h:i A') }}</div>
                                </td>
                                <td class="px-6 py-5 text-sm uppercase text-slate-300">{{ $payment->method }}</td>
                                <td class="px-6 py-5 text-sm font-mono text-slate-300">{{ $payment->transaction_id }}</td>
                                <td class="px-6 py-5 text-sm font-semibold text-white">৳ {{ number_format((float) $payment->amount, 2) }}</td>
                                <td class="px-6 py-5">
                                    <span class="{{ match ($payment->status) { 'approved' => 'border-emerald-500/30 bg-emerald-500/10 text-emerald-300', 'rejected' => 'border-rose-500/30 bg-rose-500/10 text-rose-300', default => 'border-amber-500/30 bg-amber-500/10 text-amber-300' } }} inline-flex rounded-full border px-3 py-1 text-xs font-semibold uppercase tracking-[0.16em]">
                                        {{ $payment->status }}
                                    </span>
                                    @if ($payment->approvedBy)
                                        <p class="mt-2 text-xs text-slate-500">
                                            By {{ $payment->approvedBy->name }}{{ $payment->approved_at ? ' at '.$payment->approved_at->format('d M Y, h:i A') : '' }}
                                        </p>
                                    @endif
                                </td>
                                <td class="px-6 py-5">
                                    <div class="flex flex-col items-end gap-3">
                                        @if ($payment->status === 'pending')
                                            <form method="POST" action="{{ route('admin.payments.approve', $payment) }}">
                                                @csrf
                                                <button type="submit" class="rounded-xl border border-emerald-500/30 bg-emerald-500/10 px-4 py-2 text-sm font-semibold text-emerald-300 transition hover:bg-emerald-500/20">
                                                    Approve
                                                </button>
                                            </form>

                                            <form method="POST" action="{{ route('admin.payments.reject', $payment) }}" class="w-full max-w-xs">
                                                @csrf
                                                <textarea name="note" rows="2" class="block w-full rounded-xl border-slate-700 bg-slate-950 text-sm text-slate-100 focus:border-orange-500 focus:ring-orange-500" placeholder="Reject note (optional)">{{ old('note') }}</textarea>
                                                <button type="submit" class="mt-2 w-full rounded-xl border border-rose-500/30 bg-rose-500/10 px-4 py-2 text-sm font-semibold text-rose-300 transition hover:bg-rose-500/20">
                                                    Reject
                                                </button>
                                            </form>
                                        @else
                                            <div class="max-w-xs rounded-2xl border border-slate-800 bg-slate-950/70 px-4 py-3 text-left text-sm text-slate-400">
                                                {{ $payment->note ?: 'No moderator note added.' }}
                                            </div>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <p class="text-lg font-medium text-slate-200">No payment requests found.</p>
                                    <p class="mt-2 text-sm text-slate-400">Player deposit requests will appear here for moderator review.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($payments->hasPages())
                <div class="border-t border-slate-800 px-6 py-4">
                    {{ $payments->links() }}
                </div>
            @endif
        </div>
    </div>
</x-admin-layout>
