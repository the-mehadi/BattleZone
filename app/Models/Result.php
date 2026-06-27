<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Computed;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Result extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id',
        'squad_id',
        'position',
        'total_kills',
        'kill_point',
        'rank_point',
        'total_point',
        'prize_won',
    ];

    protected function casts(): array
    {
        return [
            'position' => 'integer',
            'total_kills' => 'integer',
            'kill_point' => 'decimal:2',
            'rank_point' => 'decimal:2',
            'total_point' => 'decimal:2',
            'prize_won' => 'decimal:2',
        ];
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function squad(): BelongsTo
    {
        return $this->belongsTo(Squad::class);
    }

    #[Computed]
    protected function summary(): Attribute
    {
        return Attribute::get(
            fn (): string => "Position {$this->position} with {$this->total_point} points"
        );
    }
}
