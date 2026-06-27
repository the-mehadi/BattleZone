<x-admin-layout title="Create Category">
    <div class="mx-auto max-w-5xl">
        <div class="rounded-3xl border border-slate-800 bg-slate-900/80 p-6 shadow-2xl shadow-slate-950/30 md:p-8">
            <div class="mb-8">
                <h2 class="text-2xl font-semibold text-white">Create Tournament Category</h2>
                <p class="mt-2 text-sm text-slate-400">Configure the mode, fee, rules, and reward structure for a new BattleZone category.</p>
            </div>

            <form method="POST" action="{{ route('admin.categories.store') }}">
                @csrf

                @include('admin.categories._form', [
                    'category' => $category,
                    'prizes' => $prizes,
                    'submitLabel' => 'Create Category',
                ])
            </form>
        </div>
    </div>
</x-admin-layout>
