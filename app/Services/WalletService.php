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
            // #region debug-point D:wallet-deduct-start
            $this->reportDebug('D', 'WalletService@deduct:start', '[DEBUG] Wallet deduct started', [
                'user_id' => $account->id,
                'wallet_balance' => (float) $account->wallet_balance,
                'amount' => $amount,
                'reference_id' => $referenceId,
                'description' => $description,
            ]);
            // #endregion

            if ((float) $account->wallet_balance < $amount) {
                // #region debug-point D:wallet-insufficient
                $this->reportDebug('D', 'WalletService@deduct:insufficient', '[DEBUG] Wallet deduct returned false for insufficient funds', [
                    'user_id' => $account->id,
                    'wallet_balance' => (float) $account->wallet_balance,
                    'amount' => $amount,
                ]);
                // #endregion
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
            // #region debug-point D:wallet-deduct-saved
            $this->reportDebug('D', 'WalletService@deduct:saved', '[DEBUG] Wallet deduction saved successfully', [
                'user_id' => $account->id,
                'new_balance' => $newBalance,
            ]);
            // #endregion

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

    protected function reportDebug(string $hypothesisId, string $location, string $message, array $data = []): void
    {
        $url = 'http://127.0.0.1:7777/event';
        $sessionId = 'join-payment-failure';
        $envPath = base_path('.dbg/join-payment-failure.env');

        if (is_file($envPath)) {
            $envContents = file_get_contents($envPath) ?: '';
            preg_match('/^DEBUG_SERVER_URL=(.+)$/m', $envContents, $urlMatches);
            preg_match('/^DEBUG_SESSION_ID=(.+)$/m', $envContents, $sessionMatches);
            $url = $urlMatches[1] ?? $url;
            $sessionId = $sessionMatches[1] ?? $sessionId;
        }

        $payload = json_encode([
            'sessionId' => $sessionId,
            'runId' => 'pre-fix',
            'hypothesisId' => $hypothesisId,
            'location' => $location,
            'msg' => $message,
            'data' => $data,
            'ts' => (int) round(microtime(true) * 1000),
        ]);

        if ($payload === false) {
            return;
        }

        @file_get_contents($url, false, stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => "Content-Type: application/json\r\n",
                'content' => $payload,
                'ignore_errors' => true,
                'timeout' => 1,
            ],
        ]));
    }
}
