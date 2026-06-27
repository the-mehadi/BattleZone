<?php

namespace Database\Seeders;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::updateOrCreate([
            'phone' => '01700000000',
        ], [
            'name' => 'BattleZone Admin',
            'email' => 'admin@battlezone.local',
            'password' => Hash::make('admin1234'),
            'role' => 'moderator',
            'wallet_balance' => 0,
            'is_banned' => false,
        ]);

        foreach (['bkash_number', 'nagad_number', 'rocket_number'] as $key) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => '']
            );
        }

        $this->call([
            CategorySeeder::class,
            UserSeeder::class,
            RoomSeeder::class,
        ]);
    }
}
