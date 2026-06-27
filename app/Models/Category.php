<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Computed;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'squad_type',
        'max_players',
        'map',
        'rules',
        'entry_fee',
        'kill_point',
        'match_duration',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'entry_fee' => 'decimal:2',
            'kill_point' => 'decimal:2',
            'match_duration' => 'integer',
            'max_players' => 'integer',
        ];
    }

    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }

    public function categoryPrizes(): HasMany
    {
        return $this->hasMany(CategoryPrize::class);
    }

    #[Computed]
    protected function displayName(): Attribute
    {
        return Attribute::get(
            fn (): string => "{$this->name} ({$this->squad_type} - {$this->map})"
        );
    }
}
