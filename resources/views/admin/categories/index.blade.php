<x-admin-layout title="Categories">
    <div class="space-y-6">
        <div class="flex flex-col gap-4 rounded-3xl border border-slate-800 bg-slate-900/70 p-6 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-xl font-semibold text-white">Category Management</h2>
                <p class="mt-1 text-sm text-slate-400">Manage tournament modes, fees, rules, and default prize structures.</p>
            </div>

            <a href="{{ route('admin.categories.create') }}" class="inline-flex items-center justify-center rounded-xl bg-orange-500 px-5 py-3 text-sm font-semibold text-slate-950 transition hover:bg-orange-400">
                Create Category
            </a>
        </div>

        <div class="overflow-hidden rounded-3xl border border-slate-800 bg-slate-900/80 shadow-2xl shadow-slate-950/30">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-800">
                    <thead class="bg-slate-950/60">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Name</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Squad Type</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Entry Fee</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Prizes</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Status</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
                        @forelse ($categories as $category)
                            <tr class="hover:bg-slate-800/30">
                                <td class="px-6 py-5">
                                    <div class="font-semibold text-white">{{ $category->name }}</div>
                                    <div class="mt-1 text-sm text-slate-400">{{ $category->map }} • {{ $category->match_duration }} min</div>
                                </td>
                                <td class="px-6 py-5 text-sm capitalize text-slate-300">{{ $category->squad_type }}</td>
                                <td class="px-6 py-5 text-sm text-slate-300">৳ {{ number_format((float) $category->entry_fee, 2) }}</td>
                                <td class="px-6 py-5 text-sm text-slate-300">{{ $category->category_prizes_count }}</td>
                                <td class="px-6 py-5">
                                    <span class="{{ $category->status === 'active' ? 'border-emerald-500/30 bg-emerald-500/10 text-emerald-300' : 'border-slate-700 bg-slate-800 text-slate-300' }} inline-flex rounded-full border px-3 py-1 text-xs font-semibold uppercase tracking-wide">
                                        {{ $category->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-5">
                                    <div class="flex flex-wrap justify-end gap-2">
                                        <a href="{{ route('admin.categories.edit', $category) }}" class="rounded-xl border border-slate-700 px-4 py-2 text-sm font-medium text-slate-300 transition hover:bg-slate-800 hover:text-white">
                                            Edit
                                        </a>

                                        <form method="POST" action="{{ route('admin.categories.toggle-status', $category) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="rounded-xl border border-orange-500/30 bg-orange-500/10 px-4 py-2 text-sm font-medium text-orange-300 transition hover:bg-orange-500/20">
                                                {{ $category->status === 'active' ? 'Deactivate' : 'Activate' }}
                                            </button>
                                        </form>

                                        <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" onsubmit="return confirm('Delete this category?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="rounded-xl border border-rose-500/30 bg-rose-500/10 px-4 py-2 text-sm font-medium text-rose-300 transition hover:bg-rose-500/20">
                                                Delete
                                            </button>
                                        </form>
                                    </div>

                                    @if ($category->rooms_count > 0)
                                        <p class="mt-2 text-right text-xs text-amber-300">
                                            {{ $category->rooms_count }} room(s) linked. Delete is blocked.
                                        </p>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <p class="text-lg font-medium text-slate-200">No categories found.</p>
                                    <p class="mt-2 text-sm text-slate-400">Create your first Free Fire tournament category to get started.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-admin-layout>
