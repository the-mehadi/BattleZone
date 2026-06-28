<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Room;
use App\Models\Squad;
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
        $players = User::query()
            ->where('role', 'player')
            ->orderBy('id')
            ->get();

        if ($categories->count() < 3 || $players->isEmpty()) {
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

        $sampleSquads = [
            'Bermuda Squad Clash #1' => [
                [
                    'leader_phone' => '01710000001',
                    'squad_name' => 'Blaze Wolves',
                    'status' => 'approved',
                    'players' => [
                        ['ingame_name' => 'RafiGamer', 'ingame_id' => 'SG1001'],
                        ['ingame_name' => 'StormNadim', 'ingame_id' => 'SG1002'],
                        ['ingame_name' => 'RushHasan', 'ingame_id' => 'SG1003'],
                        ['ingame_name' => 'SniperArif', 'ingame_id' => 'SG1004'],
                    ],
                ],
                [
                    'leader_phone' => '01710000004',
                    'squad_name' => 'Alpha Drop',
                    'status' => 'pending',
                    'players' => [
                        ['ingame_name' => 'SiamLeader', 'ingame_id' => 'SG1005'],
                        ['ingame_name' => 'NightCobra', 'ingame_id' => 'SG1006'],
                        ['ingame_name' => 'FireMamba', 'ingame_id' => 'SG1007'],
                        ['ingame_name' => 'ZoneHunter', 'ingame_id' => 'SG1008'],
                    ],
                ],
            ],
            'Purgatory Duo Rush #1' => [
                [
                    'leader_phone' => '01710000002',
                    'squad_name' => 'Duo Storm',
                    'status' => 'approved',
                    'players' => [
                        ['ingame_name' => 'NadimFF', 'ingame_id' => 'DU2001'],
                        ['ingame_name' => 'RafiWing', 'ingame_id' => 'DU2002'],
                    ],
                ],
                [
                    'leader_phone' => '01710000005',
                    'squad_name' => 'Shadow Pair',
                    'status' => 'pending',
                    'players' => [
                        ['ingame_name' => 'ArifSniper', 'ingame_id' => 'DU2003'],
                        ['ingame_name' => 'SilentAce', 'ingame_id' => 'DU2004'],
                    ],
                ],
            ],
            'Kalahari Solo Night #1' => [
                [
                    'leader_phone' => '01710000003',
                    'squad_name' => 'Solo Hasan',
                    'status' => 'approved',
                    'players' => [
                        ['ingame_name' => 'HasanRush', 'ingame_id' => 'SO3001'],
                    ],
                ],
                [
                    'leader_phone' => '01710000001',
                    'squad_name' => 'Solo Rafi',
                    'status' => 'pending',
                    'players' => [
                        ['ingame_name' => 'RafiSolo', 'ingame_id' => 'SO3002'],
                    ],
                ],
            ],
        ];

        foreach ($sampleSquads as $roomTitle => $squads) {
            $room = Room::query()->where('title', $roomTitle)->first();

            if (! $room) {
                continue;
            }

            foreach ($squads as $squadData) {
                $leader = $players->firstWhere('phone', $squadData['leader_phone']);

                if (! $leader) {
                    continue;
                }

                $squad = Squad::query()->updateOrCreate(
                    [
                        'room_id' => $room->id,
                        'leader_user_id' => $leader->id,
                    ],
                    [
                        'squad_name' => $squadData['squad_name'],
                        'total_paid' => $room->entry_fee,
                        'status' => $squadData['status'],
                    ]
                );

                $squad->squadPlayers()->delete();
                $squad->squadPlayers()->createMany($squadData['players']);
            }
        }
    }
}
