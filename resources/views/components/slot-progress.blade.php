@props(['room'])

@php
    $percentage = (float) $room->slot_progress_percentage;
    $isFull = $room->isFull();
    $barColor = match (true) {
        $percentage <= 50 => 'bg-green-500',
        $percentage <= 80 => 'bg-orange-500',
        default => 'bg-red-500',
    };
@endphp

<div class="space-y-2">
    <div class="flex items-center justify-between gap-3 text-xs font-semibold uppercase tracking-[0.18em]">
        <span class="text-slate-400">Slots</span>
        <span class="text-slate-200">{{ $room->joined_squads_count }} / {{ $room->max_squads }} Teams</span>
    </div>

    @if ($isFull)
        <div class="flex items-center justify-between rounded-full border border-red-500/30 bg-red-500/10 px-4 py-2">
            <span class="text-xs font-semibold uppercase tracking-[0.18em] text-red-300">Room Full</span>
            <span class="text-xs font-semibold text-red-200">{{ $room->joined_squads_count }} / {{ $room->max_squads }} Teams</span>
        </div>
    @else
        <div class="h-3 w-full overflow-hidden rounded-full bg-slate-800">
            <div
                class="h-full rounded-full {{ $barColor }} transition-all duration-500 ease-out"
                style="width: {{ $percentage }}%;"
            ></div>
        </div>

        <p class="text-xs text-slate-400">{{ $room->available_slots }} slots remaining</p>
    @endif
</div>
