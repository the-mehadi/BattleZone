<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PlayerController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim($request->string('search')->toString());

        $players = User::query()
            ->where('role', 'player')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($innerQuery) use ($search): void {
                    $innerQuery
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.players.index', [
            'players' => $players,
            'search' => $search,
        ]);
    }

    public function ban(User $user): RedirectResponse
    {
        if ($user->role !== 'player') {
            return redirect()
                ->route('admin.players.index')
                ->with('error', 'Only player accounts can be updated from this page.');
        }

        $user->update([
            'is_banned' => ! $user->is_banned,
        ]);

        return redirect()
            ->route('admin.players.index')
            ->with('success', $user->is_banned ? 'Player has been banned successfully.' : 'Player has been unbanned successfully.');
    }
}
