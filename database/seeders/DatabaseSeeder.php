<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'is_admin' => 1,
        ]);

        User::factory()->create([
            'name' => 'Darko',
            'email' => 'darko@example.com',
            'password' => Hash::make('password'),
            'type' => 'basic',
        ]);

        User::factory()->create([
            'name' => 'Marko',
            'email' => 'marko@example.com',
            'password' => Hash::make('password'),
            'type' => 'premium',
        ]);

        $this->call(
            [
                ScheduleSeeder::class,
            ]
        );
    }
}
