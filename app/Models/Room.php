<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Computed;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'title',
        'map',
        'room_code',
        'room_password',
        'match_time',
        'entry_fee',
        'total_prize',
        'kill_prize_enabled',
        'kill_prize_per_kill',
        'max_squads',
        'is_room_locked',
        'status',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'match_time' => 'datetime',
            'entry_fee' => 'decimal:2',
            'total_prize' => 'decimal:2',
            'kill_prize_enabled' => 'boolean',
            'kill_prize_per_kill' => 'decimal:2',
            'max_squads' => 'integer',
            'is_room_locked' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function squads(): HasMany
    {
        return $this->hasMany(Squad::class);
    }

    public function roomPrizes(): HasMany
    {
        return $this->hasMany(RoomPrize::class);
    }

    public function results(): HasMany
    {
        return $this->hasMany(Result::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->where('status', 'upcoming');
    }

    public function scopeLive(Builder $query): Builder
    {
        return $query->where('status', 'live');
    }

    public function scopeFinished(Builder $query): Builder
    {
        return $query->where('status', 'finished');
    }

    #[Computed]
    protected function displayTitle(): Attribute
    {
        return Attribute::get(
            fn (): string => "{$this->title} - {$this->map}"
        );
    }

    #[Computed]
    protected function joinedSquadsCount(): Attribute
    {
        return Attribute::get(function (): int {
            if ($this->relationLoaded('squads')) {
                return $this->squads
                    ->whereIn('status', ['pending', 'approved'])
                    ->count();
            }

            return $this->squads()
                ->whereIn('status', ['pending', 'approved'])
                ->count();
        });
    }

    #[Computed]
    protected function availableSlots(): Attribute
    {
        return Attribute::get(
            fn (): int => max(0, (int) $this->max_squads - (int) $this->joined_squads_count)
        );
    }

    #[Computed]
    protected function slotProgressPercentage(): Attribute
    {
        return Attribute::get(function (): float {
            if ((int) $this->max_squads <= 0) {
                return 0;
            }

            return min(100, round(((int) $this->joined_squads_count / (int) $this->max_squads) * 100, 2));
        });
    }

    public function isFull(): bool
    {
        return (int) $this->joined_squads_count >= (int) $this->max_squads;
    }
}
