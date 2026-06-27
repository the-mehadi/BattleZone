<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\RoomController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\Player\SquadController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoomController as PublicRoomController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/rooms', [PublicRoomController::class, 'index'])->name('rooms.index');
Route::get('/rooms/{room}', [PublicRoomController::class, 'show'])->name('rooms.show');
Route::get('/leaderboard', [LeaderboardController::class, 'index'])->name('leaderboard.index');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'not.banned'])->name('dashboard');

Route::middleware(['auth', 'not.banned'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'not.banned', 'player'])->group(function () {
    Route::get('/rooms/{room}/join', [SquadController::class, 'showJoinForm'])->name('rooms.join');
    Route::post('/rooms/{room}/join', [SquadController::class, 'store'])->name('rooms.join.store');
    Route::get('/my-squads', [SquadController::class, 'index'])->name('my-squads.index');
});

Route::prefix('admin')
    ->as('admin.')
    ->middleware(['auth', 'not.banned', 'moderator'])
    ->group(function () {
        Route::redirect('/', '/admin/categories')->name('dashboard');
        Route::patch('categories/{category}/toggle-status', [CategoryController::class, 'toggleStatus'])
            ->name('categories.toggle-status');
        Route::resource('categories', CategoryController::class);
        Route::patch('rooms/{room}/toggle-lock', [RoomController::class, 'toggleLock'])
            ->name('rooms.toggle-lock');
        Route::patch('rooms/{room}/status', [RoomController::class, 'updateStatus'])
            ->name('rooms.update-status');
        Route::patch('rooms/{room}/squads/{squad}/status', [RoomController::class, 'updateSquadStatus'])
            ->name('rooms.squads.update-status');
        Route::resource('rooms', RoomController::class);
    });

require __DIR__.'/auth.php';
