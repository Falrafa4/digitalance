<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Service>
 */
class ServiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'category_id' => \App\Models\ServiceCategory::inRandomOrder()->first()->id,
            'freelancer_id' => \App\Models\Freelancer::inRandomOrder()->first()->id,
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'price_min' => $this->faker->numberBetween(100000, 500000),
            'price_max' => $this->faker->numberBetween(500001, 1000000),
            'delivery_time' => $this->faker->numberBetween(1, 14), // Delivery time in days
            'status' => 'Approved', // Default status for seeding
        ];
    }
}
