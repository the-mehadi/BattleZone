<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Result;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class LeaderboardController extends Controller
{
    public function index(): View
    {
        $leaders = Result::query()
            ->select([
                'squads.leader_user_id',
                'users.name',
                'users.phone',
                DB::raw('SUM(results.total_kills) as total_kills'),
                DB::raw('SUM(results.total_point) as total_points'),
                DB::raw('SUM(results.prize_won) as total_prize_won'),
            ])
            ->join('squads', 'squads.id', '=', 'results.squad_id')
            ->join('users', 'users.id', '=', 'squads.leader_user_id')
            ->join('rooms', 'rooms.id', '=', 'results.room_id')
            ->where('rooms.status', 'finished')
            ->groupBy('squads.leader_user_id', 'users.name', 'users.phone')
            ->orderByDesc('total_points')
            ->orderByDesc('total_kills')
            ->orderByDesc('total_prize_won')
            ->get();

        $recentResults = Room::query()
            ->finished()
            ->with([
                'category',
                'results' => fn ($query) => $query
                    ->where('position', 1)
                    ->with('squad.leader')
                    ->orderBy('position'),
            ])
            ->whereHas('results', fn ($query) => $query->where('position', 1))
            ->orderByDesc('match_time')
            ->limit(5)
            ->get();

        return view('player.leaderboard', [
            'leaders' => $leaders,
            'topLeaders' => $leaders->take(3),
            'otherLeaders' => $leaders->slice(3)->values(),
            'recentResults' => $recentResults,
        ]);
    }
}
