<?php

namespace App\Http\Controllers;

use App\Models\Result;
use Illuminate\View\View;

class LeaderboardController extends Controller
{
    public function index(): View
    {
        $leaders = Result::query()
            ->with(['room.category', 'squad'])
            ->whereHas('room', fn ($query) => $query->where('status', 'finished'))
            ->orderByDesc('total_point')
            ->orderByDesc('prize_won')
            ->orderBy('position')
            ->limit(20)
            ->get();

        return view('leaderboard.index', compact('leaders'));
    }
}
