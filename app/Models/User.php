<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Computed;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use InvalidArgumentException;
use RuntimeException;

#[Fillable([
    'name',
    'email',
    'phone',
    'role',
    'wallet_balance',
    'is_banned',
    'password',
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'wallet_balance' => 'decimal:2',
            'is_banned' => 'boolean',
            'password' => 'hashed',
        ];
    }

    public function squads(): HasMany
    {
        return $this->hasMany(Squad::class, 'leader_user_id');
    }

    public function ledSquads(): HasMany
    {
        return $this->squads();
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function approvedPayments(): HasMany
    {
        return $this->hasMany(Payment::class, 'approved_by');
    }

    public function createdRooms(): HasMany
    {
        return $this->hasMany(Room::class, 'created_by');
    }

    public function deductWallet(float|string|int $amount): self
    {
        $amount = (float) $amount;

        if ($amount <= 0) {
            throw new InvalidArgumentException('Wallet deduction amount must be greater than zero.');
        }

        if ((float) $this->wallet_balance < $amount) {
            throw new RuntimeException('Insufficient wallet balance.');
        }

        $this->wallet_balance = round((float) $this->wallet_balance - $amount, 2);
        $this->save();

        return $this->refresh();
    }

    public function creditWallet(float|string|int $amount): self
    {
        $amount = (float) $amount;

        if ($amount <= 0) {
            throw new InvalidArgumentException('Wallet credit amount must be greater than zero.');
        }

        $this->wallet_balance = round((float) $this->wallet_balance + $amount, 2);
        $this->save();

        return $this->refresh();
    }

    #[Computed]
    protected function walletBalanceFormatted(): Attribute
    {
        return Attribute::get(
            fn (): string => number_format((float) $this->wallet_balance, 2, '.', '')
        );
    }

    #[Computed]
    protected function isModerator(): Attribute
    {
        return Attribute::get(
            fn (): bool => $this->role === 'moderator'
        );
    }

    #[Computed]
    protected function isPlayer(): Attribute
    {
        return Attribute::get(
            fn (): bool => $this->role === 'player'
        );
    }
}
