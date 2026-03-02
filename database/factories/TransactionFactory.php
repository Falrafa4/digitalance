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
        return [
            'order_id' => \App\Models\Order::inRandomOrder()->first()->id,
            'amount' => $this->faker->randomFloat(2, 100000, 1000000),
            'type' => $this->faker->randomElement(['DP', 'Full', 'Refund']),
            'status' => $this->faker->randomElement(['Pending', 'Paid', 'Failed']),
        ];
    }
}
