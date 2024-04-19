<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class ExpenseGroupFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $expense = ['obuca', 'odjeca', 'clanarina', 'hrana', 'pice', 'auto'];
        $budget = [100, 200, 300, 400, 500, null];

        return [
            'name' => fake()->randomElement($expense),
            'group_budget' => fake()->randomElement($budget),
            'account_id' => fake()->numberBetween(1, 10),
        ];
    }
}
