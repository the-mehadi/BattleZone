<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Result;
use App\Models\Room;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $roomStatusCounts = Room::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $pendingPayments = Payment::query()
            ->where('status', 'pending');

        $recentPendingPayments = Payment::query()
            ->with('user')
            ->where('status', 'pending')
            ->latest()
            ->limit(5)
            ->get();

        $upcomingRooms = Room::query()
            ->with('category')
            ->upcoming()
            ->orderBy('match_time')
            ->limit(3)
            ->get();

        return view('admin.dashboard', [
            'totalPlayers' => User::query()->where('role', 'player')->count(),
            'totalRooms' => Room::query()->count(),
            'roomStatusCounts' => [
                'upcoming' => (int) ($roomStatusCounts['upcoming'] ?? 0),
                'live' => (int) ($roomStatusCounts['live'] ?? 0),
                'finished' => (int) ($roomStatusCounts['finished'] ?? 0),
            ],
            'pendingPaymentsCount' => $pendingPayments->count(),
            'pendingPaymentsAmount' => (float) $pendingPayments->sum('amount'),
            'totalPrizeDistributed' => (float) Result::query()->sum('prize_won'),
            'recentPendingPayments' => $recentPendingPayments,
            'upcomingRooms' => $upcomingRooms,
        ]);
    }
}
