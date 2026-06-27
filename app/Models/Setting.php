<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Computed;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
    ];

    #[Computed]
    protected function decodedValue(): Attribute
    {
        return Attribute::get(function (): mixed {
            $decoded = json_decode($this->value, true);

            return json_last_error() === JSON_ERROR_NONE ? $decoded : $this->value;
        });
    }
}
