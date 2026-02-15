<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'service_id' => \App\Models\Service::inRandomOrder()->first()->id,
            'client_id' => \App\Models\Client::inRandomOrder()->first()->id,
            'brief' => $this->faker->paragraph(),
            'status' => $this->faker->randomElement(['Pending', 'Negotiated', 'Paid', 'In Progress', 'Revision', 'Completed', 'Cancelled']),
            'agreed_price' => $this->faker->randomFloat(2, 100000, 1000000),
        ];
    }
}
