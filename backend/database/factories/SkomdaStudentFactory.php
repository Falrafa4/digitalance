<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SkomdaStudent>
 */
class SkomdaStudentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nis' => $this->faker->unique()->numerify('#########'),
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'class' => $this->faker->randomElement(['X', 'XI', 'XII']),
            'major' => $this->faker->randomElement(['TJAT', 'SIJA']),
        ];
    }
}
