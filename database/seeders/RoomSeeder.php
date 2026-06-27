<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    public function run(): void
    {
        $moderator = User::query()->firstOrCreate(
            ['phone' => '01700000000'],
            [
                'name' => 'BattleZone Admin',
                'email' => 'admin@battlezone.local',
                'password' => bcrypt('admin1234'),
                'role' => 'moderator',
                'wallet_balance' => 0,
                'is_banned' => false,
                'email_verified_at' => now(),
            ]
        );

        $categories = Category::query()
            ->with(['categoryPrizes' => fn ($query) => $query->orderBy('position')])
            ->get()
            ->keyBy('squad_type');

        if ($categories->count() < 3) {
            return;
        }

        $rooms = [
            ['title' => 'Bermuda Squad Clash #1', 'squad_type' => 'squad', 'hours' => 4, 'status' => 'upcoming', 'locked' => true],
            ['title' => 'Purgatory Duo Rush #1', 'squad_type' => 'duo', 'hours' => 8, 'status' => 'upcoming', 'locked' => true],
            ['title' => 'Kalahari Solo Night #1', 'squad_type' => 'solo', 'hours' => 12, 'status' => 'upcoming', 'locked' => true],
            ['title' => 'Bermuda Squad Clash #2', 'squad_type' => 'squad', 'hours' => 16, 'status' => 'upcoming', 'locked' => true],
            ['title' => 'Purgatory Duo Rush #2', 'squad_type' => 'duo', 'hours' => 20, 'status' => 'upcoming', 'locked' => true],
            ['title' => 'Kalahari Solo Night #2', 'squad_type' => 'solo', 'hours' => -2, 'status' => 'live', 'locked' => false],
            ['title' => 'Bermuda Squad Finals #1', 'squad_type' => 'squad', 'hours' => -6, 'status' => 'finished', 'locked' => false],
            ['title' => 'Purgatory Duo Finals #1', 'squad_type' => 'duo', 'hours' => -10, 'status' => 'finished', 'locked' => false],
            ['title' => 'Kalahari Solo Finals #1', 'squad_type' => 'solo', 'hours' => 24, 'status' => 'upcoming', 'locked' => true],
            ['title' => 'Bermuda Squad Special #1', 'squad_type' => 'squad', 'hours' => 30, 'status' => 'cancelled', 'locked' => true],
        ];

        foreach ($rooms as $roomData) {
            $category = $categories->get($roomData['squad_type']);

            if (! $category) {
                continue;
            }

            $room = Room::updateOrCreate(
                ['title' => $roomData['title']],
                [
                    'category_id' => $category->id,
                    'map' => $category->map,
                    'room_code' => $roomData['locked'] ? null : 'ROOM' . random_int(1000, 9999),
                    'room_password' => $roomData['locked'] ? null : (string) random_int(100000, 999999),
                    'match_time' => now()->addHours($roomData['hours']),
                    'entry_fee' => $category->entry_fee,
                    'total_prize' => $category->categoryPrizes->sum('prize_amount'),
                    'is_room_locked' => $roomData['locked'],
                    'status' => $roomData['status'],
                    'created_by' => $moderator->id,
                ]
            );

            $room->roomPrizes()->delete();
            $room->roomPrizes()->createMany(
                $category->categoryPrizes
                    ->map(fn ($prize) => [
                        'position' => $prize->position,
                        'prize_amount' => $prize->prize_amount,
                    ])
                    ->all()
            );
        }
    }
}
