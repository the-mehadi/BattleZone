<x-admin-layout title="Rooms">
    <div class="space-y-6">
        <div class="flex flex-col gap-4 rounded-3xl border border-slate-800 bg-slate-900/70 p-6 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h2 class="text-xl font-semibold text-white">Room Management</h2>
                <p class="mt-1 text-sm text-slate-400">Schedule tournament rooms, manage visibility, and monitor room states.</p>
            </div>

            <a href="{{ route('admin.rooms.create') }}" class="inline-flex items-center justify-center rounded-xl bg-orange-500 px-5 py-3 text-sm font-semibold text-slate-950 transition hover:bg-orange-400">
                Create Room
            </a>
        </div>

        <div class="rounded-3xl border border-slate-800 bg-slate-900/80 p-6">
            <form method="GET" action="{{ route('admin.rooms.index') }}" class="grid gap-4 md:grid-cols-[1fr_auto_auto]">
                <div>
                    <label for="status" class="mb-2 block text-sm font-medium text-slate-300">Status Filter</label>
                    <select id="status" name="status" class="block w-full rounded-xl border-slate-700 bg-slate-950 text-slate-100 focus:border-orange-500 focus:ring-orange-500">
                        <option value="">All statuses</option>
                        @foreach (['upcoming', 'live', 'finished', 'cancelled'] as $value)
                            <option value="{{ $value }}" @selected($status === $value)>{{ ucfirst($value) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-end">
                    <button type="submit" class="w-full rounded-xl bg-orange-500 px-4 py-3 text-sm font-semibold text-slate-950 transition hover:bg-orange-400">
                        Apply Filter
                    </button>
                </div>

                <div class="flex items-end">
                    <a href="{{ route('admin.rooms.index') }}" class="w-full rounded-xl border border-slate-700 px-4 py-3 text-center text-sm font-semibold text-slate-300 transition hover:bg-slate-800 hover:text-white">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <div class="overflow-hidden rounded-3xl border border-slate-800 bg-slate-900/80 shadow-2xl shadow-slate-950/30">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-800">
                    <thead class="bg-slate-950/60">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Title</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Category</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Match Time</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Status</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Lock</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
                        @forelse ($rooms as $room)
                            <tr class="hover:bg-slate-800/30">
                                <td class="px-6 py-5">
                                    <div class="font-semibold text-white">{{ $room->title }}</div>
                                    <div class="mt-1 text-sm text-slate-400">{{ $room->map }}</div>
                                </td>
                                <td class="px-6 py-5 text-sm text-slate-300">{{ $room->category->name }}</td>
                                <td class="px-6 py-5 text-sm text-slate-300">{{ $room->match_time->format('d M Y, h:i A') }}</td>
                                <td class="px-6 py-5">
                                    <span class="{{ match ($room->status) { 'upcoming' => 'border-sky-500/30 bg-sky-500/10 text-sky-300', 'live' => 'border-emerald-500/30 bg-emerald-500/10 text-emerald-300', 'finished' => 'border-violet-500/30 bg-violet-500/10 text-violet-300', default => 'border-rose-500/30 bg-rose-500/10 text-rose-300' } }} inline-flex rounded-full border px-3 py-1 text-xs font-semibold uppercase tracking-wide">
                                        {{ $room->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-5">
                                    <span class="{{ $room->is_room_locked ? 'border-amber-500/30 bg-amber-500/10 text-amber-300' : 'border-emerald-500/30 bg-emerald-500/10 text-emerald-300' }} inline-flex rounded-full border px-3 py-1 text-xs font-semibold uppercase tracking-wide">
                                        {{ $room->is_room_locked ? 'Locked' : 'Unlocked' }}
                                    </span>
                                </td>
                                <td class="px-6 py-5">
                                    <div class="flex flex-wrap justify-end gap-2">
                                        <a href="{{ route('admin.rooms.show', $room) }}" class="rounded-xl border border-slate-700 px-4 py-2 text-sm font-medium text-slate-300 transition hover:bg-slate-800 hover:text-white">
                                            View
                                        </a>
                                        <a href="{{ route('admin.rooms.edit', $room) }}" class="rounded-xl border border-slate-700 px-4 py-2 text-sm font-medium text-slate-300 transition hover:bg-slate-800 hover:text-white">
                                            Edit
                                        </a>
                                        @if ($room->status === 'upcoming')
                                            <form method="POST" action="{{ route('admin.rooms.destroy', $room) }}" onsubmit="return confirm('Delete this room?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="rounded-xl border border-rose-500/30 bg-rose-500/10 px-4 py-2 text-sm font-medium text-rose-300 transition hover:bg-rose-500/20">
                                                    Delete
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <p class="text-lg font-medium text-slate-200">No rooms found.</p>
                                    <p class="mt-2 text-sm text-slate-400">Create your first tournament room to start managing BattleZone matches.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($rooms->hasPages())
                <div class="border-t border-slate-800 px-6 py-4">
                    {{ $rooms->links() }}
                </div>
            @endif
        </div>
    </div>
</x-admin-layout>
