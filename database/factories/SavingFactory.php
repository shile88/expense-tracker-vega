<?php

namespace Database\Factories;

use DateTime;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class SavingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $goal = [1000, 500, 1500, 2000, null];
        $fixedAmount = [50, 100, 150, 200, null];
        $endOfMonth = new DateTime('2024-04-30');
        $date = fake()->dateTimeBetween('now', $endOfMonth)->format('Y-m-d');

        return [
            'save_goal' => fake()->randomElement($goal),
            'save_end_date' => $date,
            'save_fixed_amount' => fake()->randomElement($fixedAmount),
            'account_id' => fake()->numberBetween(1, 10),
            'schedule_id' => (random_int(1, 2) <= 1) ? random_int(1, 2) : null,
        ];
    }
}
