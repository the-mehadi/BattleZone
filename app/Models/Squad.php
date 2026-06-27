<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Computed;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Squad extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id',
        'leader_user_id',
        'squad_name',
        'total_paid',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'total_paid' => 'decimal:2',
        ];
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function leader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'leader_user_id');
    }

    public function squadPlayers(): HasMany
    {
        return $this->hasMany(SquadPlayer::class);
    }

    public function result(): HasOne
    {
        return $this->hasOne(Result::class);
    }

    #[Computed]
    protected function effectiveName(): Attribute
    {
        return Attribute::get(
            fn (): string => $this->squad_name ?: "Squad #{$this->id}"
        );
    }
}
