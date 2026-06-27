<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Computed;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'amount',
        'balance_after',
        'description',
        'reference_id',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'balance_after' => 'decimal:2',
            'reference_id' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    #[Computed]
    protected function signedAmount(): Attribute
    {
        return Attribute::get(function (): string {
            $prefix = in_array($this->type, ['entry_fee'], true) ? '-' : '+';

            if (in_array($this->type, ['refund', 'deposit', 'prize', 'adjustment'], true)) {
                $prefix = '+';
            }

            return $prefix . number_format((float) $this->amount, 2, '.', '');
        });
    }
}
