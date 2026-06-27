<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Computed;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoomPrize extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'room_id',
        'position',
        'prize_amount',
    ];

    protected function casts(): array
    {
        return [
            'position' => 'integer',
            'prize_amount' => 'decimal:2',
        ];
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    #[Computed]
    protected function prizeSummary(): Attribute
    {
        return Attribute::get(
            fn (): string => "Position {$this->position}: {$this->prize_amount}"
        );
    }
}
