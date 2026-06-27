<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Computed;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SquadPlayer extends Model
{
    use HasFactory;

    protected $fillable = [
        'squad_id',
        'ingame_name',
        'ingame_id',
    ];

    public function squad(): BelongsTo
    {
        return $this->belongsTo(Squad::class);
    }

    #[Computed]
    protected function displayIdentity(): Attribute
    {
        return Attribute::get(
            fn (): string => "{$this->ingame_name} ({$this->ingame_id})"
        );
    }
}
