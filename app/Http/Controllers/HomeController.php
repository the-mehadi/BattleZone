<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $upcomingRooms = Room::query()
            ->with('category')
            ->upcoming()
            ->where('match_time', '>', now())
            ->orderBy('match_time')
            ->limit(3)
            ->get();

        return view('home', compact('upcomingRooms'));
    }
}
