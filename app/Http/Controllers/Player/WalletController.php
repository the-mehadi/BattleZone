<?php

namespace App\Http\Controllers\Player;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class WalletController extends Controller
{
    public function index(): View
    {
        $transactions = auth()->user()
            ->transactions()
            ->latest()
            ->paginate(12);

        return view('player.wallet.index', [
            'transactions' => $transactions,
        ]);
    }

    public function deposit(): View
    {
        $paymentNumbers = Setting::query()
            ->whereIn('key', ['bkash_number', 'nagad_number', 'rocket_number'])
            ->pluck('value', 'key');

        return view('player.wallet.deposit', [
            'paymentNumbers' => [
                'bkash' => $paymentNumbers->get('bkash_number', ''),
                'nagad' => $paymentNumbers->get('nagad_number', ''),
                'rocket' => $paymentNumbers->get('rocket_number', ''),
            ],
        ]);
    }

    public function storeDeposit(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'method' => ['required', Rule::in(['bkash', 'nagad', 'rocket'])],
            'sender_number' => ['required', 'string'],
            'transaction_id' => ['required', 'string', 'unique:payments,transaction_id'],
            'amount' => ['required', 'numeric', 'min:50'],
        ]);

        $request->user()->payments()->create([
            'method' => $validated['method'],
            'sender_number' => $validated['sender_number'],
            'transaction_id' => $validated['transaction_id'],
            'amount' => $validated['amount'],
            'status' => 'pending',
        ]);

        return redirect()
            ->route('wallet.index')
            ->with('success', 'Deposit request submitted successfully. Your balance will be added after moderator verification.');
    }
}
