<?php

namespace Database\Seeders;

use App\Models\Schedule;
use Illuminate\Database\Seeder;

class ScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schedule::create([
            'type' => 'onetime',
        ]);
        Schedule::create([
            'type' => 'daily',
        ]);
        Schedule::create([
            'type' => 'weekly',
        ]);
        Schedule::create([
            'type' => 'monthly',
        ]);
    }
}
