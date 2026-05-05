<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = fake()->randomElement(['DP', 'Full', 'Refund']);

        return [
            'order_id' => \App\Models\Order::inRandomOrder()->first()->id,
            'amount' => $type === 'Refund'
                ? fake()->numberBetween(150000, 1800000)
                : fake()->numberBetween(500000, 4500000),
            'type' => $type,
            'status' => fake()->randomElement(['Pending', 'Paid', 'Failed']),
        ];
    }
}
