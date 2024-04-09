<?php

namespace Database\Factories;

use App\Models\User;
use DateTime;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class AccountFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $endOfMonth = new DateTime('2024-04-30');

        $date = fake()->dateTimeBetween('now', $endOfMonth)->format('Y-m-d');

       $basicUser = User::where('type', 'basic')->get()->map(function ($user) {
            return [
                'user_id' => $user->id,
                'balance' => fake()->numberBetween(500,1000),
                'expense_end_date' => null,
                'expense_budget' => null
            ];
        });
       
        $premiumUser = User::where('type', 'premium')->get()->map(function ($user) use ($date) {
            return [
                'user_id' => $user->id,
                'balance' => fake()->numberBetween(500,1000),
                'expense_end_date' => $date,
                'expense_budget' => fake()->randomElement([100, 200, 300, null])
            ];
        });

        return $basicUser->concat($premiumUser)->all();
    }
}
