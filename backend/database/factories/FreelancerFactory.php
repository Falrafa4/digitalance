<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Freelancer>
 */
class FreelancerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'student_id' => \App\Models\SkomdaStudent::inRandomOrder()->first()->id,
            'bio' => $this->faker->text(),
            'password' => bcrypt('password'), // Default password
            // 'status' => $this->faker->randomElement(['Pending', 'Approved', 'Suspended']),
            'status' => 'Approved', // Set default status to 'Approved' for seeding
        ];
    }
}
