<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class IncomeGroupFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $income = ['plata', 'prodaja', 'renta', 'pozajmica'];

        return [
            'name' => fake()->randomElement($income),
            'account_id' => fake()->numberBetween(1,10)
        ];
    }
}
