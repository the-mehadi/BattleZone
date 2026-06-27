<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Rafi Gamer',
                'phone' => '01710000001',
                'email' => 'rafi@battlezone.local',
                'wallet_balance' => 150.00,
            ],
            [
                'name' => 'Nadim FF',
                'phone' => '01710000002',
                'email' => 'nadim@battlezone.local',
                'wallet_balance' => 120.00,
            ],
            [
                'name' => 'Hasan Rush',
                'phone' => '01710000003',
                'email' => 'hasan@battlezone.local',
                'wallet_balance' => 90.00,
            ],
            [
                'name' => 'Siam Leader',
                'phone' => '01710000004',
                'email' => 'siam@battlezone.local',
                'wallet_balance' => 200.00,
            ],
            [
                'name' => 'Arif Sniper',
                'phone' => '01710000005',
                'email' => 'arif@battlezone.local',
                'wallet_balance' => 80.00,
            ],
        ];

        foreach ($users as $data) {
            User::updateOrCreate(
                ['phone' => $data['phone']],
                [
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'password' => Hash::make('password'),
                    'role' => 'player',
                    'wallet_balance' => $data['wallet_balance'],
                    'is_banned' => false,
                    'email_verified_at' => now(),
                ]
            );
        }
    }
}
