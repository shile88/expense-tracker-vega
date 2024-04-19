<?php

namespace Database\Seeders;

use App\Models\ExpenseGroup;
use Illuminate\Database\Seeder;

class ExpenseGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ExpenseGroup::factory(10)->create();
    }
}
