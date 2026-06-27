<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class WalletService
{
    public function deduct(User $user, float $amount, string $description, ?int $referenceId = null): bool
    {
        if ($amount <= 0) {
            throw new InvalidArgumentException('Deduction amount must be greater than zero.');
        }

        return DB::transaction(function () use ($user, $amount, $description, $referenceId): bool {
            $account = User::query()->lockForUpdate()->findOrFail($user->getKey());

            if ((float) $account->wallet_balance < $amount) {
                return false;
            }

            $newBalance = round((float) $account->wallet_balance - $amount, 2);

            $account->forceFill([
                'wallet_balance' => $newBalance,
            ])->save();

            $account->transactions()->create([
                'type' => $this->resolveDebitType($description),
                'amount' => $amount,
                'balance_after' => $newBalance,
                'description' => $description,
                'reference_id' => $referenceId,
            ]);

            $user->forceFill([
                'wallet_balance' => $account->wallet_balance,
            ]);

            return true;
        });
    }

    public function credit(User $user, float $amount, string $description, ?int $referenceId = null): void
    {
        if ($amount <= 0) {
            throw new InvalidArgumentException('Credit amount must be greater than zero.');
        }

        DB::transaction(function () use ($user, $amount, $description, $referenceId): void {
            $account = User::query()->lockForUpdate()->findOrFail($user->getKey());
            $newBalance = round((float) $account->wallet_balance + $amount, 2);

            $account->forceFill([
                'wallet_balance' => $newBalance,
            ])->save();

            $account->transactions()->create([
                'type' => $this->resolveCreditType($description),
                'amount' => $amount,
                'balance_after' => $newBalance,
                'description' => $description,
                'reference_id' => $referenceId,
            ]);

            $user->forceFill([
                'wallet_balance' => $account->wallet_balance,
            ]);
        });
    }

    protected function resolveDebitType(string $description): string
    {
        return str_contains(strtolower($description), 'adjust')
            ? 'adjustment'
            : 'entry_fee';
    }

    protected function resolveCreditType(string $description): string
    {
        $description = strtolower($description);

        if (str_contains($description, 'prize')) {
            return 'prize';
        }

        if (str_contains($description, 'refund')) {
            return 'refund';
        }

        if (str_contains($description, 'adjust')) {
            return 'adjustment';
        }

        return 'deposit';
    }
}
