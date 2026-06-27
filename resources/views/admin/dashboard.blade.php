<x-admin-layout title="Dashboard">
    <div class="space-y-6">
        <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-3xl border border-slate-800 bg-slate-900/80 p-6 shadow-2xl shadow-slate-950/30">
                <p class="text-sm font-semibold uppercase tracking-[0.2em] text-orange-400">Players</p>
                <p class="mt-4 text-4xl font-black text-white">{{ number_format($totalPlayers) }}</p>
                <p class="mt-2 text-sm text-slate-400">Registered player accounts in BattleZone.</p>
            </div>

            <div class="rounded-3xl border border-slate-800 bg-slate-900/80 p-6 shadow-2xl shadow-slate-950/30">
                <p class="text-sm font-semibold uppercase tracking-[0.2em] text-orange-400">Rooms</p>
                <p class="mt-4 text-4xl font-black text-white">{{ number_format($totalRooms) }}</p>
                <div class="mt-4 grid grid-cols-3 gap-3 text-xs uppercase tracking-[0.16em] text-slate-400">
                    <div class="rounded-2xl border border-slate-800 bg-slate-950/60 px-3 py-3 text-center">
                        <p>Upcoming</p>
                        <p class="mt-2 text-lg font-bold text-white">{{ $roomStatusCounts['upcoming'] }}</p>
                    </div>
                    <div class="rounded-2xl border border-slate-800 bg-slate-950/60 px-3 py-3 text-center">
                        <p>Live</p>
                        <p class="mt-2 text-lg font-bold text-white">{{ $roomStatusCounts['live'] }}</p>
                    </div>
                    <div class="rounded-2xl border border-slate-800 bg-slate-950/60 px-3 py-3 text-center">
                        <p>Finished</p>
                        <p class="mt-2 text-lg font-bold text-white">{{ $roomStatusCounts['finished'] }}</p>
                    </div>
                </div>
            </div>

            <div class="rounded-3xl border border-slate-800 bg-slate-900/80 p-6 shadow-2xl shadow-slate-950/30">
                <p class="text-sm font-semibold uppercase tracking-[0.2em] text-orange-400">Pending Payments</p>
                <p class="mt-4 text-4xl font-black text-white">{{ number_format($pendingPaymentsCount) }}</p>
                <p class="mt-2 text-sm text-slate-400">Awaiting approval worth <span class="font-semibold text-white">৳ {{ number_format($pendingPaymentsAmount, 2) }}</span>.</p>
            </div>

            <div class="rounded-3xl border border-slate-800 bg-slate-900/80 p-6 shadow-2xl shadow-slate-950/30">
                <p class="text-sm font-semibold uppercase tracking-[0.2em] text-orange-400">Prize Distributed</p>
                <p class="mt-4 text-4xl font-black text-white">৳ {{ number_format($totalPrizeDistributed, 2) }}</p>
                <p class="mt-2 text-sm text-slate-400">Total leaderboard and room prizes credited to players.</p>
            </div>
        </div>

        <div class="grid gap-6 xl:grid-cols-[1.2fr_0.8fr]">
            <div class="rounded-3xl border border-slate-800 bg-slate-900/80 p-6 shadow-2xl shadow-slate-950/30">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <h2 class="text-xl font-semibold text-white">Quick Actions</h2>
                        <p class="mt-1 text-sm text-slate-400">Review the latest pending deposit requests and approve them quickly.</p>
                    </div>

                    <a href="{{ route('admin.payments.index') }}" class="rounded-2xl border border-slate-700 px-4 py-3 text-sm font-semibold text-slate-300 transition hover:bg-slate-800 hover:text-white">
                        View All Payments
                    </a>
                </div>

                <div class="mt-6 space-y-4">
                    @forelse ($recentPendingPayments as $payment)
                        <div class="flex flex-col gap-4 rounded-3xl border border-slate-800 bg-slate-950/60 p-5 lg:flex-row lg:items-center lg:justify-between">
                            <div>
                                <p class="text-lg font-semibold text-white">{{ $payment->user->name }}</p>
                                <p class="mt-1 text-sm text-slate-400">{{ $payment->user->phone }} • {{ strtoupper($payment->method) }}</p>
                                <p class="mt-2 text-sm text-slate-300">
                                    Txn: <span class="font-mono">{{ $payment->transaction_id }}</span> • Amount: <span class="font-semibold text-white">৳ {{ number_format((float) $payment->amount, 2) }}</span>
                                </p>
                            </div>

                            <form method="POST" action="{{ route('admin.payments.approve', $payment) }}">
                                @csrf
                                <button type="submit" class="rounded-2xl bg-emerald-500/90 px-5 py-3 text-sm font-semibold text-slate-950 transition hover:bg-emerald-400">
                                    Approve Payment
                                </button>
                            </form>
                        </div>
                    @empty
                        <div class="rounded-3xl border border-slate-800 bg-slate-950/60 px-6 py-10 text-center">
                            <p class="text-lg font-semibold text-white">No pending payments right now.</p>
                            <p class="mt-2 text-sm text-slate-400">All deposit requests have been reviewed.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="rounded-3xl border border-slate-800 bg-slate-900/80 p-6 shadow-2xl shadow-slate-950/30">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <h2 class="text-xl font-semibold text-white">Upcoming Rooms</h2>
                        <p class="mt-1 text-sm text-slate-400">The next 3 scheduled rooms that are still open.</p>
                    </div>

                    <a href="{{ route('admin.rooms.index') }}" class="text-sm font-semibold text-orange-300 transition hover:text-orange-200">
                        Manage Rooms
                    </a>
                </div>

                <div class="mt-6 overflow-hidden rounded-3xl border border-slate-800">
                    <table class="min-w-full divide-y divide-slate-800">
                        <thead class="bg-slate-950/70">
                            <tr>
                                <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Room</th>
                                <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Category</th>
                                <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Match Time</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800 bg-slate-900/60">
                            @forelse ($upcomingRooms as $room)
                                <tr class="hover:bg-slate-800/30">
                                    <td class="px-5 py-4 text-sm font-semibold text-white">{{ $room->title }}</td>
                                    <td class="px-5 py-4 text-sm text-slate-300">{{ $room->category?->name ?? 'N/A' }}</td>
                                    <td class="px-5 py-4 text-sm text-slate-300">{{ $room->match_time?->format('d M Y, h:i A') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-5 py-10 text-center">
                                        <p class="text-base font-semibold text-white">No upcoming rooms scheduled.</p>
                                        <p class="mt-2 text-sm text-slate-400">Create a room to populate this list.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
