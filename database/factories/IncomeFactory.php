<?php

namespace Database\Factories;

use DateTime;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class IncomeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $amount = [100, 200, 300, 400, 500];

        $endOfMonth = new DateTime('2024-04-30');

        $date = fake()->boolean(50) ? null : fake()->dateTimeBetween('now', $endOfMonth)->format('Y-m-d');

        return [
           'income_group_id' => fake()->numberBetween(1,10),
           'amount' => $amount[array_rand($amount)],
           'schedule_id' => (random_int(1, 2) <= 1) ? random_int(1, 2) : null,
           'income_date' => $date
        ];
    }
}
