<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Services\WalletService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function __construct(protected WalletService $walletService)
    {
    }

    public function index(Request $request): View
    {
        $status = $request->string('status')->toString();

        $payments = Payment::query()
            ->with(['user', 'approvedBy'])
            ->when(
                in_array($status, ['pending', 'approved', 'rejected'], true),
                fn ($query) => $query->where('status', $status)
            )
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('admin.payments.index', [
            'payments' => $payments,
            'status' => $status,
        ]);
    }

    public function approve(Payment $payment): RedirectResponse
    {
        if ($payment->status !== 'pending') {
            return redirect()
                ->route('admin.payments.index')
                ->with('error', 'Only pending payments can be approved.');
        }

        DB::transaction(function () use ($payment): void {
            $payment = Payment::query()
                ->lockForUpdate()
                ->with('user')
                ->findOrFail($payment->id);

            if ($payment->status !== 'pending') {
                return;
            }

            $payment->update([
                'status' => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
                'note' => null,
            ]);

            $this->walletService->credit(
                $payment->user,
                (float) $payment->amount,
                sprintf('Deposit approved via %s', strtoupper($payment->method)),
                $payment->id
            );
        });

        return redirect()
            ->route('admin.payments.index')
            ->with('success', 'Payment approved and wallet credited successfully.');
    }

    public function reject(Request $request, Payment $payment): RedirectResponse
    {
        $validated = $request->validate([
            'note' => ['nullable', 'string'],
        ]);

        if ($payment->status !== 'pending') {
            return redirect()
                ->route('admin.payments.index')
                ->with('error', 'Only pending payments can be rejected.');
        }

        $payment->update([
            'status' => 'rejected',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'note' => $validated['note'] ?? null,
        ]);

        return redirect()
            ->route('admin.payments.index')
            ->with('success', 'Payment rejected successfully.');
    }
}
