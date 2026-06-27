<x-admin-layout title="Create Room">
    <div class="mx-auto max-w-6xl">
        <div class="rounded-3xl border border-slate-800 bg-slate-900/80 p-6 shadow-2xl shadow-slate-950/30 md:p-8">
            <div class="mb-8">
                <h2 class="text-2xl font-semibold text-white">Create Tournament Room</h2>
                <p class="mt-2 text-sm text-slate-400">Select an active category, schedule the match, and prepare the room rewards.</p>
            </div>

            <form method="POST" action="{{ route('admin.rooms.store') }}">
                @csrf

                @include('admin.rooms._form', [
                    'room' => $room,
                    'categories' => $categories,
                    'prizes' => $prizes,
                    'submitLabel' => 'Create Room',
                ])
            </form>
        </div>
    </div>
</x-admin-layout>
