<x-admin-layout title="Edit Room">
    <div class="mx-auto max-w-6xl">
        <div class="rounded-3xl border border-slate-800 bg-slate-900/80 p-6 shadow-2xl shadow-slate-950/30 md:p-8">
            <div class="mb-8">
                <h2 class="text-2xl font-semibold text-white">Edit Tournament Room</h2>
                <p class="mt-2 text-sm text-slate-400">Update room timing, secure codes, and the reward structure for this match.</p>
            </div>

            <form method="POST" action="{{ route('admin.rooms.update', $room) }}">
                @csrf
                @method('PUT')

                @include('admin.rooms._form', [
                    'room' => $room,
                    'categories' => $categories,
                    'prizes' => $prizes,
                    'submitLabel' => 'Update Room',
                ])
            </form>
        </div>
    </div>
</x-admin-layout>
