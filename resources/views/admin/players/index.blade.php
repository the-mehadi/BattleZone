<x-admin-layout title="Players">
    <div class="space-y-6">
        <div class="flex flex-col gap-4 rounded-3xl border border-slate-800 bg-slate-900/80 p-6 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <h2 class="text-xl font-semibold text-white">Player Management</h2>
                <p class="mt-1 text-sm text-slate-400">Search player accounts and toggle ban status when moderation is needed.</p>
            </div>

            <form method="GET" action="{{ route('admin.players.index') }}" class="grid gap-3 sm:grid-cols-[280px_auto_auto]">
                <input
                    type="text"
                    name="search"
                    value="{{ $search }}"
                    placeholder="Search by name or phone"
                    class="rounded-xl border-slate-700 bg-slate-950 text-slate-100 placeholder:text-slate-500 focus:border-orange-500 focus:ring-orange-500"
                >

                <button type="submit" class="rounded-xl bg-orange-500 px-4 py-3 text-sm font-semibold text-slate-950 transition hover:bg-orange-400">
                    Search
                </button>

                <a href="{{ route('admin.players.index') }}" class="rounded-xl border border-slate-700 px-4 py-3 text-center text-sm font-semibold text-slate-300 transition hover:bg-slate-800 hover:text-white">
                    Reset
                </a>
            </form>
        </div>

        <div class="overflow-hidden rounded-3xl border border-slate-800 bg-slate-900/80 shadow-2xl shadow-slate-950/30">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-800">
                    <thead class="bg-slate-950/60">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Name</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Phone</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Wallet Balance</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Status</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800 bg-slate-900/60">
                        @forelse ($players as $player)
                            <tr class="hover:bg-slate-800/30">
                                <td class="px-6 py-5">
                                    <div class="font-semibold text-white">{{ $player->name }}</div>
                                    <div class="mt-1 text-xs text-slate-500">Joined {{ $player->created_at->format('d M Y') }}</div>
                                </td>
                                <td class="px-6 py-5 text-sm text-slate-300">{{ $player->phone }}</td>
                                <td class="px-6 py-5 text-sm font-semibold text-white">৳ {{ number_format((float) $player->wallet_balance, 2) }}</td>
                                <td class="px-6 py-5">
                                    <span class="{{ $player->is_banned ? 'border-rose-500/30 bg-rose-500/10 text-rose-300' : 'border-emerald-500/30 bg-emerald-500/10 text-emerald-300' }} inline-flex rounded-full border px-3 py-1 text-xs font-semibold uppercase tracking-[0.16em]">
                                        {{ $player->is_banned ? 'Banned' : 'Active' }}
                                    </span>
                                </td>
                                <td class="px-6 py-5 text-right">
                                    <form method="POST" action="{{ route('admin.players.ban', $player) }}">
                                        @csrf
                                        <button type="submit" class="{{ $player->is_banned ? 'border-emerald-500/30 bg-emerald-500/10 text-emerald-300 hover:bg-emerald-500/20' : 'border-rose-500/30 bg-rose-500/10 text-rose-300 hover:bg-rose-500/20' }} rounded-xl border px-4 py-2 text-sm font-semibold transition">
                                            {{ $player->is_banned ? 'Unban Player' : 'Ban Player' }}
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <p class="text-lg font-semibold text-white">No players found.</p>
                                    <p class="mt-2 text-sm text-slate-400">Try a different search term or wait for new registrations.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($players->hasPages())
                <div class="border-t border-slate-800 px-6 py-4">
                    {{ $players->links() }}
                </div>
            @endif
        </div>
    </div>
</x-admin-layout>
