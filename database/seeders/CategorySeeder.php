<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Squad',
                'squad_type' => 'squad',
                'max_players' => 4,
                'map' => 'Bermuda',
                'rules' => '4-player squad room with standard BattleZone tournament rules.',
                'entry_fee' => 40.00,
                'kill_point' => 2.00,
                'match_duration' => 35,
                'status' => 'active',
                'prizes' => [
                    ['position' => 1, 'prize_amount' => 800.00],
                    ['position' => 2, 'prize_amount' => 500.00],
                    ['position' => 3, 'prize_amount' => 300.00],
                ],
            ],
            [
                'name' => 'Duo',
                'squad_type' => 'duo',
                'max_players' => 2,
                'map' => 'Purgatory',
                'rules' => '2-player duo room with placement and kill-based scoring.',
                'entry_fee' => 30.00,
                'kill_point' => 2.50,
                'match_duration' => 30,
                'status' => 'active',
                'prizes' => [
                    ['position' => 1, 'prize_amount' => 500.00],
                    ['position' => 2, 'prize_amount' => 300.00],
                    ['position' => 3, 'prize_amount' => 200.00],
                ],
            ],
            [
                'name' => 'Solo',
                'squad_type' => 'solo',
                'max_players' => 1,
                'map' => 'Kalahari',
                'rules' => 'Single-player challenge room for solo leaderboard grinders.',
                'entry_fee' => 20.00,
                'kill_point' => 3.00,
                'match_duration' => 25,
                'status' => 'active',
                'prizes' => [
                    ['position' => 1, 'prize_amount' => 300.00],
                    ['position' => 2, 'prize_amount' => 180.00],
                    ['position' => 3, 'prize_amount' => 120.00],
                ],
            ],
        ];

        foreach ($categories as $data) {
            $prizes = $data['prizes'];
            unset($data['prizes']);

            $category = Category::updateOrCreate(
                ['name' => $data['name']],
                $data
            );

            $category->categoryPrizes()->delete();
            $category->categoryPrizes()->createMany($prizes);
        }
    }
}
